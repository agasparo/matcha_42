<?php

require_once('../setup/database.php');
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);


$req_msg = $bdd->prepare('SELECT * FROM message WHERE id_p2 = ? AND vu = ?');
$req_msg->execute(array($_SESSION['id'], 0));
$count_messages = $req_msg->rowCount();
while($get_messages = $req_msg->fetch()) {
	if ($get_messages['id_envoyeur'] == $_SESSION['id']) {
		$count_messages--;
	}
}

$req_histo = $bdd->prepare('SELECT * FROM historique WHERE id_visiter = ? AND vu = ?');
$req_histo->execute(array($_SESSION['id'], 0));
$count_visites = $req_histo->rowCount();
while ($get_visiteur = $req_histo->fetch()) {
	if ($get_visiteur['id_visiteur'] == $_SESSION['id'])
		$count_visites--;
}

$req_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND vu_p1 = ? AND match_p2 = ?');
$req_match->execute(array($_SESSION['id'], 0, 1));
$count_match = $req_match->rowCount();
$req_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p2 = ? AND vu_p2 = ? AND match_p1 = ?');
$req_match->execute(array($_SESSION['id'], 0, 1));
$count_match = $count_match + $req_match->rowCount();
$req_suppr = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p2 = ? AND vu_p2 = ?');
$req_suppr->execute(array($_SESSION['id'], 0));
$count_match = $count_match + $req_suppr->rowCount();
$req_suppr = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p1 = ? AND vu_p1 = ?');
$req_suppr->execute(array($_SESSION['id'], 0));
$count_match = $count_match + $req_suppr->rowCount();

?>
<a class="nav-link" style="cursor: pointer;">Messages :<span class="badge badge-primary badge-pill"><?= $count_messages; ?></a></span>
<a class="nav-link" style="cursor: pointer;">Matchs :<span class="badge badge-primary badge-pill"><?= $count_match; ?></span></a>
<a class="nav-link" style="cursor: pointer;">Visites :<span class="badge badge-primary badge-pill"><?= $count_visites; ?></span></a>