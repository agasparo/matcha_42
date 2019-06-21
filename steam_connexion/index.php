<?php
require ('steamauth/steamauth.php');
require_once('../setup/database.php');  
?>
<!DOCTYPE html>
<head>
	<meta charset="utf-8">
</head>
<?php

function create_mdp($length) {
	$caracteres = 'abcdefghijklmnopqrstuvwxyz123456789!@#$%^&*';
	$longueurMax = strlen($caracteres);
	$chaineAleatoire = '';
	$i = 0;
	while ($i < $length) {
		$chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
		$i++;
	}
	return ($chaineAleatoire);
}

if(!isset($_SESSION['steamid'])) {
	loginbutton();
}  else {
	include ('steamauth/userInfo.php');
	require 'steamauth/SteamConfig.php';

	$urls = file_get_contents("https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=".$steamauth['apikey']."&steamid=".$steamprofile['steamid']."&format=json"); 
	$contents = json_decode($urls, true);
	$req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
	$req_bdd->execute(array($steamprofile['personaname']));
	if ($req_bdd->rowCount() > 0) {
		$get_user = $req_bdd->fetch();
		if ($get_user['oauth'] == 1) {
			$_SESSION['id'] = $get_user['id'];
			$is_connected = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
			$is_connected->execute(array($get_user['id']));
			if ($is_connected->rowCount() > 0) {
				$connected = $bdd->prepare('UPDATE connected SET co = ? WHERE id_user = ?');
				$connected->execute(array(1, $get_user['id']));
			} else {
				$connected = $bdd->prepare('INSERT INTO connected(id_user, co) VALUES(?, ?)');
				$connected->execute(array($get_user['id'], 1));
			}
			header("Location:../index.php");
		} else {
			$a = 0;
			$i = 1;
			while ($a == 0) {
				$steamprofile['personaname'] = $steamprofile['personaname'].$i;
				$req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
				$req_bdd->execute(array($steamprofile['personaname']));
				if ($req_bdd->rowCount() == 0)
					$a = 1;
				$i++;
			}
			$req = $bdd->prepare("INSERT INTO membre(nom, prenom, mail, mdp, sexe, attirance, description, photos, photos_pro, age, verif, nom_utilisteur, interets, oauth) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$req->execute(array("", $steamprofile['personaname'], "", sha1(create_mdp(rand(6, 20))), 'A', 'A', 0, $steamprofile['avatarfull'], $steamprofile['avatarfull'], 0, 1, $steamprofile['personaname'], 0, 1));

			$req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
			$req_bdd->execute(array($steamprofile['personaname']));
			$get_user = $req_bdd->fetch();
			$_SESSION['id'] = $get_user['id'];
			$i = 0;
			while (isset($contents['response']['games'][$i])) {
				$req_game = $bdd->prepare('INSERT INTO played(id_user, game, time_played) VALUES(?, ?, ?)');
				$req_game->execute(array($get_user['id'], $contents['response']['games'][$i]['name'], $contents['response']['games'][$i]['playtime_forever']));
				$i++;
			}
			$is_connected = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
			$is_connected->execute(array($get_user['id']));
			if ($is_connected->rowCount() > 0) {
				$connected = $bdd->prepare('UPDATE connected SET co = ? WHERE id_user = ?');
				$connected->execute(array(1, $get_user['id']));
			} else {
				$connected = $bdd->prepare('INSERT INTO connected(id_user, co) VALUES(?, ?)');
				$connected->execute(array($get_user['id'], 1));
			}
			header("Location:../index.php");
		}
	} else {
		$req = $bdd->prepare("INSERT INTO membre(nom, prenom, mail, mdp, sexe, attirance, description, photos, photos_pro, age, verif, nom_utilisteur, interets, oauth) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$req->execute(array("", $steamprofile['personaname'], "", sha1(create_mdp(rand(6, 20))), 'A', 'A', 0, $steamprofile['avatarfull'], $steamprofile['avatarfull'], 0, 1, $steamprofile['personaname'], 0, 1));

		$req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
		$req_bdd->execute(array($steamprofile['personaname']));
		$get_user = $req_bdd->fetch();
		$_SESSION['id'] = $get_user['id'];
		$i = 0;
		while (isset($contents['response']['games'][$i])) {
			$req_game = $bdd->prepare('INSERT INTO played(id_user, game, time_played) VALUES(?, ?, ?)');
			$req_game->execute(array($get_user['id'], $contents['response']['games'][$i]['name'], $contents['response']['games'][$i]['playtime_forever']));
			$i++;
		}
		$is_connected = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
		$is_connected->execute(array($get_user['id']));
		if ($is_connected->rowCount() > 0) {
			$connected = $bdd->prepare('UPDATE connected SET co = ? WHERE id_user = ?');
			$connected->execute(array(1, $get_user['id']));
		} else {
			$connected = $bdd->prepare('INSERT INTO connected(id_user, co) VALUES(?, ?)');
			$connected->execute(array($get_user['id'], 1));
		}
		header("Location:../index.php");
	}
}
?>
