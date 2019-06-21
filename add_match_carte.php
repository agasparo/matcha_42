<?php

require_once('setup/database.php');
session_start();

extract($_POST);

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$is_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND id_p2 = ?');
$is_match->execute(array($_SESSION['id'], $id));

if ($is_match->rowCount() > 0) {
	$is_match = $bdd->prepare('SELECT * FROM matchs WHERE match_p1 = ?');
	$is_match->execute(array(0));
	if ($is_match->rowCount() > 0) {
		echo "go remp bdd";
	} else {
		echo "deja demander";
	}
} else {
	$is_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND id_p2 = ?');
	$is_match->execute(array($id, $_SESSION['id']));

	if ($is_match->rowCount() > 0) {
		$is_match = $bdd->prepare('SELECT * FROM matchs WHERE match_p2 = ?');
		$is_match->execute(array(0));
		if ($is_match->rowCount() > 0) {
			echo "go remp bdd";
		} else {
			echo "deja demander";
		}
	} else {
		$req_insert = $bdd->prepare('INSERT INTO matchs(id_p1, id_p2, match_p1, match_p2, vu_p1, vu_p2) VALUES(?, ?, ?, ?, ?, ?)');
		$req_insert->execute(array($_SESSION['id'], $id, 1, 0, 0, 0));
	}
}
?>