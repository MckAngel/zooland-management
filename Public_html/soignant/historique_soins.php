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

$id_personnel_conn = (int) ($_SESSION['id_personnel'] ?? 0);

$animal_a_traiter = '';
if (isset($_POST['id_rfid'])) {
    $animal_a_traiter = trim($_POST['id_rfid']);
}

$animal_info = null;
$historique = [];

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
        $req_histo = 'SELECT c.Id_rfid,
                           a.Nom_animal,
                           p.Id_personnel,
                           p.nom_personnel,
                           p.prenom_personnel,
                           s.Id_specialite,
                           s.Nom_specialite,
                           m.Id_medication,
                           m.Type_soin,
                           m.Dose_soin
                    FROM consommer c
                    JOIN Animal a ON c.Id_rfid = a.Id_rfid
                    JOIN Medicament m ON c.Id_medication = m.Id_medication
                    JOIN utilise u ON u.Id_medication = c.Id_medication
                    JOIN personnel p ON u.Id_personnel = p.Id_personnel
                    JOIN Specialite s ON u.Id_specialite = s.Id_specialite
                    WHERE c.Id_rfid = :id_rfid
                    ORDER BY c.Id_medication, p.Id_personnel';

        $stidHist = oci_parse($conn, $req_histo);
        oci_bind_by_name($stidHist, ':id_rfid', $animal_a_traiter);
        oci_execute($stidHist);
        while ($tab = oci_fetch_assoc($stidHist)) {
            $historique[] = $tab;
        }
        oci_free_statement($stidHist);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des soins</title>
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
        <h1>Historique des soins (par animal)</h1>

        <?php if ($test['success']): ?>
            <div class="success"><?= htmlspecialchars($test['success']) ?></div>
        <?php endif; ?>
        <?php if ($test['error']): ?>
            <div class="error"><?= htmlspecialchars($test['error']) ?></div>
        <?php endif; ?>

        <form method="post" action="historique_soins.php" style="margin-bottom:20px;">
            <label for="id_rfid">Id animal (Id_rfid)</label>
            <input type="number" name="id_rfid" id="id_rfid" value="<?= htmlspecialchars($animal_a_traiter) ?>" required>
            <button type="submit">Voir l'historique</button>
        </form>

        <?php if ($animal_a_traiter !== ''): ?>
            <?php if ($animal_info): ?>
                <h2>Animal #<?= htmlspecialchars($animal_info['ID_RFID']) ?> - <?= htmlspecialchars($animal_info['NOM_ANIMAL']) ?></h2>
                <p>Espèce: <?= htmlspecialchars($animal_info['NOM_ESPECE']) ?>, Date de naissance: <?= htmlspecialchars($animal_info['DATE_NAISSANCE']) ?>, Poids: <?= htmlspecialchars($animal_info['POIDS']) ?></p>

                <?php if (empty($historique)): ?>
                    <p>Aucun soin trouvé pour cet animal.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Id Personnel</th>
                            <th>Soignant</th>
                            <th>Id Spécialité</th>
                            <th>Spécialité</th>
                            <th>Id Médicament</th>
                            <th>Médicament</th>
                            <th>Dose</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($historique as $h): ?>
                            <tr>
                                <td><?= htmlspecialchars($h['ID_PERSONNEL']) ?></td>
                                <td><?= htmlspecialchars($h['NOM_PERSONNEL'] . ' ' . $h['PRENOM_PERSONNEL']) ?></td>
                                <td><?= htmlspecialchars($h['ID_SPECIALITE']) ?></td>
                                <td><?= htmlspecialchars($h['NOM_SPECIALITE']) ?></td>
                                <td><?= htmlspecialchars($h['ID_MEDICATION']) ?></td>
                                <td><?= htmlspecialchars($h['TYPE_SOIN']) ?></td>
                                <td><?= htmlspecialchars($h['DOSE_SOIN']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

    </main>
</div>
</body>
</html>