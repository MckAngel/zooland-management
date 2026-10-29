<?php
session_start();

if (!isset($_SESSION['id_personnel'])) {
    header("Location: login.php");
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
        'colonnes_affichage' => [
            'ID_PERSONNEL'     => 'ID',
            'NOM_PERSONNEL'    => 'Nom',
            'PRENOM_PERSONNEL' => 'Prénom',
            'TYPE_PERSONNEL'   => 'Type',
            'SALAIRE'          => 'Salaire',
            'ID_GERANT'        => 'ID Gérant'
        ]
    ],
    'animal' => [
        'label' => 'Animal',
        'colonnes_affichage' => [
            'ID_RFID'        => 'ID RFID',
            'NOM_ANIMAL'     => 'Nom',
            'DATE_NAISSANCE' => 'Date naissance',
            'POIDS'          => 'Poids (kg)',
            'NOM_ESPECE'     => 'Espèce'
        ]
    ],
    'enclos' => [
        'label' => 'Enclos',
        'colonnes_affichage' => [
            'ID_ENCLOS'  => 'ID',
            'LONGITUDE'  => 'Longitude',
            'LATITUDE'   => 'Latitude',
            'SURFACE'    => 'Surface (m²)'
        ]
    ],
    'espece' => [
        'label' => 'Espèce',
        'colonnes_affichage' => [
            'NOM_ESPECE'       => 'Nom espèce',
            'NOM_LATIN_ESPECE' => 'Nom latin',
            'MENACE'           => 'Menacée ?'
        ]
    ],
    'boutique' => [
        'label' => 'Boutique',
        'colonnes_affichage' => [
            'ID_BOUTIQUE'        => 'ID',
            'ID_GERANT_BOUTIQUE' => 'ID Gérant'
        ]
    ],
    'ca_journalier' => [
        'label' => 'CA Journalier',
        'colonnes_affichage' => [
            'ID_CA'       => 'ID',
            'MONTANT'     => 'Montant (€)',
            'DATE_CA'     => 'Date',
            'ID_BOUTIQUE' => 'ID Boutique'
        ]
    ],
    'contrat' => [
        'label' => 'Contrat',
        'colonnes_affichage' => [
            'ID_CONTRAT'   => 'ID',
            'DATE_CONTRAT' => 'Date',
            'TYPE_CONTRAT' => 'Type',
            'ID_PERSONNEL' => 'ID Personnel'
        ]
    ],
    'zone' => [
        'label' => 'Zone',
        'colonnes_affichage' => [
            'NOM_ZONE' => 'Nom zone'
        ]
    ],
    'medicament' => [
        'label' => 'Médicament',
        'colonnes_affichage' => [
            'ID_MEDICATION' => 'ID',
            'TYPE_SOIN'     => 'Type de soin',
            'DOSE_SOIN'     => 'Dose'
        ]
    ],
    'nouriture' => [
        'label' => 'Nourriture',
        'colonnes_affichage' => [
            'NOM_NOURRITURE'  => 'Nom',
            'DOSE_NOURRITURE' => 'Dose',
            'DATE_EXPIRATION' => 'Date expiration'
        ]
    ]
];

$entites_autorisees = $autorisations[$type_personnel] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche - Zoo'land</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="layout">
    <aside class="sidebar">
        <h2>Zoo'land</h2>
        <a href="dashboard.php">Accueil</a>
        <a href="selection_rech.php">Recherche</a>
        <a href="mdf_mdp.php">Modifier le mot de passe</a>
        <a href="deconexion.php">Déconnexion</a>
    </aside>

    <main class="content">
        <div class="card">
            <h1> Moteur de recherche</h1>

            <?php if (empty($entites_autorisees)): ?>
                <p style="color:red;">Aucune entité accessible pour votre profil.</p>
            <?php else: ?>

            <form method="post" action="resultat_rech.php">

                <label for="entite">Entité à rechercher :</label>
                <select name="entite" id="entite" required>
                    <option value="">-- Choisir une entité --</option>
                    <?php foreach ($entites_autorisees as $e): ?>
                        <?php if (isset($configuration[$e])): ?>
                        <option value="<?= htmlspecialchars($e) ?>">
                            <?= htmlspecialchars($configuration[$e]['label']) ?>
                        </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>

                <label for="valeur">Valeur recherchée :</label>
                <input type="text" name="valeur" id="valeur" placeholder="Laissez vide pour tout afficher">
                <p class="info">La recherche porte sur toutes les colonnes de l'entité choisie.</p>

                <button class="btn" type="submit">Rechercher</button>
            </form>

            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>