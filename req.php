<?php

require_once('setup/database.php');
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$check_pro = $bdd->prepare("SELECT * FROM membre WHERE id = ?");
$check_pro->execute(array($_SESSION['id']));
$infs = $check_pro->fetch();

foreach ($infs as $key => $value) {
	if ($value == "0" && $key != 'oauth' && $key != 14)
		exit(0);
}

foreach ($_POST as $key => $value) {
	$_POST[$key] = htmlspecialchars($value);
	if (!empty($value)) {
		if ($key == 'age_min')
			$_POST[$key] = ">= ".$value;
		if ($key == 'age_max')
			$_POST[$key] = "<= ".$value;
		if ($key == 'time_played')
			$_POST[$key] = ">= ".$value;
		if ($key == 'nb_followers')
			$_POST[$key] = ">= ".$value;
	}
}


// membre
//$_POST['prenom'] = ""; ok
//$_POST['sexe'] = "F"; ok
//$_POST['attirance'] = "M"; ok
//$_POST['age_min'] = ">= 25"; ok
//$_POST['age_max'] = "<= 35"; ok
//$_POST['interets'] = ""; ok
$_POST['id'] = "";

// played
//$_POST['game'] = ""; ok
//$_POST['time_played'] = ""; ok

//insta
//$_POST['nb_followers'] = ">= 50"; ok

//local
//$_POST['city'] = ""; ok
//$_POST['region'] = ""; ok
//$_POST['pays'] = ""; ok

$tt = $_POST['type_trie'];
$tv = $_POST['trie_val'];
$to = $_POST['order_trie'];
$_POST['type_trie'] = "";
$_POST['trie_val'] = "";
$_POST['order_trie'] = "";

if (empty($tv))
	$tv = '0';

if ($to == "decroissant")
	$to = "DESC";
else
	$to = "ASC";

if ($tt == "grade")
	$tt = "popularite macht";
else if ($tt == "localisation")
	$tt = "loc";
else if ($tt == "popularite")
	$tt = "popularite nb_followers";
else if ($tt == "none")
	$tt = "";
else
	$tt = "age";

$tv = str_replace("plus grand que", ">=", $tv);
$tv = str_replace("plus petit que", "<=", $tv);

if ($tt == "age")
	$_POST['order'] = $tt." ".$to;
if ($tt == "loc") {
	if (!empty($tv))
		$_POST['order'] = $tt." ".$tv." ".$to;
}
if ($tt == "popularite nb_followers")
	$_POST['order'] = $tt." ".$tv." ".$to;
if ($tt == "popularite macht")
	$_POST['order'] = $tt." ".$tv." ".$to;

if (isset($_POST['order'])) {
	$order = $_POST['order'];
	$_POST['order'] = "";
} else {
	$order = "";
}


$external = external();
$insta = $external[3];
$local = $external[2];
$played = $external[1];
$external_req = $external[0];
$er = explode(" ", $order);
$chaine = "";
if (!empty($external_req)) {
	$data = explode("|", $external_req);
	foreach ($data as $dat => $value) {
		if (!empty($value) && !preg_match("#popularite nb_followers#", $order)) {
			$chaine = $chaine."$".str_replace($chaine, '', bdd_req(requette($value, ${$data[$dat]}), $chaine, $bdd));
		}
	}
	$_POST['id'] = create_final_req(explode("$", $chaine));
}


$bloquer = bloquer($bdd);

if (empty($order)) {
	final_search($bdd, unset_bloquer($bloquer, exec_req($bdd, $insta, $local, $played), requette('membre', get_membre_val($insta, $local, $played))), 1);
} else {
	order($order, $bdd, $insta, $local, $played, $bloquer);
}

function exec_req($bdd, $insta, $local, $played) {
	$res = [];
	$i = 0;
	$get_res = $bdd->query(requette('membre', get_membre_val($insta, $local, $played)));
	while ($gr = $get_res->fetch()) {
		$res[$i] = $gr['id'];
		$i++;
	}
	return ($res);
}

function unset_bloquer($list_bloq, $req_res, $req) {
	$tab_f = [];
	$i = 0;
	foreach ($req_res as $key => $value) {
		foreach ($list_bloq as $keys => $values) {
			$e = explode('_', $values);
			if ($e[0] != $value && $e[1] != $value && !in_array($value, $tab_f)) {
				$tab_f[$i] = $value;
				$i++;
			} else {
				if ($value == $_SESSION['id']) {
					$tab_f[$i] = $value;
					$i++;
				}
			}
		}
	}
	if (empty($tab_f) && count($list_bloq) >= 1)
		return ("SELECT * FROM membre WHERE id = -1");
	if (empty($tab_f))
		return ($req);
	$requete = "SELECT * FROM membre WHERE";
	foreach ($tab_f as $key => $value) {
		if ($requete == "SELECT * FROM membre WHERE")
			$requete = $requete." id = ".$value;
		else
			$requete = $requete." OR id = ".$value;
	}
	return ($requete);
}

function bloquer($bdd) {
	$tab = [];
	$i = 0;
	$bloq = $bdd->prepare('SELECT * FROM bloquer WHERE id_bloquer = ? OR id_bloqueur = ?');
	$bloq->execute(array($_SESSION['id'], $_SESSION['id']));
	while ($get_bloq = $bloq->fetch()) {
		$tab[$i] = $get_bloq['id_bloqueur'].'_'.$get_bloq['id_bloquer'];
		$i++;
	}
	return ($tab);
}

function create_final_req($tab) {
	foreach ($tab as $key => $value) {
		if (empty($value))
			unset($tab[$key]);
	}
	if (empty($tab))
		return ('-1');
	$i = 0;
	$tab = array_values($tab);
	if($tab[$i] == '-1')
		return ('-1');
	$tab_val = $tab[$i];
	$i++;
	while (isset($tab[$i])) {
		if($tab[$i] == '-1')
			return ('-1');
		$tab_val = compare(explode("|", $tab_val), explode("|", $tab[$i]));
		$i++;
	}
	if (empty($tab_val))
		return('-1');
	return ($tab_val);
}

function compare($tab, $tab1) {
	$tabs = [];
	$i = 0;
	if (count($tab) >= count($tab1)) {
		$choix = 'tab';
		$autre = 'tab1';
	} else {
		$choix = 'tab1';
		$autre = 'tab';
	}
	foreach (${$choix} as $key => $value) {
		if (in_array($value, ${$autre})) {
			$tabs[$i] = $value;
			$i++;
		}
	}
	return (implode("|", $tabs));
}

function exec_req_order($bdd, $req) {
	$res = [];
	$i = 0;
	$get_res = $bdd->query($req);
	while ($gr = $get_res->fetch()) {
		$res[$i] = $gr['id'];
		$i++;
	}
	return ($res);
}

function bloquer_tab($list_bloquer, $list_tab) {
	if (empty($list_bloquer))
		return ($list_tab);
	$tabs = [];
	$i = 0;
	foreach ($list_tab as $key => $value) {
		foreach ($list_bloquer as $keys => $values) {
			$e = explode('_', $values);
			if ($e[0] != $key && $e[1] != $key && !array_key_exists($key, $tabs)) {
				$tabs[$key] = $value;
			} else if ($key == $_SESSION['id']) {
				$tabs[$key] = $value;
			}
		}
	}
	if (empty($tabs) && count($list_tab) >= 1)
		return ("SELECT * FROM membre WHERE id = -1");
	if (empty($tabs))
		return ($list_tab);
	return ($tabs);
}

function bloquer_tab_val($list_bloquer, $list_tab) {
	if (empty($list_bloquer))
		return ($list_tab);
	$tabs = [];
	$i = 0;
	foreach ($list_tab as $key => $value) {
		foreach ($list_bloquer as $keys => $values) {
			$e = explode('_', $values);
			if ($e[0] != $value && $e[1] != $value && !in_array($value, $tabs)) {
				$tabs[$key] = $value;
			} else if ($value == $_SESSION['id']) {
				$tabs[$key] = $value;
			}
		}
	}
	if (empty($tabs) && count($list_tab) >= 1)
		return ("SELECT * FROM membre WHERE id = -1");
	if (empty($tabs))
		return ($list_tab);
	return ($tabs);
}

function order($order, $bdd, $insta, $local, $played, $bloquer) {
	$get = explode(" ", $order);
	if ($get[0] == 'age')
		final_search($bdd, unset_bloquer($bloquer, exec_req($bdd, $insta, $local, $played), requette('membre', get_membre_val($insta, $local, $played)))." ORDER BY ".$order, 1);
	if ($get[0] == "popularite") {
		if ($get[1] == "nb_followers") {
			$reqs = str_replace("AND (id = ", "{", requette("membre", $_POST));
			$deb = explode("{", $reqs);
			$deb[0] = trim(str_replace("SELECT * FROM membre WHERE", "", $deb[0]));
			if (!empty($_POST['nb_followers']))
				$get[3] = $_POST['nb_followers'];
			$req = "SELECT * FROM insta INNER JOIN membre ON insta.id_user = membre.id AND insta.nb_followers ".$get[2]." ".$get[3]." AND ".$deb[0]." ORDER BY nb_followers ".$get[4];
			final_search($bdd, unset_bloquer($bloquer, exec_req_order($bdd, $req), $req), 1);
		} else if ($get[1] == "macht") {
			$req = requette('membre', get_membre_val($insta, $local, $played));
			$get_match = final_search($bdd, requette('membre', get_membre_val($insta, $local, $played)), 2);
			$tab = explode("|", $get_match);
			$final_tab = [];
			foreach ($tab as $key => $value) {
				if (!empty($value)) {
					$e = explode(":", $value);
					if ($get[2] == '>=') {
						if ($e[0] >= intval($get[3]))
							$final_tab[$e[1]] = $e[0];
					} else {
						if ($e[0] <= intval($get[3]))
							$final_tab[$e[1]] = $e[0];
					}
				}
			}

			if (count(bloquer_tab($bloquer, $final_tab)) == 0)
				echo "<p style='width: 100%; text-align: center;'>aucun resultat trouve</p>";
			else
				echo "<p style='width: 100%; text-align: center;'>".count(bloquer_tab($bloquer, $final_tab))." resultat(s) touve(s)</p>";

			if (!empty($final_tab)) {
				if ($get[4] == 'ASC')
					asort($final_tab);
				else
					arsort($final_tab);
				final_search_tab(bloquer_tab($bloquer, $final_tab), $bdd, 'value');
			}
		}
	}
	if ($get[0] == "loc") {
		$distance = $get[1]." ".$get[2];
		$get_loc = final_search($bdd, requette('membre', get_membre_val($insta, $local, $played)), 0);
		$req_loc = $bdd->prepare('SELECT * FROM local WHERE id_user = ?');
		$req_loc->execute(array($_SESSION['id']));
		$locs = $req_loc->fetch();
		$current_loc = $locs['lon'].":".$locs['lat'];
		$chaine = calc_dis(explode("|", $get_loc), $current_loc, $distance);
		$tab_re = explode("|", $chaine);
		$final_tab = [];
		foreach ($tab_re as $key => $value) {
			if (!empty($value)) {
				$e = explode(":", $value);
				$final_tab[$e[1]] = $e[0];
			}
		}

		if (count(bloquer_tab_val($bloquer, $final_tab)) == 0)
			echo "<p style='width: 100%; text-align: center;'>aucun resultat trouve</p>";
		else
			echo "<p style='width: 100%; text-align: center;'>".count(bloquer_tab_val($bloquer, $final_tab))." resultat(s) touve(s)</p>";

		if ($get[3] == 'ASC')
			ksort($final_tab);
		else
			krsort($final_tab);
		final_search_tab(bloquer_tab_val($bloquer, $final_tab), $bdd, 'key');
	}
}

function final_search_tab($tab, $bdd, $type) {
	foreach ($tab as $key => $value) {
		if ($type == 'key') {
			if (!empty($value)) {
				$req_f = $bdd->prepare("SELECT * FROM membre WHERE id = ?");
				$req_f->execute(array($value));
				$infs = $req_f->fetch();
				?>
				<div class='div_pro'>
					<h3><?php if ($_SESSION['id'] == $infs['id']) { echo $infs['prenom']." (vous)"; } else { echo $infs['prenom']; } ?></h3>
					<?php
					if(preg_match("#https://#", $infs['photos_pro']) || preg_match("#http://#", $infs['photos_pro'])) {
						?><img src="<?= $infs['photos_pro']; ?>" class='img_pro_req'><?php
					} else {
						?><img src="<?= 'photos/'.$infs['photos_pro']; ?>" class='img_pro_req'><?php
					}
					?>
					<p class="show_pro_search" id="<?= $infs['id']; ?>">voir le profil</p>
				</div>
				<?php
			}
		} else {
			if (!empty($key)) {
				$req_f = $bdd->prepare("SELECT * FROM membre WHERE id = ?");
				$req_f->execute(array($key));
				$infs = $req_f->fetch();
				?>
				<div class='div_pro'>
					<h3><?php if ($_SESSION['id'] == $infs['id']) { echo $infs['prenom']." (vous)"; } else { echo $infs['prenom']; } ?></h3>
					<?php
					if(preg_match("#https://#", $infs['photos_pro']) || preg_match("#http://#", $infs['photos_pro'])) {
						?><img src="<?= $infs['photos_pro']; ?>" class='img_pro_req'><?php
					} else {
						?><img src="<?= 'photos/'.$infs['photos_pro']; ?>" class='img_pro_req'><?php
					}
					?>
					<p class="show_pro_search" id="<?= $infs['id']; ?>">voir le profil</p>
				</div>
				<?php
			}
		}
	}
}

function calc_dis($tab, $current_coord, $distance) {
	$is_ok = "";
	foreach ($tab as $key => $value) {
		if (!empty($value)) {
			$data = str_replace("{", "", $value);
			$data = str_replace("}", "", $data);
			$inf = explode(":", $data);

			$coord_a = deg2rad(doubleval($inf[0]));
			$coord_b = deg2rad(doubleval($inf[1]));

			$d = explode(":", $current_coord);
			$coord_c = deg2rad(doubleval($d[0]));
			$coord_d = deg2rad(doubleval($d[1]));

			$dis = acos(sin($coord_b) * sin($coord_d) + cos($coord_b) * cos($coord_d) * cos($coord_c - $coord_a)) * 6371;
			$check = explode(" ", $distance);

			if (trim($check[0]) == ">=") {
				if ($dis >= intval($check[1]))
					$is_ok = $is_ok."|".$inf[2].":".$dis;
			} else {
				if ($dis <= intval($check[1]))
					$is_ok = $is_ok."|".$inf[2].":".$dis;
			}
		}
	}
	return ($is_ok);
}

function get_membre_val($insta, $local, $played) {
	$tab = [];
	foreach ($_POST as $key => $value) {
		if (!array_key_exists($key, $played) && !array_key_exists($key, $local) && !array_key_exists($key, $insta)) {
			$tab[$key] = $value;
		}
	}
	return ($tab);
}

function final_search($bdd, $req, $is) {
	if (preg_match("#id = '-1'#", $req))
		$req = 'SELECT * FROM membre WHERE id = -1';
	$re = $bdd->query($req);

	if ($is == 1) {
		$is_result = $re->rowCount();
		if ($is_result > 0) {
			echo "<p style='width: 100%; text-align: center;'>".$is_result." resultat(s) touve(s)</p>";
		} else {
			echo "<p style='width: 100%; text-align: center;'>aucun resultat trouve</p>";
		}
	}

	$loc = "";
	while ($inf = $re->fetch()) {
		if ($is == 1) {
			?>
			<div class='div_pro'>
				<h3><?php if ($_SESSION['id'] == $inf['id']) { echo $inf['prenom']." (vous)"; } else { echo $inf['prenom']; } ?></h3>
				<?php
				if(preg_match("#https://#", $inf['photos_pro']) || preg_match("#http://#", $inf['photos_pro'])) {
					?><img src="<?= $inf['photos_pro']; ?>" class='img_pro_req'><?php
				} else {
					?><img src="<?= 'photos/'.$inf['photos_pro']; ?>" class='img_pro_req'><?php
				}
				?>
				<p class="show_pro_search" id="<?= $inf['id']; ?>">voir le profil</p>
			</div>
			<?php
		}
		if ($is == 0) {
			$req_loc = $bdd->prepare('SELECT * FROM local WHERE id_user = ?');
			$req_loc->execute(array($inf['id']));
			$locs = $req_loc->fetch();
			$loc = $loc."{".$locs['lon'].":".$locs['lat'].":".$inf['id']."}|";
		}
		if ($is == 2) {
			$req_loc = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? OR id_p2 = ? AND match_p1 = ? AND match_p2 = ?');
			$req_loc->execute(array($inf['id'], $inf['id'], 1, 1));
			$locs = $req_loc->rowCount();
			$loc = $loc.$locs.":".$inf['id']."|";
		}
		if ($is == 3)
			$loc = $loc.'|'.$inf['id'];
	}
	return ($loc);
}

function bdd_req($req, $sortie, $bdd) {
	$re = $bdd->query($req);
	if ($re->rowCount() > 0) {
		while ($inf = $re->fetch()) {
			if (!empty($sortie))
				$sortie = $sortie."|".$inf['id_user'];
			else
				$sortie = $inf['id_user'];
		}
	} else {
		$sortie = '-1';
	}
	return ($sortie);
}

function external() {
	$insta = [];
	$local = [];
	$played = [];
	$external_req = "";
	foreach ($_POST as $key => $value) {
		if (!empty($value) && ($key == 'game' || $key == 'time_played')) {
			$played[$key] = $value;
			$e = explode("|", $external_req);
			if (!in_array("played", $e))
				$external_req = $external_req."|played";
		}

		if (!empty($value) && $key == 'nb_followers') {
			$insta[$key] = $value;
			$e = explode("|", $external_req);
			if (!in_array("insta", $e))
				$external_req = $external_req."|insta";
		}

		if (!empty($value) && ($key == 'city' || $key == 'region' || $key == 'pays')) {
			$e = explode("|", $external_req);
			$local[$key] = $value;
			if (!in_array("local", $e))
				$external_req = $external_req."|local";
		}
	}
	return (array(0 => $external_req, 1 => $played, 2 => $local, 3 => $insta));
}

function requette($bdd, $tab) {
	$deb = 0;
	$req = "SELECT * FROM ".$bdd." WHERE";
	foreach ($tab as $key => $value) {
		if (!empty($value)) {
			$sort = explode("|", $value);
			if (count($sort) > 1) {
				$i = 0;
				if ($deb == 1)
					$is = " AND (";
				else
					$is = " (";
				while (isset($sort[$i + 1])) {
					if ($key == 'interets' || $key == 'game')
						$is = $is.$key." LIKE '%".$sort[$i]."%' OR ";
					else
						$is = $is.$key." = '".$sort[$i]."' OR ";
					$i++;
				}
				$is = $is.$key." = '".$sort[$i]."')";
			} else {
				if ($deb == 1) {
					if ($key == 'prenom' || $key == 'interets' || $key == 'game' || $key == 'city' || $key == 'region' || $key == 'pays')
						$is = " AND ".$key." LIKE '%".$value."%'";
					else if ($key == 'age_max' || $key == 'time_played') {
						$e = explode(" ", $value);
						$key = str_replace("_max", "", $key);
						$is = " AND ".$key." ".$e[0]." ".$e[1];
					} else if($key == 'age_min' || $key == 'nb_followers') {
						$key = str_replace("_min", "", $key);
						$e = explode(" ", $value);
						$is = " AND ".$key." ".$e[0]." ".$e[1];
					} else
					$is = " AND ".$key." = '".$value."'";
				} else {
					if ($key == 'prenom' || $key == 'interets' || $key == 'game' || $key == 'city' || $key == 'region' || $key == 'pays')
						$is = " ".$key." LIKE '%".$value."%'";
					else if ($key == 'age_max' || $key == 'time_played') {
						$key = str_replace("_max", "", $key);
						$e = explode(" ", $value);
						$is = " ".$key." ".$e[0]." ".$e[1];
					} else if($key == 'age_min' || $key == 'nb_followers') {
						$key = str_replace("_min", "", $key);
						$e = explode(" ", $value);
						$is = " ".$key." ".$e[0]." ".$e[1];
					} else
					$is = " ".$key." = '".$value."'";
				}
			}
			$req = $req.$is;
			$deb = 1;
		}
	}
	return ($req);
}


?>