<?php
session_start();

if (isset($_SESSION['id_personnel'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Zoo'land</title>
<link rel="stylesheet" href="style.css">
</head>
<div class = "banner"> <div class="text"> <h1>Zoo'land </h1></div> </div>
<body class="center">

<div class="card">
    <h1>Zoo'land</h1>
    <p>Interface de gestion du parc zoologique</p>

    <a class="btn" href="login.php">Se connecter</a>
</div>

</body>
</html>