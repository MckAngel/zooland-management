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


$type_personnel = isset($_SESSION['type_personnel']) ? strtolower(trim($_SESSION['type_personnel'])) : '';
$peut_acceder   = ($type_personnel === 'gerant');

include_once('../myparam.inc.php');

if (!isset($conn) || !$conn) {
    $e = oci_error();
    die("Erreur de connexion Oracle : " . htmlspecialchars($e['message']));
}

$succes_msg = isset($_GET['succes']) ? htmlspecialchars(urldecode($_GET['succes'])) : '';
$erreur_msg = isset($_GET['erreur']) ? htmlspecialchars(urldecode($_GET['erreur'])) : '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $peut_acceder) {

    $action = $_POST['action'] ?? '';


    if ($action === 'creer_parrain') {
        $id  = trim($_POST['Id_parrain']?? '');
        $tel = trim($_POST['Num_telephone_visiteur'] ?? '');

        if ($id === '' || $tel === '') {
            header("Location: parrain.php?onglet=parrain&erreur=" . urlencode("Champs obligatoires manquants."));
            exit;
        }
        if (!ctype_digit($id)) {
            header("Location: parrain.php?onglet=parrain&erreur=" . urlencode("L'ID doit être un entier positif."));
            exit;
        }
        if (!preg_match('/^\d{10}$/', $tel)) {
            header("Location: parrain.php?onglet=parrain&erreur=" . urlencode("Le téléphone doit contenir exactement 10 chiffres."));
            exit;
        }

        $verif = oci_parse($conn, "SELECT COUNT(*) AS NB FROM parrain WHERE Id_parrain = :id");
        oci_bind_by_name($verif, ":id", $id);
        oci_execute($verif);
        $tab = oci_fetch_assoc($verif);
        oci_free_statement($verif);
        if ((int)$tab['NB'] > 0) {
            header("Location: parrain.php?onglet=parrain&erreur=" . urlencode("Un parrain avec l'ID $id existe déjà."));
            exit;
        }

        $stid = oci_parse($conn, "INSERT INTO parrain (Id_parrain, Num_telephone_visiteur) VALUES (:id, :tel)");
        oci_bind_by_name($stid, ":id",  $id);
        oci_bind_by_name($stid, ":tel", $tel);
        $e = oci_execute($stid, OCI_COMMIT_ON_SUCCESS);
        if (!$e) {
            $e = oci_error($stid);
            oci_free_statement($stid);
            oci_close($conn);
            header("Location: parrain.php?onglet=parrain&erreur=" . urlencode("Erreur Oracle : " . $e['message']));
            exit;
        }
        oci_free_statement($stid);
        oci_close($conn);
        header("Location: parrain.php?onglet=parrain&succes=" . urlencode("Parrain ajouté avec succès."));
        exit;
    }

    if ($action === 'creer_parrainage') {
        $idp  = trim($_POST['Id_parrainage']    ?? '');
        $id   = trim($_POST['Id_parrain']       ?? '');
        $rfid = trim($_POST['Id_rfid']          ?? '');
        $niv  = trim($_POST['Niv_contribution'] ?? '');

        if ($idp === '' || $id === '' || $rfid === '' || $niv === '') {
            header("Location: parrain.php?onglet=parrainage&erreur=" . urlencode("Tous les champs sont obligatoires."));
            exit;
        }

        $stid = oci_parse($conn, "INSERT INTO parrainer (Id_parrainage, Id_parrain, Id_rfid, Niv_contribution)
                                   VALUES (:idp, :id, :rfid, :niv)");
        oci_bind_by_name($stid, ":idp",  $idp);
        oci_bind_by_name($stid, ":id",   $id);
        oci_bind_by_name($stid, ":rfid", $rfid);
        oci_bind_by_name($stid, ":niv",  $niv);
        $e = oci_execute($stid, OCI_COMMIT_ON_SUCCESS);
        if (!$e) {
            $e = oci_error($stid);
            oci_free_statement($stid);
            oci_close($conn);
            header("Location: parrain.php?onglet=parrainage&erreur=" . urlencode("Erreur Oracle : " . $e['message']));
            exit;
        }
        oci_free_statement($stid);
        oci_close($conn);
        header("Location: parrain.php?onglet=parrainage&succes=" . urlencode("Parrainage ajouté avec succès."));
        exit;
    }

    if ($action === 'supprimer_parrainage') {
        $idp = trim($_POST['Id_parrainage'] ?? '');

        if ($idp === '') {
            header("Location: parrain.php?onglet=consulter&erreur=" . urlencode("Identifiant manquant."));
            exit;
        }

        $stid = oci_parse($conn, "DELETE FROM parrainer WHERE Id_parrainage = :idp");
        oci_bind_by_name($stid, ":idp", $idp);
        $e = oci_execute($stid, OCI_COMMIT_ON_SUCCESS);
        if (!$e) {
            $e = oci_error($stid);
            oci_free_statement($stid);
            oci_close($conn);
            header("Location: parrain.php?onglet=consulter&erreur=" . urlencode("Erreur Oracle : " . $e['message']));
            exit;
        }
        oci_free_statement($stid);
        oci_close($conn);
        header("Location: parrain.php?onglet=consulter&succes=" . urlencode("Parrainage supprimé avec succès."));
        exit;
    }
}

$parrainages = [];
$stid = oci_parse($conn,
    "SELECT p.Id_parrainage, pr.Id_parrain, pr.Num_telephone_visiteur,
            a.Nom_animal, a.Id_rfid, p.Niv_contribution
     FROM   parrainer p
     JOIN   parrain   pr ON pr.Id_parrain = p.Id_parrain
     JOIN   animal    a  ON a.Id_rfid     = p.Id_rfid
     ORDER  BY p.Id_parrainage");
oci_execute($stid);
while ($tab = oci_fetch_assoc($stid)) { $parrainages[] = $tab; }
oci_free_statement($stid);

$liste_parrains = [];
$stid = oci_parse($conn, "SELECT Id_parrain, Num_telephone_visiteur FROM parrain ORDER BY Id_parrain");
oci_execute($stid);
while ($tab = oci_fetch_assoc($stid)) { $liste_parrains[] = $tab; }
oci_free_statement($stid);

$liste_animaux = [];
$stid = oci_parse($conn, "SELECT Id_rfid, Nom_animal FROM animal ORDER BY Nom_animal");
oci_execute($stid);
while ($tab = oci_fetch_assoc($stid)) { $liste_animaux[] = $tab; }
oci_free_statement($stid);

oci_close($conn);

$onglet = $_GET['onglet'] ?? 'consulter';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Parrains — Zoo'land</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <h2>Zoo'land</h2>
        <a href="../dashboard.php">Accueil</a>
        <a href="../selection_rech.php">Recherche</a>
        <a href="../mdf_mdp.php">Mot de passe</a>
        <a href="../deconexion.php">Déconnexion</a>
    </aside>

    <main class="content">

        <?php if (!$peut_acceder): ?>
            <div class="card">
                <p class="erreur">Accès réservé au gérant.</p>
            </div>

        <?php else: ?>
            <div class="card">
                <h1>Gestion des parrains</h1>

                <?php if ($succes_msg !== ''): ?>
                    <p class="succes"> Réussi <?= $succes_msg ?></p>
                <?php endif; ?>
                <?php if ($erreur_msg !== ''): ?>
                    <p class="erreur"> Échoué <?= $erreur_msg ?></p>
                <?php endif; ?>

                <nav class="tabs">
                    <a href="?onglet=consulter" class="tab-btn <?= $onglet==='consulter'?'active':'' ?>">Liste</a>
                    <a href="?onglet=parrain" class="tab-btn <?= $onglet==='parrain'?'active':'' ?>"> Parrain</a>
                    <a href="?onglet=parrainage" class="tab-btn <?= $onglet==='parrainage'?'active':'' ?>"> Parrainage</a>
                </nav>

                <!-- ── LISTE ───────────────────────────────── -->
                <?php if ($onglet === 'consulter'): ?>

                    <?php if (empty($parrainages)): ?>
                        <div class="aucun-resultat">
                            <span class="icon">aucun</span>
                            Aucun parrainage enregistré.
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Parrain</th>
                                        <th>Téléphone</th>
                                        <th>Animal</th>
                                        <th>Niveau</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($parrainages as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['ID_PARRAINAGE']) ?></td>
                                            <td><?= htmlspecialchars($p['ID_PARRAIN']) ?></td>
                                            <td><?= htmlspecialchars($p['NUM_TELEPHONE_VISITEUR']) ?></td>
                                            <td><?= htmlspecialchars($p['NOM_ANIMAL']) ?></td>
                                            <td><?= htmlspecialchars($p['NIV_CONTRIBUTION']) ?></td>
                                            <td>
                                                <form method="post" action="parrain.php">
                                                    <input type="hidden" name="action"        value="supprimer_parrainage">
                                                    <input type="hidden" name="Id_parrainage" value="<?= htmlspecialchars($p['ID_PARRAINAGE']) ?>">
                                                    <button class="btn-danger" type="submit"
                                                            onclick="return confirm('Supprimer ce parrainage ?')">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>


                <?php elseif ($onglet === 'parrain'): ?>

                    <form method="post" action="parrain.php">
                        <input type="hidden" name="action" value="creer_parrain">
                        <div class="parrain-form-row">
                            <div class="form-group">
                                <label for="Id_parrain">ID Parrain <span class="required-star">*</span></label>
                                <input type="number" name="Id_parrain" id="Id_parrain" placeholder="Ex : 10" required>
                            </div>
                            <div class="form-group">
                                <label for="Num_telephone_visiteur">Téléphone <span class="required-star">*</span></label>
                                <input type="tel" name="Num_telephone_visiteur" id="Num_telephone_visiteur"
                                       maxlength="10" pattern="\d{10}" placeholder="0612345678" required>
                                <p class="hint">10 chiffres, sans espace ni tiret.</p>
                            </div>
                        </div>
                        <button class="btn-submit" type="submit"> Ajouter</button>
                    </form>

                <?php elseif ($onglet === 'parrainage'): ?>

                    <form method="post" action="parrain.php">
                        <input type="hidden" name="action" value="creer_parrainage">
                        <div class="form-group">
                            <label for="Id_parrainage">ID Parrainage <span class="required-star">*</span></label>
                            <input type="number" name="Id_parrainage" id="Id_parrainage" placeholder="Identifiant unique" required>
                        </div>
                        <div class="parrain-form-row">
                            <div class="form-group">
                                <label for="Id_parrain">Parrain <span class="required-star">*</span></label>
                                <select name="Id_parrain" id="Id_parrain" required>
                                    <option value="">-- Choisir --</option>
                                    <?php foreach ($liste_parrains as $p): ?>
                                        <option value="<?= htmlspecialchars($p['ID_PARRAIN']) ?>">
                                            Parrain #<?= htmlspecialchars($p['ID_PARRAIN']) ?>
                                            — <?= htmlspecialchars($p['NUM_TELEPHONE_VISITEUR']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="Id_rfid">Animal <span class="required-star">*</span></label>
                                <select name="Id_rfid" id="Id_rfid" required>
                                    <option value="">-- Choisir --</option>
                                    <?php foreach ($liste_animaux as $a): ?>
                                        <option value="<?= htmlspecialchars($a['ID_RFID']) ?>">
                                            <?= htmlspecialchars($a['NOM_ANIMAL']) ?>
                                            — RFID <?= htmlspecialchars($a['ID_RFID']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Niv_contribution">Niveau de contribution <span class="required-star">*</span></label>
                            <select name="Niv_contribution" id="Niv_contribution" required>
                                <option value="">-- Choisir --</option>
                                <option value="1">1 — Bronze</option>
                                <option value="2">2 — Argent</option>
                                <option value="3">3 — Or</option>
                                <option value="4">4 — Platine</option>
                                <option value="5">5 — Diamant</option>
                            </select>
                        </div>
                        <button class="btn-submit" type="submit">Créer</button>
                    </form>

                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</div>
</body>
</html>