<?php
session_start();

if (!isset($_SESSION['id_personnel'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: selection_rech.php");
    exit;
}

$type_personnel = isset($_SESSION['type_personnel']) ? strtolower(trim($_SESSION['type_personnel'])) : '';

$autorisations = [
    'gerant'                   => ['personnel', 'animal', 'enclos', 'espece', 'boutique', 'ca_journalier', 'contrat', 'zone', 'medicament', 'nouriture'],
    'soignant'                 => ['animal', 'enclos', 'espece', 'medicament', 'nouriture'],
    'gerant boutique'          => ['boutique', 'ca_journalier'],
    'agent entretient enclos'  => ['enclos', 'animal'],
    'comptable'                => ['personnel', 'boutique', 'ca_journalier', 'contrat']
];

$configuration = [
    'personnel' => [
        'label' => 'Personnel',
        'table' => 'personnel',
        'colonnes_affichage' => [
            'ID_PERSONNEL'     => 'ID',
            'NOM_PERSONNEL'    => 'Nom',
            'PRENOM_PERSONNEL' => 'Prénom',
            'TYPE_PERSONNEL'   => 'Type',
            'SALAIRE'          => 'Salaire',
            'ID_GERANT'        => 'ID Gérant'
        ],
        'colonnes_recherche' => ['ID_PERSONNEL', 'NOM_PERSONNEL', 'PRENOM_PERSONNEL', 'TYPE_PERSONNEL', 'SALAIRE', 'ID_GERANT']
    ],
    'animal' => [
        'label' => 'Animal',
        'table' => 'animal',
        'colonnes_affichage' => [
            'ID_RFID'        => 'ID RFID',
            'NOM_ANIMAL'     => 'Nom',
            'DATE_NAISSANCE' => 'Date naissance',
            'POIDS'          => 'Poids (kg)',
            'NOM_ESPECE'     => 'Espèce'
        ],
        'colonnes_recherche' => ['ID_RFID', 'NOM_ANIMAL', 'NOM_ESPECE']
    ],
    'enclos' => [
        'label' => 'Enclos',
        'table' => 'enclos',
        'colonnes_affichage' => [
            'ID_ENCLOS' => 'ID',
            'LONGITUDE' => 'Longitude',
            'LATITUDE'  => 'Latitude',
            'SURFACE'   => 'Surface (m²)'
        ],
        'colonnes_recherche' => ['ID_ENCLOS', 'SURFACE']
    ],
    'espece' => [
        'label' => 'Espèce',
        'table' => 'espece',
        'colonnes_affichage' => [
            'NOM_ESPECE'       => 'Nom espèce',
            'NOM_LATIN_ESPECE' => 'Nom latin',
            'MENACE'           => 'Menacée ?'
        ],
        'colonnes_recherche' => ['NOM_ESPECE', 'NOM_LATIN_ESPECE', 'MENACE']
    ],
    'boutique' => [
        'label' => 'Boutique',
        'table' => 'boutique',
        'colonnes_affichage' => [
            'ID_BOUTIQUE'        => 'ID',
            'ID_GERANT_BOUTIQUE' => 'ID Gérant'
        ],
        'colonnes_recherche' => ['ID_BOUTIQUE', 'ID_GERANT_BOUTIQUE']
    ],
    'ca_journalier' => [
        'label' => 'CA Journalier',
        'table' => 'ca_journalier',
        'colonnes_affichage' => [
            'ID_CA'       => 'ID',
            'MONTANT'     => 'Montant (€)',
            'DATE_CA'     => 'Date',
            'ID_BOUTIQUE' => 'ID Boutique'
        ],
        'colonnes_recherche' => ['ID_CA', 'MONTANT', 'ID_BOUTIQUE']
    ],
    'contrat' => [
        'label' => 'Contrat',
        'table' => 'contrat',
        'colonnes_affichage' => [
            'ID_CONTRAT'   => 'ID',
            'DATE_CONTRAT' => 'Date',
            'TYPE_CONTRAT' => 'Type',
            'ID_PERSONNEL' => 'ID Personnel'
        ],
        'colonnes_recherche' => ['ID_CONTRAT', 'TYPE_CONTRAT', 'ID_PERSONNEL']
    ],
    'zone' => [
        'label' => 'Zone',
        'table' => 'zone',
        'colonnes_affichage' => [
            'NOM_ZONE' => 'Nom zone'
        ],
        'colonnes_recherche' => ['NOM_ZONE']
    ],
    'medicament' => [
        'label' => 'Médicament',
        'table' => 'medicament',
        'colonnes_affichage' => [
            'ID_MEDICATION' => 'ID',
            'TYPE_SOIN'     => 'Type de soin',
            'DOSE_SOIN'     => 'Dose'
        ],
        'colonnes_recherche' => ['ID_MEDICATION', 'TYPE_SOIN']
    ],
    'nouriture' => [
        'label' => 'Nourriture',
        'table' => 'nouriture',
        'colonnes_affichage' => [
            'NOM_NOURRITURE'  => 'Nom',
            'DOSE_NOURRITURE' => 'Dose',
            'DATE_EXPIRATION' => 'Date expiration'
        ],
        'colonnes_recherche' => ['NOM_NOURRITURE']
    ]
];

$entites_autorisees = $autorisations[$type_personnel] ?? [];
$entite = isset($_POST['entite']) ? strtolower(trim($_POST['entite'])) : '';
$valeur = isset($_POST['valeur']) ? trim($_POST['valeur']) : '';

$erreur             = '';
$resultats          = [];
$colonnes_affichage = [];
$label_entite       = '';

if (empty($entite)) {
    $erreur = "Aucune entité sélectionnée.";
} elseif (!in_array($entite, $entites_autorisees, true)) {
    $erreur = "Vous n'avez pas accès à cette entité.";
} elseif (!isset($configuration[$entite])) {
    $erreur = "Entité inconnue.";
}

if ($erreur === '') {
    $config             = $configuration[$entite];
    $table              = $config['table'];
    $colonnes_affichage = $config['colonnes_affichage'];
    $colonnes_recherche = $config['colonnes_recherche'];
    $label_entite       = $config['label'];

    include_once("myparam.inc.php");

    $conn = oci_connect(MYUSER, MYPASS, MYHOST, 'AL32UTF8');
    if (!$conn) {
        $e = oci_error();
        die("Erreur de connexion Oracle : " . htmlentities($e['message'], ENT_QUOTES));
    }

    $liste_colonnes = implode(", ", array_keys($colonnes_affichage));
    $sql = "SELECT $liste_colonnes FROM $table";

    if ($valeur !== '') {
        $conditions = [];
        foreach ($colonnes_recherche as $col) {
            $conditions[] = "UPPER(TO_CHAR($col)) LIKE UPPER(:valeur)";
        }
        $sql .= " WHERE " . implode(" OR ", $conditions);
    }

    $stid = oci_parse($conn, $sql);

    if (!$stid) {
        $e = oci_error($conn);
        $erreur = "Erreur de préparation SQL : " . htmlentities($e['message'], ENT_QUOTES);
    } else {
        if ($valeur !== '') {
            $recherche = "%" . $valeur . "%";
            oci_bind_by_name($stid, ":valeur", $recherche);
        }

        $r = oci_execute($stid);

        if (!$r) {
            $e = oci_error($stid);
            $erreur = "Erreur d'exécution SQL : " . htmlentities($e['message'], ENT_QUOTES);
        } else {
            while (($row = oci_fetch_assoc($stid)) !== false) {
                $resultats[] = $row;
            }
        }

        oci_free_statement($stid);
    }

    oci_close($conn);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats - Zoo'land</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="layout">

    <aside class="sidebar">
        <h2>Zoo'land</h2>
        <a href="dashboard.php"> Accueil</a>
        <a href="selection_rech.php"> Recherche</a>
        <a href="mdf_mdp.php"> Modifier le mot de passe</a>
        <a href="deconexion.php"> Déconnexion</a>
    </aside>

    <main class="content">

        <a class="btn-retour" href="selection_rech.php">← Nouvelle recherche</a>

        <?php if ($erreur !== ''): ?>
            <div class="card">
                <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
            </div>

        <?php else: ?>
            <div class="card">
                <h1>Résultats — <?= htmlspecialchars($label_entite) ?></h1>

                <p class="compteur">
                    <?php if ($valeur !== ''): ?>
                        Recherche : « <strong><?= htmlspecialchars($valeur) ?></strong> » —
                    <?php else: ?>
                        Affichage complet —
                    <?php endif; ?>
                    <span><?= count($resultats) ?></span>
                    enregistrement<?= count($resultats) > 1 ? 's' : '' ?> trouvé<?= count($resultats) > 1 ? 's' : '' ?>
                </p>

                <?php if (count($resultats) > 0): ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <?php foreach ($colonnes_affichage as $col => $label): ?>
                                        <th><?= htmlspecialchars($label) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultats as $ligne): ?>
                                    <tr>
                                        <?php foreach ($colonnes_affichage as $col => $label): ?>
                                            <td>
                                                <?= isset($ligne[$col]) && $ligne[$col] !== null
                                                    ? htmlspecialchars((string)$ligne[$col])
                                                    : '<em style="color:#bbb">—</em>' ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>
                    <div class="aucun-resultat">
                        <span class="icon">🔎</span>
                        Aucun résultat trouvé<?= $valeur !== '' ? ' pour « ' . htmlspecialchars($valeur) . ' »' : '' ?>.
                        <br><br>
                        <a class="btn-retour" href="selection_rech.php">← Modifier la recherche</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>