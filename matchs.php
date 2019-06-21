<?php

require_once('setup/database.php');
extract($_POST);
session_start();

if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
	$identifiant = str_replace('matchs', '', $identifiant);
	if ($satus == "matcher en retour") {
		$update_matchs = $bdd->prepare("UPDATE matchs SET match_p2 = ?, vu_p1 = ?, vu_p2 = ? WHERE id_p1 = ? AND id_p2 = ?");
		$update_matchs->execute(array(1, 0, 0, $identifiant, $_SESSION['id']));
		echo "it's a match";
	} else {
		$check_if_i_al = $bdd->prepare("SELECT * FROM matchs WHERE id_p1 = ? AND id_p2 = ?");
		$check_if_i_al->execute(array($_SESSION['id'], $identifiant));
		if ($check_if_i_al->rowCount() == 0) {
			$insert_match = $bdd->prepare("INSERT INTO matchs(id_p1, id_p2, match_p1, match_p2, vu_p1, vu_p2) VALUES(?, ?, ?, ?, ?, ?)");
			$insert_match->execute(array($_SESSION['id'], $identifiant, 1, 0, 1, 0));
			echo "Demande de match envoyee";
		} else {
			echo "Demande de match deja envoyee";
		}
	}
}
?>