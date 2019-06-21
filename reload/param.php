<?php

require_once('../setup/database.php');
extract($_POST);
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

if ($id == 1)
	echo get_macths($bdd);
else if ($id == 2)
	echo get_bloque($bdd);
else if ($id == 3)
	echo get_macths($bdd)."|".get_bloque($bdd);

function get_macths($bdd) {
	$tab = "<h1 class='display-6'>Liste des personnes avec qui vous avez matches</h1><table class='table table-hover'>";
	$get_m = $bdd->prepare('SELECT * FROM matchs WHERE (id_p1 = ? OR id_p2 = ?) AND match_p1 = ? AND match_p2 = ?');
	$get_m->execute(array($_SESSION['id'], $_SESSION['id'], 1, 1));
	while ($match_infos = $get_m->fetch()) {
		if ($match_infos['id_p1'] == $_SESSION['id'])
			$matched = $match_infos['id_p2'];
		else
			$matched = $match_infos['id_p1'];
		$user = $bdd->prepare("SELECT * FROM membre WHERE id = ?");
		$user->execute(array($matched));
		$userinfos = $user->fetch();
		if (preg_match("#https://#", $userinfos['photos_pro']) || preg_match("#http://#", $userinfos['photos_pro']))
			$photo = "<img src='".$userinfos['photos_pro']."' style='width: 15vw;'/>";
		else
			$photo = "<img src='photos/".$userinfos['photos_pro']."'/>";
		$tab .= "<tr><td style='vertical-align: inherit;'>".$photo."</td><td style='vertical-align: inherit;'>".$userinfos['nom']." ".$userinfos['prenom']."</td><td style='vertical-align: inherit; color: red; cursor: pointer;' id='".$userinfos['id']."' class='match_del'>Ne plus matcher</td><td style='vertical-align: inherit; cursor: pointer;' id='".$_SESSION['id']."_".$userinfos['id']."_msg_env_f' class='msg_env'>envoyer un message</td></tr>";
	}
	$tab = $tab."</table>";
	if ($tab == "<table class='table table-hover'></table>")
		return ("<table class='table table-hover'><tr><td>Vous avez matche avec personne</td></tr></table>");
	return ($tab);
}

function get_bloque($bdd) {
	$tab = "<h1 class='display-6'>Liste des personnes que vous avez bloques</h1><table class='table table-hover'>";
	$get_m = $bdd->prepare('SELECT * FROM bloquer WHERE id_bloqueur = ?');
	$get_m->execute(array($_SESSION['id']));
	while ($match_infos = $get_m->fetch()) {
		$user = $bdd->prepare("SELECT * FROM membre WHERE id = ?");
		$user->execute(array($match_infos['id_bloquer']));
		$userinfos = $user->fetch();
		if (preg_match("#https://#", $userinfos['photos_pro']) || preg_match("#http://#", $userinfos['photos_pro']))
			$photo = "<img src='".$userinfos['photos_pro']."' style='width: 15vw;'/>";
		else
			$photo = "<img src='photos/".$userinfos['photos_pro']."'/>";
		$tab .= "<tr><td style='vertical-align: inherit;'>".$photo."</td><td style='vertical-align: inherit;'>".$userinfos['nom']." ".$userinfos['prenom']."</td><td style='vertical-align: inherit; color: green; cursor: pointer;' id='".$userinfos['id']."' class='bloq_del'>Ne plus bloquer</td></tr>";
	}
	$tab = $tab."</table>";
	if ($tab == "<table class='table table-hover'></table>")
		return ("<table class='table table-hover'><tr><td>Vous avez bloquer personne</td></tr></table>");
	return ($tab);
}

?>