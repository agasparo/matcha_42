<?php

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$tab_order = ["song", "played", "historique", "local", "insta"];
$tab_res = [];
$final_tab = get_final_array(get_member($tab_res, $tab_order, $bdd));
$tab_res = array_merge($tab_res, profil_by_ori($final_tab, $bdd));
$tab_res = array_merge($tab_res, profil_by_ori_mid($final_tab, $bdd));
$tab_res = array_merge($tab_res, $final_tab);
$tab_res = get_final_array($tab_res);

$_SESSION['profils'] = is_bloque($bdd, $tab_res);

function is_bloque($bdd, $tab_res) {
	$tab_bloq = [];
	$bloc = $bdd->prepare('SELECT * FROM bloquer WHERE id_bloqueur = ? OR id_bloquer = ?');
	$bloc->execute(array($_SESSION['id'], $_SESSION['id']));
	while ($is_bloc = $bloc->fetch()) {
		if ($is_bloc['id_bloqueur'] == $_SESSION['id'])
			array_push($tab_bloq, $is_bloc['id_bloquer']);
		else
			array_push($tab_bloq, $is_bloc['id_bloqueur']);
	}
	if (empty($tab_bloq))
		return ($tab_res);
	foreach ($tab_bloq as $key => $value) {
		if (($key = array_search($value, $tab_res)) !== false) {
    		unset($tab_res[$key]);
		}
	}
	return (array_values($tab_res));
}

function profil_by_ori($final_tab, $bdd) {
	$tabs_deb = [];
	$query = 'SELECT * FROM membre WHERE id = '.$_SESSION['id'];
	$df = $bdd->query($query);
	$sf = $df->fetch();
	$sexe = $sf['sexe'];
	$att = $sf['attirance'];
	foreach ($final_tab as $key => $value) {
		$query = 'SELECT * FROM membre WHERE id = '.$value;
		$user = $bdd->query($query);
		$userinfos = $user->fetch();
		if ($userinfos['sexe'] == $att && $userinfos['attirance'] == $sexe) {
			array_push($tabs_deb, $userinfos['id']);
		}
	}
	return ($tabs_deb);
}

function profil_by_ori_mid($final_tab, $bdd) {
	$tabs_deb = [];
	$query = 'SELECT * FROM membre WHERE id = '.$_SESSION['id'];
	$df = $bdd->query($query);
	$sf = $df->fetch();
	$sexe = $sf['sexe'];
	$att = $sf['attirance'];
	foreach ($final_tab as $key => $value) {
		$query = 'SELECT * FROM membre WHERE id = '.$value;
		$user = $bdd->query($query);
		$userinfos = $user->fetch();
		if ($userinfos['sexe'] == $att) {
			array_push($tabs_deb, $userinfos['id']);
		}
	}
	return ($tabs_deb);
}

function get_final_array($tab) {
	$final_tab = [];
	$i = 0;
	while (isset($tab[$i])) {
		if (!in_array($tab[$i], $final_tab))
			array_push($final_tab, $tab[$i]);
		$i++;
	}
	return ($final_tab);
}

function get_member($tab_res, $tab_order, $bdd) {
	$tab = [];
	foreach ($tab_order as $key => $value) {
		$tab = array_merge($tab, get_res($value, $bdd));
	}
	$tab = array_merge($tab, get_member_values($bdd));
	return ($tab);
}

function get_member_values($bdd) {
	$tab = [];
	$query = 'SELECT * FROM membre WHERE id = '.$_SESSION['id'];
	$user = $bdd->query($query);
	$userinfos = $user->fetch();
	$sexe = $userinfos['sexe'];
	$att = $userinfos['attirance'];
	$interts = $userinfos['interets'];
	$tab = array_merge($tab, orientation($bdd, $sexe, $att));
	$tab = array_merge($tab, interets($bdd, $interts));
	$query = 'SELECT * FROM matchs WHERE (id_p1 = '.$_SESSION['id'].' OR id_p2 = '.$_SESSION['id'].') AND match_p1 = 1 AND match_p2 = 1';
	$m = $bdd->query($query);
	$tab = array_merge($tab, popularite($bdd, $m->rowCount()));
	return ($tab);
}

function popularite($bdd, $count) {
	if ($count < 10)
		$tab = get_by_grade(9, 0, $bdd);
	if ($count >= 10 && $count <= 50)
		$tab = get_by_grade(50, 10, $bdd);
	if ($count > 50) {
		$tab = get_by_grade(10000000, 51, $bdd);
	}
	return ($tab);
}

function get_by_grade($grade_max, $grade_min, $bdd) {
	$tab = [];
	$query = 'SELECT * FROM matchs';
	$rep = $bdd->query($query);
	while ($infos_m = $rep->fetch()) {
		$query = 'SELECT * FROM matchs WHERE (id_p1 = '.$infos_m['id_p1'].' OR id_p2 = '.$infos_m['id_p1'].') AND match_p1 = 1 AND match_p2 = 1';
		$c = $bdd->query($query);
		if ($c->rowCount() >= $grade_min && $c->rowCount() <= $grade_max) {
			if ($infos_m['id_p1'] !== $_SESSION['id'] && !in_array($infos_m['id_p1'], $tab))
				array_push($tab, $infos_m['id_p1']);
		}
		$query = 'SELECT * FROM matchs WHERE (id_p1 = '.$infos_m['id_p2'].' OR id_p2 = '.$infos_m['id_p2'].') AND match_p1 = 1 AND match_p2 = 1';
		$c = $bdd->query($query);
		if ($c->rowCount() >= $grade_min && $c->rowCount() <= $grade_max) {
			if ($infos_m['id_p2'] !== $_SESSION['id'] && !in_array($infos_m['id_p2'], $tab))
				array_push($tab, $infos_m['id_p2']);
		}
	}
	return ($tab);
}

function interets($bdd, $ints) {
	$tab = [];
	$int = explode(' ', $ints);
	$i = 0;
	while (isset($int[$i])) {
		$query = 'SELECT * FROM membre WHERE interets LIKE "%'.$int[$i].'%"';
		$rep = $bdd->query($query);
		while ($reps = $rep->fetch()) {
			if (!in_array($reps['id'], $tab))
				array_push($tab, $reps['id']);
		}
		$i++;
	}
	return ($tab);
}

function orientation($bdd, $sexe, $att) {
	$tab = [];
	$query = 'SELECT * FROM membre WHERE sexe = "'.$att.'" AND attirance = "'.$sexe.'"';
	$rep = $bdd->query($query);
	while ($reps = $rep->fetch()) {
		array_push($tab, $reps['id']);
	}
	return ($tab);
}

function get_res($tab_bdd, $bdd) {
	$tab = [];
	if ($tab_bdd != "historique")
		$query = 'SELECT * FROM '.$tab_bdd.' WHERE id_user = '.$_SESSION['id'];
	else
		$query = 'SELECT * FROM '.$tab_bdd;
	$res_req = $bdd->query($query);
	if ($res_req->rowCount() > 0) {
		if ($tab_bdd == "song")
			$tab = array_merge($tab, song($bdd, $res_req));
		if ($tab_bdd == "played")
			$tab = array_merge($tab, played($bdd, $res_req));
		if ($tab_bdd == "historique")
			$tab = array_merge($tab, histo($bdd, $res_req));
		if ($tab_bdd == "local")
			$tab = array_merge($tab, local($bdd, $res_req));
		if ($tab_bdd == "insta")
			$tab = array_merge($tab, insta($bdd, $res_req));
	} else {
		return ($tab);
	}
	return ($tab);
}

function insta($bdd, $game_rep) {
	$tab_insta = [];
	while ($instainfos = $game_rep->fetch()) {
		$query = 'SELECT * FROM insta';
		$get_rep = $bdd->query($query);
		while ($reps = $get_rep->fetch()) {
			if ($reps['nb_followers'] + 500 >= $instainfos['nb_followers'] || $reps['nb_followers'] - 500 >= $instainfos['nb_followers']) {
				if ($reps['id_user'] !== $_SESSION['id']) {
					if (!in_array($reps['id_user'], $tab_insta))
						array_push($tab_insta, $reps['id_user']);
				}
			}
		}
	}
	return ($tab_insta);
}

function local($bdd, $local_rep) {
	$tab_local = [];
	$tab_local = array_merge($tab_local, req_local($bdd, 'pays', $local_rep));
	$tab_local = array_merge($tab_local, req_local($bdd, 'city', $local_rep));
	return ($tab_local);
}

function req_local($bdd, $type, $local_rep) {
	$tab_local = [];
	$local_infos = $local_rep->fetch();
	$query = 'SELECT * FROM local WHERE '.$type.' = "'.$local_infos[$type].'"';
	$get_rep = $bdd->query($query);
	while ($reps = $get_rep->fetch()) {
		if ($reps[$type] == $local_infos[$type] && $reps['id_user'] !== $_SESSION['id']) {
			if (!in_array($local_infos['id_user'], $tab_local))
				array_push($tab_local, $reps['id_user']);
		}
	}
	return ($tab_local);
}

function histo($bdd, $histo_rep) {
	$tab_histo = [];
	while ($histo_infos = $histo_rep->fetch()) {
		$query = 'SELECT * FROM historique WHERE id_visiteur = '.$histo_infos['id_visiter'];
		$get_rep = $bdd->query($query);
		while ($reps = $get_rep->fetch()) {
			if ($reps['id_visiter'] == $_SESSION['id'] && $reps['id_visiteur'] !== $_SESSION['id']) {
				if (!in_array($reps['id_visiteur'], $tab_histo))
					array_push($tab_histo, $reps['id_visiteur']);
			}
		}
	}
	return ($tab_histo);
}

function played($bdd, $game_rep) {
	$tab_game = [];
	while ($gameinfos = $game_rep->fetch()) {
		$query = 'SELECT * FROM played WHERE game = "'.$gameinfos['game'].'"';
		$get_rep = $bdd->query($query);
		while ($reps = $get_rep->fetch()) {
			if ($reps['time_played'] + 250 >= $gameinfos['time_played'] || $reps['time_played'] - 250 >= $gameinfos['time_played']) {
				if ($reps['id_user'] !== $_SESSION['id']) {
					if (!in_array($reps['id_user'], $tab_game))
						array_push($tab_game, $reps['id_user']);
				}
			}
		}
	}
	return ($tab_game);
}

function song($bdd, $song_req) {
	$tab_song = [];
	while ($songinfos = $song_req->fetch()) {
		$query = 'SELECT * FROM song WHERE id_musique = "'.$songinfos['id_musique'].'"';
		$get_rep = $bdd->query($query);
		while ($reps = $get_rep->fetch()) {
			if ($reps['nb_fois'] + 10 >= $songinfos['nb_fois'] || $reps['nb_fois'] - 10 >= $songinfos['nb_fois']) {
				if ($reps['id_user'] !== $_SESSION['id']) {
					if (!in_array($reps['id_user'], $tab_song))
						array_push($tab_song, $reps['id_user']);
				}
			}
		}
	}
	return ($tab_song);
}

?>