<?php

require_once('setup/database.php');
extract($_POST);
session_start();

if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {

	$e = 0;

	$check_if_i_al = $bdd->prepare("SELECT * FROM matchs WHERE id_p1 = ? AND id_p2 = ? AND match_p1 = ? AND match_p2 = ?");
	$check_if_i_al->execute(array($_SESSION['id'], $identifiant, 1, 1));
	if ($check_if_i_al->rowCount() > 0) {
		echo "Envoyer un message|";
		$e = 1;
	}

	$check_if_i_al = $bdd->prepare("SELECT * FROM matchs WHERE id_p1 = ? AND id_p2 = ? AND match_p1 = ? AND match_p2 = ?");
	$check_if_i_al->execute(array($identifiant, $_SESSION['id'], 1, 1));
	if ($check_if_i_al->rowCount() > 0) {
		echo "Envoyer un message|";
		$e = 1;
	}

	if ($e == 0) {
		$is_match = $bdd->prepare('SELECT * FROM matchs WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?)');
		$is_match->execute(array($_SESSION['id'], $identifiant, $_SESSION['id'], $identifiant));
		if ($is_match->rowCount() == 0) {
			echo "match|";
		} else {
			$inf = $is_match->fetch(0);
			if ($inf['id_p1'] == $_SESSION['id'] AND $inf['match_p1'] == 0) {
				echo "matcher en retour|";
			} else if ($inf['id_p2'] == $_SESSION['id'] AND $inf['match_p2'] == 0) {
				echo "matcher en retour|";
			} else {
				echo "match|";
			}
		}
	}


	$is_bloq = $bdd->prepare("SELECT * FROM bloquer WHERE id_bloqueur = ?");
	$is_bloq->execute(array($_SESSION['id']));
	if ($is_bloq->rowCount() > 0) {
		$req_signal = $bdd->prepare("DELETE FROM bloquer WHERE id_bloqueur = ?");
		$req_signal->execute(array($_SESSION['id']));
		echo "L'utilisateur a bien ete debloque";
	} else {
		$req_signal = $bdd->prepare("INSERT INTO bloquer(id_bloquer, id_bloqueur) VALUES(?, ?)");
		$req_signal->execute(array($identifiant, $_SESSION['id']));
		echo "L'utilisateur a bien ete bloque";
	}
}

?>