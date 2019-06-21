<?php

require "vendor/autoload.php";
require_once('../setup/database.php');  
session_start();

$auth = new Instagram\Auth([
	'client_id'     => '',
	'client_secret' => '',
	'redirect_uri'  => 'http://192.168.99.100.xip.io:41062/www/matcha/insta_connexion/index.php'
]);

if(!isset($_SESSION['instagram_token'])){
	if(!isset($_GET['code'])){
		$auth->authorize();
	} else {
		$access_token = $auth->getAccessToken($_GET['code']);
		$_SESSION['instagram_token'] = $access_token;
	}
}

$instagram = new Instagram\Instagram();
$instagram->setAccessToken($_SESSION['instagram_token']);
$current_user = $instagram->getCurrentUser();

$user_login = $current_user->getFullName()." ";
$user_bio = $current_user->getBio()." ";
$user_picture_pro = $current_user->getProfilePicture()." ";
$user_follow = $current_user->getFollowersCount()." ";
$medias = $current_user->getMedia(['count' => 4]);
$imgs = $user_picture_pro;
foreach($medias as $media){
	if ($media->type == 'image') {
		$imgs = $imgs.'{'.$media->images->standard_resolution->url;
	}
}

$req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
$req_bdd->execute(array($user_login));
if ($req_bdd->rowCount() > 0) {
	$get_user = $req_bdd->fetch();
	if ($get_user['oauth'] == 1) {
		$is_connected = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
		$is_connected->execute(array($get_user['id']));
		if ($is_connected->rowCount() > 0) {
			$connected = $bdd->prepare('UPDATE connected SET co = ? WHERE id_user = ?');
			$connected->execute(array(1, $get_user['id']));
		} else {
			$connected = $bdd->prepare('INSERT INTO connected(id_user, co) VALUES(?, ?)');
			$connected->execute(array($get_user['id'], 1));
		}
		$_SESSION['id'] = $get_user['id'];
		header("Location:../index.php");
	} else {
		$a = 0;
		$i = 1;
		while ($a == 0) {
			$user_login = $user_login.$i;
			$req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
			$req_bdd->execute(array($user_login));
			if ($req_bdd->rowCount() == 0)
				$a = 1;
			$i++;
		}
		$comma = explode(" ", $user_login);
		$req = $bdd->prepare("INSERT INTO membre(nom, prenom, mail, mdp, sexe, attirance, description, photos, photos_pro, age, verif, nom_utilisteur, interets, oauth) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$req->execute(array($comma[1], $comma[0], "", sha1(create_mdp(rand(6, 20))), 'A', 'A', $user_bio, $user_picture_pro, $imgs, 0, 1, $user_login, 0, 1));

		$req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
		$req_bdd->execute(array($user_login));
		$get_user = $req_bdd->fetch();
		$_SESSION['id'] = $get_user['id'];
		$i = 0;
		$req_game = $bdd->prepare('INSERT INTO insta(id_user, nb_followers) VALUES(?, ?)');
		$req_game->execute(array($get_user['id'], $user_follow));
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
	$comma = explode(" ", $user_login);
	$req = $bdd->prepare("INSERT INTO membre(nom, prenom, mail, mdp, sexe, attirance, description, photos, photos_pro, age, verif, nom_utilisteur, interets, oauth) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$req->execute(array($comma[1], $comma[0], "", sha1(create_mdp(rand(6, 20))), 'A', 'A', $user_bio, $user_picture_pro, $imgs, 0, 1, $user_login, 0, 1));

	$req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
	$req_bdd->execute(array($user_login));
	$get_user = $req_bdd->fetch();
	$_SESSION['id'] = $get_user['id'];
	$i = 0;
	$req_game = $bdd->prepare('INSERT INTO insta(id_user, nb_followers) VALUES(?, ?)');
	$req_game->execute(array($get_user['id'], $user_follow));
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
?>
