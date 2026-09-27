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

$test = ['success' => '', 'error' => ''];
$animal_a_traiter = '';
$animal_info = null;
$aliments = [];
$nourritures = [];

if (isset($_POST['id_rfid'])) {
    $animal_a_traiter = trim($_POST['id_rfid']);
}

if (isset($_POST['action']) && $_POST['action'] === 'add_aliment') {
$id_rfid = isset($_POST['id_rfid']) ? trim($_POST['id_rfid']) : '';
    $nom_nourriture = isset($_POST['nom_nourriture']) ? trim($_POST['nom_nourriture']) : '';

    if ($id_rfid === '' || $nom_nourriture === '') {
        $test['error'] = 'Id RFID et aliment sont requis pour ajouter un enregistrement.';
    } else {
        $verifAnimal = oci_parse($conn, 'SELECT COUNT(*) AS CPT FROM Animal WHERE Id_rfid = :id_rfid');
        oci_bind_by_name($verifAnimal, ':id_rfid', $id_rfid);
        oci_execute($verifAnimal);
        $tabAnimal = oci_fetch_assoc($verifAnimal);
        oci_free_statement($verifAnimal);

        if (!$tabAnimal || (int) $tabAnimal['CPT'] === 0) {
            $test['error'] = 'Animal introuvable pour Id_rfid = ' . htmlspecialchars($id_rfid);
        } else {

            $verifNourriture = oci_parse($conn, 'SELECT COUNT(*) AS CPT FROM Nourriture WHERE Nom_nourriture = :nom');
            oci_bind_by_name($verifNourriture, ':nom', $nom_nourriture);
            oci_execute($verifNourriture);
            $tabNourriture = oci_fetch_assoc($verifNourriture);
            oci_free_statement($verifNourriture);

            if (!$tabNourriture || (int) $tabNourriture['CPT'] === 0) {
                $test['error'] = 'Nourriture introuvable: ' . htmlspecialchars($nom_nourriture);
            } else {

                $verifMange = oci_parse($conn, 'SELECT COUNT(*) AS CPT FROM mange WHERE Id_rfid = :id_rfid AND Nom_nourriture = :nom');
                oci_bind_by_name($verifMange, ':id_rfid', $id_rfid);
                oci_bind_by_name($verifMange, ':nom', $nom_nourriture);
                oci_execute($verifMange);
                $tabMange = oci_fetch_assoc($verifMange);
                oci_free_statement($verifMange);

                if ($tabMange && (int) $tabMange['CPT'] > 0) {
                    $test['error'] = 'Cet animal a déjà cette nourriture enregistrée.';
                } else {
                    $insertMange = oci_parse($conn, 'INSERT INTO mange (Id_rfid, Nom_nourriture) VALUES (:id_rfid, :nom)');
                    oci_bind_by_name($insertMange, ':id_rfid', $id_rfid);
                    oci_bind_by_name($insertMange, ':nom', $nom_nourriture);
                    $ok = oci_execute($insertMange, OCI_COMMIT_ON_SUCCESS);
                    if (!$ok) {
                        $e = oci_error($insertMange);
                        $test['error'] = 'Erreur insertion mange : ' . htmlspecialchars($e['message']);
                    } else {
                        $test['success'] = 'Aliment ajouté pour l’animal.';
                        $animal_a_traiter = (string)$id_rfid;
                    }
                    oci_free_statement($insertMange);
                }
            }
        }
    }
}



$req_nourritures = oci_parse($conn, 'SELECT Nom_nourriture FROM Nourriture ORDER BY Nom_nourriture');
oci_execute($req_nourritures);
while ($tab = oci_fetch_assoc($req_nourritures)) {
    $nourritures[] = $tab;
}
oci_free_statement($req_nourritures);

if ($animal_a_traiter !== '') {
    $sql_animal = 'SELECT Id_rfid, Nom_animal, Date_naissance, Poids, Nom_espece FROM Animal WHERE Id_rfid = :id_rfid';
    $req_animal = oci_parse($conn, $sql_animal);
    oci_bind_by_name($req_animal, ':id_rfid', $animal_a_traiter);
    oci_execute($req_animal);
    $animal_info = oci_fetch_assoc($req_animal);
    oci_free_statement($req_animal);

    if (!$animal_info) {
        $test['error'] = 'Aucun animal trouvé pour Id_rfid = ' . htmlspecialchars($animal_a_traiter);
    } else {
        $sql_aliments = 'SELECT m.Id_rfid, n.Nom_nourriture, n.Dose_nourriture, n.Date_expiration
                         FROM mange m
                         JOIN Nourriture n ON m.Nom_nourriture = n.Nom_nourriture
                         WHERE m.Id_rfid = :id_rfid
                         ORDER BY n.Nom_nourriture';

        $stid_aliments = oci_parse($conn, $sql_aliments);
        oci_bind_by_name($stid_aliments, ':id_rfid', $animal_a_traiter);
        oci_execute($stid_aliments);

        while ($tab = oci_fetch_assoc($stid_aliments)) {
            $aliments[] = $tab;
        }

        oci_free_statement($stid_aliments);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique alimentation</title>
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
        <h1>Historique alimentation (par animal)</h1>

        <?php if ($test['success']): ?>
            <div class="success"><?= htmlspecialchars($test['success']) ?></div>
        <?php endif; ?>
        <?php if ($test['error']): ?>
            <div class="error"><?= htmlspecialchars($test['error']) ?></div>
        <?php endif; ?>

        <form method="post" action="alimentation.php" style="margin-bottom:20px;">
            <label for="id_rfid">Id animal (Id_rfid)</label>
            <input type="number" name="id_rfid" id="id_rfid" value="<?= htmlspecialchars($animal_a_traiter) ?>" required>
            <button type="submit">Voir l'historique</button>
        </form>

        <?php if ($animal_a_traiter !== ''): ?>
            <?php if ($animal_info): ?>
                <h2>Animal #<?= htmlspecialchars($animal_info['ID_RFID']) ?> - <?= htmlspecialchars($animal_info['NOM_ANIMAL']) ?></h2>
                <p>Espèce: <?= htmlspecialchars($animal_info['NOM_ESPECE']) ?>, Date de naissance: <?= htmlspecialchars($animal_info['DATE_NAISSANCE']) ?>, Poids: <?= htmlspecialchars($animal_info['POIDS']) ?></p>

                <?php if (empty($aliments)): ?>
                    <p>Aucun aliment trouvé pour cet animal.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Id RFID</th>
                            <th>Nom nourriture</th>
                            <th>Dose</th>
                            <th>Date expiration</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($aliments as $a): ?>
                            <tr>
                                <td><?= htmlspecialchars($a['ID_RFID']) ?></td>
                                <td><?= htmlspecialchars($a['NOM_NOURRITURE']) ?></td>
                                <td><?= htmlspecialchars($a['DOSE_NOURRITURE']) ?></td>
                                <td><?= htmlspecialchars($a['DATE_EXPIRATION']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <section style="margin-top: 30px;">
                    <h2>Ajouter une nourriture à l'animal</h2>
                    <form method="post" action="alimentation.php">
                        <input type="hidden" name="action" value="add_aliment">
                        <div>
                            <label for="id_rfid_add">Id animal (Id_rfid)</label>
                            <input type="number" name="id_rfid" id="id_rfid_add" required value="<?= htmlspecialchars($animal_a_traiter) ?>">
                        </div>
                        <div>
                            <label for="nom_nourriture">Nourriture</label>
                            <select name="nom_nourriture" id="nom_nourriture" required>
                                <option value="">-- Choisir --</option>
                                <?php foreach ($nourritures as $n): ?>
                                    <option value="<?= htmlspecialchars($n['NOM_NOURRITURE']) ?>"><?= htmlspecialchars($n['NOM_NOURRITURE']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit">Ajouter l'aliment</button>
                    </form>
                </section>

            <?php endif; ?>
        <?php endif; ?>

    </main>
</div>
</body>
</html>