<?php

require_once('setup/database.php');
extract($_POST);
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$list_id = explode('_', $id);


$update = $bdd->prepare('UPDATE message SET vu = ? WHERE id_p1 = ? AND id_p2 = ?');
$update->execute(array(1, $list_id[1], $_SESSION['id']));

?>