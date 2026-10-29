<?php
session_start();

if (!isset($_SESSION['id_personnel'])) {
    header("Location: ../login.php");
    exit;
}
if($_SESSION['type_personnel'] != 'gerant' && $_SESSION['type_personnel'] != 'gerant boutique' && $_SESSION['type_personnel'] != 'comptable'){
    header("Location: ../dashboard.php");
    exit;
}


require_once '../myparam.inc.php';

$req = "SELECT Id_ca, Montant, TO_CHAR(Date_ca, 'DD/MM/YYYY') AS Date_ca, Id_boutique FROM CA_journalier ORDER BY Date_ca DESC";
$stid = oci_parse($conn, $req);
if (!$stid) {
    $e = oci_error($conn);
    die('Erreur préparation requête : ' . htmlspecialchars($e['message']));
}

oci_execute($stid);

$ca_list = [];
while ($tab = oci_fetch_assoc($stid)) {
    $ca_list[] = $tab;
}

oci_free_statement($stid);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques CA Journalier</title>
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
        <h1>CA Journalier</h1>
        <p>Liste des entrées du journalier pour les ventes par boutique.</p>

        <?php if (count($ca_list) === 0): ?>
            <div class="info">Aucune donnée trouvée dans CA_journalier.</div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Id_ca</th>
                        <th>Montant</th>
                        <th>Date_ca</th>
                        <th>Id_boutique</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ca_list as $ligne): 
                        //permet de s'assurer que Montant est un float valide avant number_format
                        $montant = $ligne['MONTANT'];
                        $montant = str_replace([',', ' '], ['.', ''], $montant); // enleve les virgules et les espaces pour éviter les erreurs de conversion    
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($ligne['ID_CA']) ?></td>
                            <td><?= htmlspecialchars(number_format($montant, 2, ',', ' ')) ?></td>
                            <td><?= htmlspecialchars($ligne['DATE_CA']) ?></td>
                            <td><?= htmlspecialchars($ligne['ID_BOUTIQUE']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </main>
</div>
</body>
</html>