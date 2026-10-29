<?php
session_start();

if (!isset($_SESSION['id_personnel'])) {
    header("Location: ../login.php");
    exit;
}
if($_SESSION['type_personnel'] != 'gerant'){
    header("Location: ../dashboard.php");
    exit;
}

require_once '../myparam.inc.php';

if (!isset($conn) || !$conn) {
    $e = oci_error();
    die("Erreur de connexion Oracle : " . htmlspecialchars($e['message']));
}

if($_SESSION['id_personnel'] == '1' ){
    $req = 'SELECT Id_personnel, nom_personnel, prenom_personnel, type_personnel, salaire FROM personnel  ORDER BY nom_personnel';
}
else $req = 'SELECT Id_personnel, nom_personnel, prenom_personnel, type_personnel, salaire FROM personnel where id_personnel != 1 ORDER BY nom_personnel';



$stid = oci_parse($conn, $req);
if (!$stid) {
    $e = oci_error($conn);
    die('Erreur préparation requête : ' . htmlspecialchars($e['message']));
}

oci_execute($stid);

$personnel = [];
while ($tab = oci_fetch_assoc($stid)) {
    $personnel[] = $tab;
}

oci_free_statement($stid);

$roles = ['gerant', 'soignant', 'gerant boutique', 'agent entretient enclos', 'comptable'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du personnel</title>
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
        <h1>Gestion du personnel</h1>

        <?php if (empty($personnel)): ?>
            <p>Aucun membre du personnel enregistré.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Rôle</th>
                        <th>Salaire</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personnel as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['ID_PERSONNEL']) ?></td>
                            <td><?= htmlspecialchars($p['NOM_PERSONNEL']) ?></td>
                            <td><?= htmlspecialchars($p['PRENOM_PERSONNEL']) ?></td>
                            <td><?= htmlspecialchars($p['TYPE_PERSONNEL']) ?></td>
                            <td><?= htmlspecialchars($p['SALAIRE']) ?></td>
                            <td>
                                

                                <form method="get" action="change-role.php" style="display:inline-flex; gap:6px; margin-top:4px;">
                                    <input type="hidden" name="id_personnel" value="<?= htmlspecialchars($p['ID_PERSONNEL']) ?>">
                                    <select name="new_role" required>
                                        <?php foreach ($roles as $role): ?>
                                        <option value="<?= htmlspecialchars($role) ?>" <?= $p['TYPE_PERSONNEL'] === $role ? 'selected' : '' ?>><?= htmlspecialchars($role) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit">Valider</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </main>
</div>
</body>
</html>
