<?php
require_once('setup/database.php');
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
   header("Location:index.php");
else {
	$check_pro = $bdd->prepare("SELECT * FROM membre WHERE id = ?");
	$check_pro->execute(array($_SESSION['id']));
	$infs = $check_pro->fetch();

	foreach ($infs as $key => $value) {
		if (empty($value) && $key != 'oauth' && $key != 14) {
			header("Location:profil/".$infs['prenom'].$infs['nom']);
		}
	}
}
?>
<head>
	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="icon" type="image/png" href="photos/logo.png"/>
</head>
<style type="text/css">
	.leaflet-popup-content-wrapper {
		width: auto;
    	text-align: center;
	}
</style>
<?php

function create_profil($userinfos, $bdd) {
	if ($userinfos['sexe'] == "M")
		$sexe = "Homme";
	else if ($userinfos['sexe'] == "F")
		$sexe = "Femme";
	else
		$sexe = "Autre";
	if ($userinfos['attirance'] == "M")
		$att = "Homme";
	else if ($userinfos['attirance'] == "F")
		$att = "Femme";
	else
		$att = "Autre";

	$req_followers = $bdd->prepare('SELECT * FROM insta WHERE id_user = ?');
	$req_followers->execute(array($userinfos['id']));
	$nb_followers = $req_followers->fetch();
	if ($req_followers->rowCount() > 0)
		$ff = $nb_followers['nb_followers']." followers";
	else
		$ff = 'followers : none';


	$req_jeux = $bdd->prepare('SELECT * FROM played WHERE id_user = ?');
	$req_jeux->execute(array($userinfos['id']));
	$fj = "<select>";
	while ($jeux = $req_jeux->fetch()) {
		$fj = $fj."<option>".str_replace('"', '', $jeux['game'])." --> ".floor(intval($jeux['time_played']) / 60)." heures"."</option>";
	}
	if ($fj == "<select>")
		$fj = "<select><option>none</option>";
	$fj = $fj."</select>";


	$req_local = $bdd->prepare('SELECT * FROM local WHERE id_user = ?');
	$req_local->execute(array($userinfos['id']));
	$local = $req_local->fetch();
	$fl = 'ville : '.str_replace('"', '', $local["city"]).' ('.str_replace('"', '', $local["pays"]).')';

	$get_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p2 = ? OR id_p1 = ? AND match_p1 = ? AND match_p2 = ?');
	$get_match->execute(array($userinfos['id'], $userinfos['id'], 1, 1));
	$matchs = $get_match->rowCount();

	if ($matchs < 10)
		$grade = 'Novice &#x270C;';
	if ($matchs >= 10 && $matchs <= 50) {
		$grade = 'Pro &#x2764;';
	}
	if ($matchs > 50) {
		$grade = "Bg &#128536;";
	}

	if ($_SESSION['id'] == $userinfos['id']) {
		$name = $userinfos['prenom']." (vous)";
	} else {
		$name = $userinfos['prenom'];
	}

	if ($_SESSION['id'] == $userinfos['id']) {
		$body = "<td class='pointer'>Signaler</td><td class='pointer'>Bloquer</td>";
	} else {
		$body = "<td class='pointer signal' id='".$userinfos["id"]."'>Signaler</td>";
		$list_bloque = bloquer($bdd);
		if (in_array($userinfos['id'], $list_bloque)) {
			$body = $body."<td class='pointer bloq' id='".$userinfos['id']."'>Debloquer</td>";
		} else {
			$body = $body."<td class='pointer bloq' id='".$userinfos['id']."'>Bloquer</td>";
		}
	}

	$get_connect = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
	$get_connect->execute(array($userinfos['id']));

	$time = "0";

	if ($get_connect->rowCount() > 0) {
		$connect = $get_connect->fetch();
		if ($connect['co'] == 1)
			$time = "<span style='background: green; position: relative; width: 11px;height: 11px;border-radius: 50%;'></span>";
		else {
			$time_now = time();
			$pass = $time_now - $connect['co'];
			$get_val_time = explode(':', gmdate("H:i:s", $pass));
			$pas = "";
			$i = 0;
			while(isset($get_val_time[$i])) {
				$get_val_time[$i] = str_replace('0', '', $get_val_time[$i]);
				$i++;
			}
			if (!empty($get_val_time[0])) {
				$pas = return_date($get_val_time[0], 'heures');
			} else {
				if (!empty($get_val_time[1])) {
					$pas = return_date($get_val_time[1], 'minutes');
				} else {
					if (!empty($get_val_time[2])) {
						$pas = return_date($get_val_time[2], 'secondes');
					} else {
						$pas = "0 secondes";
					}
				}
			}
			$time = "<span style='background: red; position: relative; width: 11px;height: 11px;border-radius: 50%;'></span><span style='font-size: 1vh;''>Connecte il y a ".$pas."</span>";
		}
	}

	if (preg_match("#https://#", $userinfos['photos_pro']) || preg_match("#http://#", $userinfos['photos_pro'])) {
		$userinfos['photos_pro'] =$userinfos['photos_pro'];
	} else {
		$userinfos['photos_pro'] = 'photos/'.$userinfos['photos_pro'];
	}

	return("<h3>".$name.$time."</h3><div class='black_map'><img src='".$userinfos['photos_pro']."' style='height: 100%; width: auto; max-width: 100%;'></div><br><br><table class='table table-hover'><tr><td>Age : ".$userinfos['age']." ans</td><td>Sexe : ".$sexe."</td><td>Attirance : ".$att."</td></tr><tr><td>".$ff."</td><td>".$fj."</td><td>".$fl."</td></tr><tr><td>Grade : ".$grade."</td>".$body."</tr><table><div>");
}

function return_date($val, $txt) {
	if ($val > 1) {
		return ($val." ".$txt);
	} else {
		return ($val." ".str_replace($txt[strlen($txt) - 1], '', $txt));
	}
}

function set_match($userinfos, $bdd) {
	$st = "";
	if ($_SESSION['id'] != $userinfos['id']) {
		$list_bloque = bloquer($bdd);
		if (in_array($userinfos['id'], $list_bloque)){
			$st = "<font class='matchs pointer' id='".$userinfos['id']."matchs'></font></p>";
		} else {
			$is_match = $bdd->prepare('SELECT * FROM matchs WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?) AND match_p1 = ? AND match_p2 = ?');
			$is_match->execute(array($_SESSION['id'], $userinfos['id'], $_SESSION['id'], $userinfos['id'], 1, 1));
			if ($is_match->rowCount() == 1) {
				$st = "<font class='msg pointer' id='".$userinfos['id']."matchs'>Envoyer un message</font></p>";
			} else {
				$is_match = $bdd->prepare('SELECT * FROM matchs WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?)');
				$is_match->execute(array($_SESSION['id'], $userinfos['id'], $_SESSION['id'], $userinfos['id']));
				if ($is_match->rowCount() == 0) {
					$st = "<font class='matchs pointer' id='".$userinfos['id']."matchs'>match</font></p>";
				} else {
					$inf = $is_match->fetch(0);
					if ($inf['id_p1'] == $_SESSION['id'] AND $inf['match_p1'] == 0) {
						$st = "<font class='matchs pointer' id='".$userinfos['id']."matchs'>matcher en retour</font></p>";
					} else if ($inf['id_p2'] == $_SESSION['id'] AND $inf['match_p2'] == 0) {
						$st = "<font class='matchs pointer' id='".$userinfos['id']."matchs'>matcher en retour</font></p>";
					} else {
						$st = "<font class='matchs pointer' id='".$userinfos['id']."matchs'>match</font></p>";
					}
				}
			}
		}
	}
	return ($st);
}

function bloquer($bdd) {
	$is_bloq = $bdd->prepare("SELECT * FROM bloquer WHERE id_bloqueur = ? OR id_bloquer = ?");
	$is_bloq->execute(array($_SESSION['id'], $_SESSION['id']));
	$list_bloque = [];
	$i = 0;
	while ($get_bloque = $is_bloq->fetch()) {
		if ($_SESSION['id'] == $get_bloque['id_bloquer'])
			$list_bloque[$i] = $get_bloque['id_bloqueur'];
		else
			$list_bloque[$i] = $get_bloque['id_bloquer'];
		$i++;
	}
	return ($list_bloque);
}

$fd = fopen("js/data.js", "w+");
$header = "window.onload = function() {
	var mymap = L.map('mapid').setView([51.505, -0.09], 3);

	L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
		maxZoom: 30,
	}).addTo(mymap);";
	$mid = "";
	$bloquer = 
	$req = $bdd->query("SELECT * FROM local");
	while ($inf = $req->fetch()) {
		$req_user = $bdd->prepare("SELECT * FROM membre WHERE id = ?");
		$req_user->execute(array($inf['id_user']));
		$userinfos = $req_user->fetch();
		if (!in_array($userinfos['id'], bloquer($bdd))){
			if (preg_match("#https://#", $userinfos['photos_pro']))
				$img = "'".$userinfos['photos_pro']."'";
			else
				$img = "'photos/".$userinfos['photos_pro']."'";
			$mid = $mid."
			var myIcon = L.icon({
			iconUrl: ".$img.",
			iconSize: [38, 38],
			iconAnchor: [22, 22],
			popupAnchor: [-3, -3],
			className: ".$userinfos['id']."
			});
			var marker = L.marker([".$inf['lat'].", ".$inf['lon']."], {icon: myIcon}).addTo(mymap);";
			$mid = $mid."marker.bindPopup(\""."<div id='".$userinfos['id']."_map'>".create_profil($userinfos, $bdd).set_match($userinfos, $bdd)."</div>\");";
			if ($_SESSION['id'] == $userinfos['id']) {
				$mid = $mid."\nvar circle = L.circle([".$inf['lat'].", ".$inf['lon']."], {
				color: '#F57A00',
				fillColor: '#EB6864',
				fillOpacity: 0.5,
				radius: 500
				}).addTo(mymap);
				";
			}
		}
	}
	$footer = "\n};";
	fwrite($fd, $header.$mid.$footer);
	fclose($fd);
			?>
			<head>
				<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
				integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
				crossorigin=""/>
				<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
				integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
				crossorigin=""></script>
				<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
				<link rel="stylesheet" type="text/css" href="css/style.css">
				<title>Matcha - carte</title>
			</head>
			<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
				<a class="navbar-brand" href="#">Carte</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon" id="small_btn"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarColor01">
					<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="index.php" id="home">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?= 'profil/'.$infs['prenom'].$infs['nom'];?>">Profil</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#" id="notif_bar">Notifications</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="parametres.php">Parametres</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="recherche.php">Recherche</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="deco.php">Se deconnecter</a>
					</li>
				</ul>
				</div>
			</nav>
			<ul class="list-group" id="notif_menu">
				<div id="notif_php">
					<?php
					if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
						?><div id="cache"></div><?php
					}

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
					<li class="list-group-item d-flex justify-content-between align-items-center notifications">
						Messages
						<span class="badge badge-primary badge-pill"><?= $count_messages; ?></span>
					</li>
					<li class="list-group-item d-flex justify-content-between align-items-center notifications">
						Matchs
						<span class="badge badge-primary badge-pill"><?= $count_match; ?></span>
					</li>
					<li class="list-group-item d-flex justify-content-between align-items-center notifications">
						Visites
						<span class="badge badge-primary badge-pill"><?= $count_visites; ?></span>
					</li>
				</div>
			</ul>
			<div id="notif_show_all" class="notif_show_all"></div>
			<div id="small_menu">
			<div class="small_meu">
				<a class="nav-link" href="index.php">Home</a>
				<a class="nav-link" href="<?= 'profil/'.$infs['prenom'].$infs['nom'];?>" id="profil_bar">Mon profil</a>
				<a class="nav-link" href="#" id="notif_bar1">Notifications</a>
				<div id="notif_php1" style="display: none;">
						<?php
						if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
							?><div id="cache"></div><?php
						}

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
					</div>
					<a class="nav-link" href="parametres.php">Parametres</a>
					<a class="nav-link" href="recherche.php">Recherche</a>
					<a class="nav-link" href="deco.php">Se deconnecter</a>
				</div>
			</div>
			<div class="musique_lecteur" id="musique_lecteur"><?php include('lecteur/lecteur.html'); ?><div class="in_ou_m"><p id="close_open_lecteur">ouvrir / fermer la fenetre</p></div></div>
			<div class="alert alert-dismissible alert-success" style="display: none;" id="alert_sign">
			Signalement envoye
		</div>
		<div class="alert alert-dismissible alert-success" style="display: none;" id="alert_bloq">
		</div>
		<div class="alert alert-dismissible alert-primary" style="display: none;" id="alert_allready_match">
		</div>
			<div id="mapid" style="
			position: relative;
			left: 0;
			top: 0;
			max-height: 95.15%;
			min-height: 95.15%;
			max-width: 100%;
			min-width: 100%;
			z-index: 2;
			">
		</div>
		<div id="notif_show_mobil"></div>
		<div id="chat_mes"></div>
		<script type="text/javascript" src="js/data.js"></script>
		<script type="text/javascript" src="js/carte.js"></script>