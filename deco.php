<?php

require_once('setup/database.php');
session_start();

if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
	$connected = $bdd->prepare('UPDATE connected SET co = ? WHERE id_user = ?');
	$connected->execute(array(time(), $_SESSION['id']));
}
$_SESSION = array();
session_destroy();
header('location:index.php');

?>