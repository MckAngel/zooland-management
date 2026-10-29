

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion</title>
<link rel="stylesheet" href="style.css">
</head>
<div class = "banner"> <div class="text"> <h1>Zoo'land </h1></div> </div>

<body class="center">

<div class="card">

<h2> Modifer votre mot de passe </h2>

<?php

if(isset($_GET['erreur'])){
    if($_GET['erreur'] == '1')
    echo "<p class='error'> mot de passe incorrect</p>";
    
    else { if($_GET['erreur'] == '2') echo "<p class='error'> nouveaux mots de passe non conformes  </p>";}
}
?>

<form method="post" action="modif_trait.php">

<label>Ancient mot de passe</label>
<input type="password" name="ancient_mdp" required>

<label>nouveau mot de passe</label>
<input type="password" name="nv_mdp" required>

<label>confirmer le nouveau mot de passe</label>
<input type="password" name="nv_mdp_conf" required>

<button class="btn" type="submit"> modifier </button>

</form>

</div>

</body>
</html>
