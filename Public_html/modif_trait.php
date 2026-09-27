<?php
session_start();

if (!isset($_SESSION['id_personnel'])) {
    header("Location: login.php");
    exit;
}

if (
    !isset($_POST['ancient_mdp']) ||
    !isset($_POST['nv_mdp']) ||
    !isset($_POST['nv_mdp_conf']) ||
    $_POST['ancient_mdp'] == '' ||
    $_POST['nv_mdp'] == '' ||
    $_POST['nv_mdp_conf'] == ''
) {
    header("Location: mdf_mdp.php?erreur=4");
    exit;
}

$ancien_mdp = $_POST['ancient_mdp'];
$nv_mdp = $_POST['nv_mdp'];
$nv_mdp_conf = $_POST['nv_mdp_conf'];

if ($nv_mdp !== $nv_mdp_conf) {
    header("Location: mdf_mdp.php?erreur=2");
    exit;
}

include_once("myparam.inc.php");

$conn = oci_connect(MYUSER, MYPASS, MYHOST);

if (!$conn) {
    $e = oci_error();
    die("Erreur de connexion Oracle : " . htmlentities($e['message'], ENT_QUOTES));
}

$sql = "SELECT mot_de_passe
        FROM personnel
        WHERE Id_personnel = :id";

$stid = oci_parse($conn, $sql);

if (!$stid) {
    $e = oci_error($conn);
    die("Erreur de préparation : " . htmlentities($e['message'], ENT_QUOTES));
}

$id_personnel = $_SESSION['id_personnel'];
oci_bind_by_name($stid, ":id", $id_personnel);

$r = oci_execute($stid);

if (!$r) {
    $e = oci_error($stid);
    die("Erreur d'exécution : " . htmlentities($e['message'], ENT_QUOTES));
}

$user = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

if (!$user) {
    oci_free_statement($stid);
    oci_close($conn);
    header("Location: mdf_mdp.php?erreur=3");
    exit;
}

if (!password_verify($ancien_mdp, $user['MOT_DE_PASSE'])) {
    oci_free_statement($stid);
    oci_close($conn);
    header("Location: mdf_mdp.php?erreur=1");
    exit;
}



$taille = strlen($nv_mdp);
if ($taille < 6) {
    oci_free_statement($stid);
    oci_close($conn);
    header("Location: mdf_mdp.php?erreur=2");
    exit;
}

$hash = password_hash($nv_mdp, PASSWORD_DEFAULT);

$sql = "UPDATE personnel
        SET mot_de_passe = :mdp
        WHERE Id_personnel = :id";

$stid = oci_parse($conn, $sql);

if (!$stid) {
    $e = oci_error($conn);
    die("Erreur de préparation : " . htmlentities($e['message'], ENT_QUOTES));
}

oci_bind_by_name($stid, ":mdp", $hash);
oci_bind_by_name($stid, ":id", $id_personnel);

$r = oci_execute($stid, OCI_COMMIT_ON_SUCCESS);

if (!$r) {
    $e = oci_error($stid);
    oci_rollback($conn);
    die("Erreur d'exécution : " . htmlentities($e['message'], ENT_QUOTES));
}


oci_free_statement($stid);
oci_close($conn);

header("Location: dashboard.php");
exit;

?>
