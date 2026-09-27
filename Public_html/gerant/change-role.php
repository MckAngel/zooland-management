<?php
session_start();

if (!isset($_SESSION['id_personnel'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id_personnel']) || !isset($_GET['new_role'])) {
    header('Location: gestion_personnel.php?error=' . urlencode('Paramètres manquants.'));
    exit;
}

$id_personnel = $_GET['id_personnel'];
$nouv_role     = trim($_GET['new_role']);

$roles_valid = ['gerant', 'soignant', 'gerant boutique', 'agent entretient enclos', 'comptable'];
if (!in_array($nouv_role, $roles_valid, true)) {
    header('Location: gestion_personnel.php?error=' . urlencode('Rôle invalide.'));
    exit;
}

if (!is_numeric($id_personnel)) {
    header('Location: gestion_personnel.php?error=' . urlencode('ID personnel invalide.'));
    exit;
}

require_once '../myparam.inc.php';

if (!isset($conn) || !$conn) {
    $e = oci_error();
    die('Erreur connexion : ' . htmlspecialchars($e['message']));
}

$req = 'UPDATE personnel SET type_personnel = :role WHERE id_personnel = :id';
$stid = oci_parse($conn, $req);
if (!$stid) {
    $e = oci_error($conn);
    die('Erreur préparation requête : ' . htmlspecialchars($e['message']));
}

$id_personnel_int = (int)$id_personnel;
oci_bind_by_name($stid, ':role', $nouv_role);
oci_bind_by_name($stid, ':id', $id_personnel_int);

if (!oci_execute($stid, OCI_COMMIT_ON_SUCCESS)) {
    $e = oci_error($stid);
    oci_free_statement($stid);
    die('Erreur exécution update : ' . htmlspecialchars($e['message']));
}

oci_free_statement($stid);

header('Location: gestion_personnel.php?success=' . urlencode('Rôle modifié avec succès.'));
exit;
