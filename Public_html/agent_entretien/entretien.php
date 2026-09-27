<?php
session_start();

if (!isset($_SESSION['id_personnel'])) {
    header('Location: login.php');
    exit;
}

require_once '../myparam.inc.php';

if (!isset($conn) || !$conn) {
    $e = oci_error();
    die('Erreur de connexion Oracle : ' . htmlspecialchars($e['message']));
}

$id_agent = (int) $_SESSION['id_personnel'];
$test= ['success' => '', 'error' => ''];

if (isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $id_enclos = isset($_POST['id_enclos'])  ? (int)  trim($_POST['id_enclos'])  : 0;
    $date_interv = isset($_POST['date_interv']) ? trim($_POST['date_interv']) : '';
    $nature = isset($_POST['nature'])      ? trim($_POST['nature'])      : '';

    if ($id_enclos <= 0 || $date_interv === '' || $nature === '') {
        $test['error'] = 'Tous les champs sont obligatoires.';
    } else {
        // Vérifier que l'enclos existe
        $verif = oci_parse($conn, 'SELECT COUNT(*) AS NB FROM Enclos WHERE Id_enclos = :id');
        oci_bind_by_name($verif, ':id', $id_enclos);
        oci_execute($verif);
        $tab = oci_fetch_assoc($verif);
        oci_free_statement($verif);

        if ((int)$tab['NB'] === 0) {
            $test['error'] = "L'enclos n°$id_enclos n'existe pas.";
        } else {
            // Vérifier que la combinaison (personnel, enclos, date) n'existe pas déjà (PK)
            $verif2 = oci_parse($conn,
                "SELECT COUNT(*) AS NB FROM intervient
                 WHERE Id_personnel = :id_p
                   AND Id_enclos = :id_e
                   AND Date_interv  = TO_DATE(:d, 'YYYY-MM-DD')");
            oci_bind_by_name($verif2, ':id_p', $id_agent);
            oci_bind_by_name($verif2, ':id_e', $id_enclos);
            oci_bind_by_name($verif2, ':d', $date_interv);
            oci_execute($verif2);
            $tab2 = oci_fetch_assoc($verif2);
            oci_free_statement($verif2);

            if ((int)$tab2['NB'] > 0) {
                $test['error'] = 'Une intervention de votre part sur cet enclos à cette date existe déjà.';
            } else {
                $ins = oci_parse($conn,
                    "INSERT INTO intervient (Id_personnel, Id_enclos, Date_interv, Nature)
                     VALUES (:id_p, :id_e, TO_DATE(:d, 'YYYY-MM-DD'), :nature)");
                oci_bind_by_name($ins, ':id_p', $id_agent);
                oci_bind_by_name($ins, ':id_e', $id_enclos);
                oci_bind_by_name($ins, ':d', $date_interv);
                oci_bind_by_name($ins, ':nature', $nature);

                if (oci_execute($ins, OCI_COMMIT_ON_SUCCESS)) {
                    $test['success'] = "Intervention ajoutée avec succès sur l'enclos n°$id_enclos.";
                } else {
                    $e = oci_error($ins);
                    $test['error'] = 'Erreur insertion : ' . htmlspecialchars($e['message']);
                }
                oci_free_statement($ins);
            }
        }
    }
}


$filtre_enclos = isset($_GET['filtre_enclos']) && is_numeric($_GET['filtre_enclos'])
    ? (int) $_GET['filtre_enclos']
    : 0;

$historique = [];
$sql_hist = "SELECT i.Id_personnel,
                    p.nom_personnel,
                    p.prenom_personnel,
                    p.type_personnel,
                    i.Id_enclos,
                    TO_CHAR(i.Date_interv, 'DD/MM/YYYY') AS DATE_INTERV,
                    i.Nature
             FROM intervient i
             JOIN personnel  p ON i.Id_personnel = p.Id_personnel"
          . ($filtre_enclos > 0 ? " WHERE i.Id_enclos = :id_e" : "")
          . " ORDER BY i.Date_interv DESC";

$stid = oci_parse($conn, $sql_hist);
if ($filtre_enclos > 0) {
    oci_bind_by_name($stid, ':id_e', $filtre_enclos);
}
oci_execute($stid);
while ($tab = oci_fetch_assoc($stid)) {
    $historique[] = $tab;
}
oci_free_statement($stid);

$enclos_list = [];
$stid2 = oci_parse($conn, 'SELECT Id_enclos, Nom_zone FROM Enclos ORDER BY Id_enclos');
oci_execute($stid2);
while ($tab = oci_fetch_assoc($stid2)) {
    $enclos_list[] = $tab;
}
oci_free_statement($stid2);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entretien des enclos</title>
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
        <h1>Entretien des enclos</h1>

        <?php if ($test['success']): ?>
            <div class="success"><?= htmlspecialchars($test['success']) ?></div>
        <?php endif; ?>
        <?php if ($test['error']): ?>
            <div class="error"><?= htmlspecialchars($test['error']) ?></div>
        <?php endif; ?>


        <!-- ── Formulaire ajout intervention ─────────────────────────── -->
        <section>
            <h2>Déclarer une intervention</h2>
            <form method="post" action="entretien.php">
                <input type="hidden" name="action" value="ajouter">

                <label for="id_enclos">Enclos</label>
                <select name="id_enclos" id="id_enclos" required>
                    <option value="">-- Choisir un enclos --</option>
                    <?php foreach ($enclos_list as $enc): ?>
                        <option value="<?= (int)$enc['ID_ENCLOS'] ?>">
                            Enclos <?= (int)$enc['ID_ENCLOS'] ?>
                            (Zone : <?= htmlspecialchars($enc['NOM_ZONE']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="date_interv">Date de l'intervention</label>
                <input type="date" name="date_interv" id="date_interv"
                       max="<?= date('Y-m-d') ?>" required>

                <label for="nature">Nature de l'intervention</label>
                <input type="text" name="nature" id="nature"
                       placeholder="Ex : nettoyage, réparation clôture…" required>

                <button type="submit" class="btn">Ajouter l'intervention</button>
            </form>
        </section>


        <!-- ── Historique ────────────────────────────────────────────── -->
        <section style="margin-top:2rem;">
            <h2>Historique des interventions</h2>

            <!-- Filtre par enclos -->
            <form method="get" action="entretien.php" style="margin-bottom:1rem;">
                <label for="filtre_enclos">Filtrer par enclos</label>
                <select name="filtre_enclos" id="filtre_enclos">
                    <option value="">-- Tous les enclos --</option>
                    <?php foreach ($enclos_list as $enc): ?>
                        <option value="<?= (int)$enc['ID_ENCLOS'] ?>"
                            <?= $filtre_enclos === (int)$enc['ID_ENCLOS'] ? 'selected' : '' ?>>
                            Enclos <?= (int)$enc['ID_ENCLOS'] ?>
                            (Zone : <?= htmlspecialchars($enc['NOM_ZONE']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn">Filtrer</button>
                <?php if ($filtre_enclos > 0): ?>
                    <a href="entretien.php" class="btn" style="background:#888;">Tout afficher</a>
                <?php endif; ?>
            </form>

            <?php if (empty($historique)): ?>
                <p>Aucune intervention enregistrée<?= $filtre_enclos > 0 ? " pour l'enclos n°$filtre_enclos" : '' ?>.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Enclos</th>
                            <th>Date</th>
                            <th>Nature</th>
                            <th>Intervenant</th>
                            <th>Rôle</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($historique as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['ID_ENCLOS']) ?></td>
                            <td><?= htmlspecialchars($h['DATE_INTERV']) ?></td>
                            <td><?= htmlspecialchars($h['NATURE']) ?></td>
                            <td><?= htmlspecialchars($h['NOM_PERSONNEL'] . ' ' . $h['PRENOM_PERSONNEL']) ?></td>
                            <td><?= htmlspecialchars($h['TYPE_PERSONNEL']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

    </main>
</div>
</body>
</html>