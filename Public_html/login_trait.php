<?php
session_start();


if (!empty($_POST['id_personnel']) && !empty($_POST['mot_de_passe'])) {

    $id = (int) $_POST['id_personnel'];
    $mdp = $_POST['mot_de_passe'];


    include_once("myparam.inc.php");
  

    $conn = oci_connect(MYUSER, MYPASS, MYHOST);


    if (!$conn) {
        $e = oci_error();
        die("Erreur de connexion Oracle : " . htmlentities($e['message'], ENT_QUOTES));
    }

    $sql = "SELECT nom_personnel, prenom_personnel, mot_de_passe , type_personnel
            FROM personnel
            WHERE Id_personnel = :id";

    $stid = oci_parse($conn, $sql);

    if (!$stid) {
        $e = oci_error($conn);
        die("Erreur de préparation : " . htmlentities($e['message'], ENT_QUOTES));
    }

    oci_bind_by_name($stid, ":id", $id);

    $r = oci_execute($stid);

    if (!$r) {
        $e = oci_error($stid);
        die("Erreur d'exécution : " . htmlentities($e['message'], ENT_QUOTES));
    }

    $user = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

if ($user) {
    if (password_verify($mdp, $user['MOT_DE_PASSE'])) {
        $_SESSION['id_personnel'] = $id;
        $_SESSION['nom']           = $user['NOM_PERSONNEL'];
        $_SESSION['prenom']        = $user['PRENOM_PERSONNEL'];
        $_SESSION['type_personnel'] = $user['TYPE_PERSONNEL'];

        oci_free_statement($stid);
        oci_close($conn);

        header("Location: dashboard.php");
        exit;
    }
    else {
            oci_free_statement($stid);
            oci_close($conn);

            header("Location: login.php?erreur=2");
            exit;
        }
    } 
    else {
        oci_free_statement($stid);
        oci_close($conn);

        header("Location: login.php?erreur=44");
        exit;
    }

} else {
    header("Location: login.php?erreur=455555");
    exit;
}
?>
