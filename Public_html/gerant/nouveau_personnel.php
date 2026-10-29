<?php
session_start();

if (!isset($_SESSION['id_personnel'])) {
    header('Location: ../login.php');
    exit;
}
if($_SESSION['type_personnel'] != 'gerant'){
    header("Location: ../dashboard.php");
    exit;
}


$type_personnel = isset($_SESSION['type_personnel']) ? strtolower(trim($_SESSION['type_personnel'])) : '';
if ($type_personnel !== 'gerant') {
    die('Accès refusé : seul le gérant peut enregistrer un nouveau personnel.');
}

require_once '../myparam.inc.php';

$roles_valid = ['gerant', 'soignant', 'gerant boutique', 'agent entretient enclos', 'comptable'];
$contrat_types = ['CDI', 'CDD', 'Stage', 'Alternance'];

$errors = [];
$success = '';


    $id_personnel = isset($_POST['id_personnel']) ? intval($_POST['id_personnel']) : 0;
    $nom_personnel = trim($_POST['nom_personnel'] ?? '');
    $prenom_personnel = trim($_POST['prenom_personnel'] ?? '');
    $type_personnel_post = trim($_POST['type_personnel'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $salaire = isset($_POST['salaire']) ? trim($_POST['salaire']) : null;
    $id_gerant = isset($_POST['id_gerant']) && $_POST['id_gerant'] !== '' ? intval($_POST['id_gerant']) : null;

    $id_contrat = isset($_POST['id_contrat']) ? intval($_POST['id_contrat']) : 0;
    $date_contrat = trim($_POST['date_contrat'] ?? '');
    $type_contrat = trim($_POST['type_contrat'] ?? '');

    if ($id_personnel <= 0) {
        $errors[] = 'L\'ID du personnel est obligatoire et doit être un nombre positif.';
    }
    if ($nom_personnel === '') {
        $errors[] = 'Le nom du personnel est requis.';
    }
    if ($prenom_personnel === '') {
        $errors[] = 'Le prénom du personnel est requis.';
    }
    if (!in_array($type_personnel_post, $roles_valid, true)) {
        $errors[] = 'Le rôle saisi n\'est pas valide.';
    }
    if ($mot_de_passe === '') {
        $errors[] = 'Le mot de passe est requis.';
    }

    if (!empty($salaire) && !is_numeric($salaire)) {
        $errors[] = 'Le salaire doit être un nombre.';
    }

    if ($id_contrat <= 0) {
        $errors[] = 'L\'ID du contrat est obligatoire.';
    }
    if ($type_contrat === '' || !in_array($type_contrat, $contrat_types, true)) {
        $errors[] = 'Le type de contrat est invalide.';
    }

    if ($date_contrat !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_contrat)) {
        $errors[] = 'Le format de la date de contrat doit être YYYY-MM-DD.';
    }

    // Vérification d'existence
    if (empty($errors)) {
        $verif_Pers = oci_parse($conn, 'SELECT COUNT(*) AS NB FROM personnel WHERE Id_personnel = :id_personnel');
        oci_bind_by_name($verif_Pers, ':id_personnel', $id_personnel);
        oci_execute($verif_Pers);
        $tab_Pers = oci_fetch_assoc($verif_Pers);
        oci_free_statement($verif_Pers);

        if ($tab_Pers && (int) $tab_Pers['NB'] > 0) {
            $errors[] = 'ID personnel déjà utilisé.';
        }

        $verif_Contrat = oci_parse($conn, 'SELECT COUNT(*) AS NB FROM contrat WHERE Id_contrat = :id_contrat');
        oci_bind_by_name($verif_Contrat, ':id_contrat', $id_contrat);
        oci_execute($verif_Contrat);
        $tab_Contrat = oci_fetch_assoc($verif_Contrat);
        oci_free_statement($verif_Contrat);

        if ($tab_Contrat && (int) $tab_Contrat['NB'] > 0) {
            $errors[] = 'ID contrat déjà utilisé.';
        }
    }

    if (empty($errors)) {
        $hash_mdp = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Insertion personnel
        $reqPers = 'INSERT INTO personnel (Id_personnel, nom_personnel, prenom_personnel, type_personnel, mot_de_passe, salaire, Id_gerant)
                    VALUES (:id_personnel, :nom_personnel, :prenom_personnel, :type_personnel, :mot_de_passe, :salaire, :id_gerant)';
        $stidPers = oci_parse($conn, $reqPers);
        oci_bind_by_name($stidPers, ':id_personnel', $id_personnel);
        oci_bind_by_name($stidPers, ':nom_personnel', $nom_personnel);
        oci_bind_by_name($stidPers, ':prenom_personnel', $prenom_personnel);
        oci_bind_by_name($stidPers, ':type_personnel', $type_personnel_post);
        oci_bind_by_name($stidPers, ':mot_de_passe', $hash_mdp);
        oci_bind_by_name($stidPers, ':salaire', $salaire);
        oci_bind_by_name($stidPers, ':id_gerant', $id_gerant);

        $okPers = oci_execute($stidPers, OCI_COMMIT_ON_SUCCESS);
        if (!$okPers) {
            $e = oci_error($stidPers);
            $errors[] = 'Erreur création du personnel : ' . htmlspecialchars($e['message']);
            oci_free_statement($stidPers);
        } else {
            oci_free_statement($stidPers);

            // Insertion contrat
            if (empty($errors)) {
                $reqContr = 'INSERT INTO contrat (Id_contrat, date_contrat, type_de_contrat, Id_personnel)
                            VALUES (:id_contrat, ' . ($date_contrat === '' ? 'NULL' : 'TO_DATE(:date_contrat, \'YYYY-MM-DD\')') . ', :type_contrat, :id_personnel)';
                $stidContr = oci_parse($conn, $reqContr);
                oci_bind_by_name($stidContr, ':id_contrat', $id_contrat);
                if ($date_contrat !== '') {
                    oci_bind_by_name($stidContr, ':date_contrat', $date_contrat);
                }
                oci_bind_by_name($stidContr, ':type_contrat', $type_contrat);
                oci_bind_by_name($stidContr, ':id_personnel', $id_personnel);

                $okContr = oci_execute($stidContr, OCI_COMMIT_ON_SUCCESS);
                if (!$okContr) {
                    $e = oci_error($stidContr);
                    $errors[] = 'Erreur création du contrat : ' . htmlspecialchars($e['message']);
                    // tenter rollback du personnel (optionnel)
                    $rollback = oci_parse($conn, 'DELETE FROM personnel WHERE Id_personnel = :id_personnel');
                    oci_bind_by_name($rollback, ':id_personnel', $id_personnel);
                    oci_execute($rollback, OCI_COMMIT_ON_SUCCESS);
                    oci_free_statement($rollback);
                } else {
                    oci_free_statement($stidContr);
                    $success = 'Nouveau personnel et contrat créés avec succès.';
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recrutement - Nouveau personnel</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <h2>Zoo'land</h2>
        <a href="../dashboard.php">Accueil</a>
        <a href="../selection_rech.php">Recherche</a>
        <a href="../mdf_mdp.php">Modifier le mot de passe</a>
        <a href="../deconexion.php">Déconnexion</a>
    </aside>

    <main class="content">
        <h1>Recruter un nouveau membre du personnel</h1>

        <?php if (!empty($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="nouveau_personnel.php">
            <fieldset>
                <legend>Informations du personnel</legend>

                <label>ID Personnel*<br><input type="number" name="id_personnel" value="<?= isset($id_personnel) ? htmlspecialchars($id_personnel) : '' ?>" required></label><br>
                <label>Nom*<br><input type="text" name="nom_personnel" value="<?= isset($nom_personnel) ? htmlspecialchars($nom_personnel) : '' ?>" required></label><br>
                <label>Prénom*<br><input type="text" name="prenom_personnel" value="<?= isset($prenom_personnel) ? htmlspecialchars($prenom_personnel) : '' ?>" required></label><br>
                <label>Rôle*<br>
                    <select name="type_personnel" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($roles_valid as $role): ?>
                            <option value="<?= htmlspecialchars($role) ?>" <?= (isset($type_personnel_post) && $type_personnel_post === $role) ? 'selected' : '' ?>><?= htmlspecialchars($role) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label><br>
                <label>Mot de passe*<br><input type="password" name="mot_de_passe" required></label><br>
                <label>Salaire (€)<br><input type="number" step="0.01" name="salaire" value="<?= isset($salaire) ? htmlspecialchars($salaire) : '' ?>"></label><br>
                <label>ID Gérant (optionnel)<br><input type="number" name="id_gerant" value="<?= isset($id_gerant) ? htmlspecialchars($id_gerant) : '' ?>"></label><br>
            </fieldset>

            <fieldset>
                <legend>Contrat</legend>

                <label>ID Contrat*<br><input type="number" name="id_contrat" value="<?= isset($id_contrat) ? htmlspecialchars($id_contrat) : '' ?>" required></label><br>
                <label>Date Contrat (YYYY-MM-DD)<br><input type="date" name="date_contrat" value="<?= isset($date_contrat) ? htmlspecialchars($date_contrat) : '' ?>"></label><br>
                <label>Type Contrat*<br>
                    <select name="type_contrat" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($contrat_types as $ct): ?>
                            <option value="<?= htmlspecialchars($ct) ?>" <?= (isset($type_contrat) && $type_contrat === $ct) ? 'selected' : '' ?>><?= htmlspecialchars($ct) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </fieldset>

            <button type="submit">Enregistrer le nouveau personnel</button>
        </form>
    </main>
</div>
</body>
</html>
