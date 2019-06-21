<?php

require_once('../setup/database.php');
extract($_POST);
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$is_m = $bdd->prepare('SELECT * FROM bloquer WHERE id_bloqueur = ? AND id_bloquer = ?');
$is_m->execute(array($_SESSION['id'], $id));
if ($is_m->rowCount() > 0) {
	$get_m = $is_m->fetch();
	$rm_m = $bdd->prepare("DELETE FROM bloquer WHERE id_bloqueur = ? AND id_bloquer = ?");
	$rm_m->execute(array($_SESSION['id'], $id));
}

?>