<?php

require_once('../setup/database.php');
extract($_POST);
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$is_m = $bdd->prepare('SELECT * FROM matchs WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?) AND match_p1 = ? AND match_p2 = ?');
$is_m->execute(array($_SESSION['id'], $id, $_SESSION['id'], $id, 1, 1));
if ($is_m->rowCount() > 0) {
	$get_m = $is_m->fetch();
	$rm_m = $bdd->prepare("DELETE FROM matchs WHERE id_p1 = ? AND id_p2 = ?");
	$rm_m->execute(array($get_m['id_p1'], $get_m['id_p2']));
	$a = 0;
	$b = 0;
	if ($get_m['id_p1'] == $_SESSION['id'])
		$a = 1;
	else
		$b = 1;
	$get_supp = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p1 = ? AND id_p2 = ?');
	$get_supp->execute(array($get_m['id_p1'], $get_m['id_p2']));
	if ($get_supp->rowCount() > 0) {
		$get_supps = $get_supp->fetch();
		$rm_supp = $bdd->prepare("DELETE FROM suppr_m WHERE id_p1 = ? AND id_p2 = ?");
		$rm_supp->execute(array($get_supps['id_p1'], $get_supps['id_p2']));
	}
	$add_supp = $bdd->prepare('INSERT INTO suppr_m(id_p1, id_p2, vu_p1, vu_p2) VALUES(?, ?, ?, ?)');
	$add_supp->execute(array($get_m['id_p1'], $get_m['id_p2'], $a, $b));
}

?>