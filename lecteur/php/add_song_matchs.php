<?php

$DB_DSN = 'mysql:host=localhost;dbname=membres';
$DB_USER = 'root';
$DB_PASSWORD = '';

$bdd = new PDO($DB_DSN , $DB_USER, $DB_PASSWORD);

session_start();
extract($_POST);

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$get_m = $bdd->prepare("SELECT * FROM song WHERE id_user = ? AND id_musique = ?");
$get_m->execute(array($_SESSION['id'], $id_musique));
if ($get_m->rowCount() > 0) {
	$gets = $get_m->fetch();
	$nb_fois = intval($gets['nb_fois']) + 1;
	$get_m = $bdd->prepare("UPDATE song SET nb_fois = ? WHERE id_user = ? AND id_musique = ?");
	$get_m->execute(array($nb_fois, $_SESSION['id'], $id_musique));
} else {
	$set_m = $bdd->prepare("INSERT INTO song(id_user, id_musique, nb_fois) VALUES(?, ?, ?)");
	$set_m->execute(array($_SESSION['id'], $id_musique, 1));
}

?>