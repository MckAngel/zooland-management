<?php
session_start();

if (isset($_SESSION['mot_de_passe'])) {
    header("Location: dashboard.php");
    exit;
}
?>

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

<h2>Connexion</h2>

<?php
if(isset($_GET['erreur'])){
    echo "<p class='error'>Identifiants incorrects</p>";
}
?>

<form method="post" action="login_trait.php">

<label>ID personnel</label>
<input type="number" name="id_personnel" required>

<label>Mot de passe</label>
<input type="password" name="mot_de_passe" required>

<button class="btn" type="submit">Connexion</button>

</form>

</div>

</body>
</html>
