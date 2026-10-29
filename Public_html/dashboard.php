<?php
session_start();

if (!isset($_SESSION['id_personnel'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
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
    <h1>Bienvenue <?= htmlspecialchars($_SESSION['prenom']) ?> <?= htmlspecialchars($_SESSION['nom']) ?></h1>
    <p style="color:#555;">Rôle : <strong><?= htmlspecialchars($_SESSION['type_personnel']) ?></strong></p>

    <div class="cards">
        <?php 
             switch( $_SESSION['type_personnel'] ) {
                case 'gerant':
                    echo '<div class="card-small">
                            <h3>Statistiques</h3>
                            <p>Visualisez les statistiques du parc.</p>
                            <a class="btn" href="gerant/statistiques.php">Accéder</a>
                          </div>';
                    echo '<div class="card-small">
                            <h3>Gestion du personnel</h3>
                            <p>Gérez les membres du personnel.</p>
                            <a class="btn" href="gerant/gestion_personnel.php">Accéder</a>
                          </div>';
                    echo '<div class="card-small">
                            <h3>Nouveau personnel</h3>
                            <p>Ajouter un nouveau membre.</p>
                            <a class="btn" href="gerant/nouveau_personnel.php">Accéder</a>
                          </div>';
                    echo '<div class="card-small">
                            <h3>Parrain</h3>
                            <p>Ajouter un parrain.</p>
                            <a class="btn" href="gerant/parrain.php">Accéder</a>
                          </div>';
                    break;
                case 'gerant boutique':
                    echo '<div class="card-small">
                            <h3>Statistiques</h3>
                            <p>Visualisez les statistiques du parc.</p>
                            <a class="btn" href="statistiques.php">Accéder</a>
                          </div>';
                    break;
                case 'comptable':
                    echo '<div class="card-small">
                            <h3>Finances</h3>
                            <p>Gérez les finances du parc.</p>
                            <a class="btn" href="gerant/statistiques.php">Accéder</a>
                          </div>';
                    break;
                case 'soignant':
                    echo '<div class="card-small">
                            <h3>Soins & historique</h3>
                            <p>Gérez les soins des animaux.</p>
                            <a class="btn" href="soignant/historique_soins.php">Accéder</a>
                          </div>';
                    echo '<div class="card-small">
                            <h3>Alimentation</h3>
                            <p>Gérez l\'alimentation des animaux.</p>
                            <a class="btn" href="soignant/alimentation.php">Accéder</a>
                          </div>';
                    break;
                case 'agent entretient enclos':
                    echo '<div class="card-small">
                            <h3>Entretien</h3>
                            <p>Gérez l\'entretien des enclos.</p>
                            <a class="btn" href="agent_entretien/entretien.php">Accéder</a>
                          </div>';
                    break;
                default:
                    // Pas de carte supplémentaire pour les autres rôles
                    break;
            }
        ?>
    </div>
</main>

</div>

</body>
</html>
