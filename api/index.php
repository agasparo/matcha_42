<?php

header('Content-Type: application/json');

require_once('../setup/database.php');

$retour['success'] = 1;
$retour['message'] = "";
$retour['infos'] = "";

extract($_GET);
if (isset($type) && $type == 'musique_all')
	echo get_all_musique($bdd, $retour);
if (isset($type) && $type == 'jeux')
	echo get_game($bdd, $retour);
if (isset($tab) && !empty($tab) && isset($type) && !empty($type))
	echo get_inf($type, $bdd, $retour, $tab);

function toUtf8($string){
	$string = str_replace('Ã¢','â', $string);
	$string = str_replace('Ã¢','â', $string);
	$string = str_replace('Ã©','é', $string);
	$string = str_replace('à©','é', $string);
	$string = str_replace('í©','é', $string);
	$string = str_replace('Ã ','à', $string);
	$string = str_replace('Ã¨','è', $string);
	$string = str_replace('Ã§','ç', $string);
	$string = str_replace('Â«','«', $string);
	$string = str_replace('Â»','»', $string);
	$string = str_replace("â€™","'", $string);
	$string = str_replace('Ãª','ê', $string);
	$string = str_replace('àª','ê', $string);
	$string = str_replace('â‚¬','€', $string);
	$string = str_replace('Ã´','ô', $string);
	$string = str_replace('Ã¤','ä', $string);
	$string = str_replace('Ã¹','ù', $string);
	$string = str_replace('Ã®','î', $string);
	$string = str_replace('à¨','è', $string);
	$string = str_replace('àª','ê', $string);
	$string = str_replace('Å“','œ', $string);
	$string = str_replace('à§','ç', $string);
	$string = str_replace('à»','û', $string);
	$string = str_replace('à®','î', $string);
	$string = str_replace('à´','ô', $string);
	$string = str_replace('à‰','é', $string);
	$string = str_replace('à€','à', $string);
	$string = str_replace('Â','', $string);
	$string = str_replace('Ã€', 'À', $string);
	$string = str_replace('Ã‰', 'É', $string);
	$string = str_replace('â€"', '-', $string);
	return ($string);
}

function get_inf($type, $bdd, $retour, $tab) {
	$tab = json_decode(unserialize($tab), true);
	$tab = $tab['infos']['user'];
	$str = "SELECT * FROM membre WHERE ";
	foreach ($tab as $key => $value) {
		$str .= $key.'="'.$value.'" AND ';
	}
	$str = substr($str, 0, -5);
	$req = $bdd->query($str);
	$req_user = $req->fetch();
	if ($type == 'local') {
		$pos = $bdd->query('SELECT * FROM local WHERE id_user = '.$req_user['id']);
		$pos_rep = $pos->fetch();
		$retour['message'] = "local user get data success";
		unset($pos_rep['id']);
		unset($pos_rep['id_user']);
		unset($pos_rep['lat']);
		unset($pos_rep['lon']);
		foreach ($pos_rep as $key => $value) {
			if (is_numeric($key))
				unset($pos_rep[$key]);
			else
				$pos_rep[$key] = toUtf8($value);
		}
		$retour['infos'] = $pos_rep;
		return (json_encode($retour));
	}
	if ($type == 'game_vid') {
		$pos = $bdd->query('SELECT * FROM played WHERE id_user = '.$req_user['id']);
		$pos_rep = $pos->fetch();
		if ($pos->rowCount() > 0) {
			$retour['message'] = "game user get data success";
			unset($pos_rep['id']);
			unset($pos_rep['id_user']);
			foreach ($pos_rep as $key => $value) {
				if (is_numeric($key))
					unset($pos_rep[$key]);
				else
					$pos_rep[$key] = toUtf8($value);
			}
		} else {
			$retour['message'] = "game user get data success";
			$pos_rep = 'no game play';
		}
		$retour['infos'] = $pos_rep;
		return (json_encode($retour));
	}
	if ($type == 'follow') {
		$pos = $bdd->query('SELECT * FROM insta WHERE id_user = '.$req_user['id']);
		$pos_rep = $pos->fetch();
		if ($pos->rowCount() > 0) {
			$retour['message'] = "followers user get data success";
			unset($pos_rep['id']);
			unset($pos_rep['id_user']);
			foreach ($pos_rep as $key => $value) {
				if (is_numeric($key))
					unset($pos_rep[$key]);
				else
					$pos_rep[$key] = toUtf8($value);
			}
		} else {
			$retour['message'] = "followers user get data success";
			$pos_rep = 'no followers';
		}
		$retour['infos'] = $pos_rep;
		return (json_encode($retour));
	}
	if ($type == 'musique_user') {
		$pos = $bdd->query('SELECT * FROM song WHERE id_user = '.$req_user['id']);
		$retour['message'] = "musique user get data success";
		$tab = [];
		$i = 0;
		while ($pos_rep = $pos->fetch()) {
			$i++;
			$tab[$i] = $pos_rep['id_musique'].'||'.$pos_rep['nb_fois'];
		}
	}
	if (empty($tab))
		$retour['infos'] = 'no music heard';
	else
		$retour['infos'] = $tab;
	return (json_encode($retour));
}

function get_game($bdd, $retour) {
	$tab = [];
	$get_game = $bdd->query('SELECT * FROM played');
	$i = 0;
	while ($game = $get_game->fetch()) {
		$tab['game'][$i] = $game['game'];
		$i++;
	}
	$retour['message'] = "game get data success";
	$retour['infos'] = $tab;
	return (json_encode($retour));
}

function get_all_musique($bdd, $retour) {
	$tab = [];
	$get_m = $bdd->query('SELECT * FROM musique');
	while ($musique = $get_m->fetch()) {
		$tab['musique'][$musique['id_musique']] = toUtf8($musique['name_musique']);
	}
	$retour['message'] = "musique get data success";
	$retour['infos'] = $tab;
	return (json_encode($retour));
}

?>