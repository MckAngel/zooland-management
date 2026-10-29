<?php
// Modèle de configuration de la connexion Oracle.
// Copier ce fichier en "myparam.inc.php" (ignoré par Git) et renseigner vos propres valeurs.
// Ne jamais publier de vrais identifiants.
define("MYHOST", "adresse_du_serveur/service");
define("MYUSER", "utilisateur");
define("MYPASS", "mot_de_passe");

if (!isset($conn)) {
    $conn = oci_connect(MYUSER, MYPASS, MYHOST);
    if (!$conn) {
        $e = oci_error();
        die("Erreur de connexion Oracle : " . htmlspecialchars($e['message']));
    }
}
?>
