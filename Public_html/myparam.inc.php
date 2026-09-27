<?php
define("MYHOST","10.1.16.56/oracle2");
define("MYUSER","pascalrousselle");
define("MYPASS","oracle");
   
if (!isset($conn)) {
    $conn = oci_connect(MYUSER, MYPASS, MYHOST);
    if (!$conn) {
        $e = oci_error();
        die("Erreur de connexion Oracle : " . htmlspecialchars($e['message']));
    }
}
?>
