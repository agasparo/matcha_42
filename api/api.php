<?php

session_start();

require_once('vendor/match.class.php');

//***********************Connection a l api*****************************//
$url_retour = 'http://192.168.99.100:41062/www/matcha/api/api.php';
$macth_init = new Match_connection();
if (isset($_GET['acces_token']) && !empty($_GET['acces_token'])) {
	$_SESSION['acces_token_matcha'] = $_GET['acces_token'];
	$_SESSION['pass_token'] = $_GET['pass_token'];
}
if (!isset($_SESSION['acces_token_matcha']) || empty($_SESSION['acces_token_matcha'])) {
	$url = 'http://192.168.99.100.xip.io:41062/www/matcha/api/connection.php?url='.urlencode($url_retour);
	echo "<a href='".$url."'>Se connecter avec matcha</a>";
} else {
	//***********************Connection a lespace user*****************************//
	$user = $macth_init->ini_user();
	$a = $user->connect_user();
	echo $user->get_user_prenom($a)."<br>";
	echo $user->get_user_nom($a)."<br>";
	echo $user->get_user_mail($a)."<br>";
	echo $user->get_user_sexe($a)."<br>";
	echo $user->get_user_attirance($a)."<br>";
	echo $user->get_user_description($a)."<br>";
	echo $user->get_user_photos($a)."<br>";
	echo $user->get_user_photos_pro($a)."<br>";
	echo $user->get_user_age($a)."<br>";
	echo $user->get_user_interets($a)."<br>";
	$localisation = $user->get_user_localisation($a);
	echo $user->local_city($localisation)."<br>";
	echo $user->local_region($localisation)."<br>";
	echo $user->local_pays($localisation)."<br>";
	$game = $user->get_user_game($a);
	echo $user->game_play($game)."<br>";
	$followers = $user->get_user_followers($a);
	echo $user->followers_play($followers)."<br>";
	$musique_user = $user->get_user_musique($a);
	echo $user->musique_play($musique_user)."<br>";
	echo "<br>------------------------------------------------------------------------------<br>";
	//***********************Connection a lespace musique*****************************//
	$musique = $macth_init->init_musique();
	$all_musique = $musique->get_all_musique();
	echo $musique->show_all_musique($all_musique);
	$id = 'J7GL8kO-DBw';
	$titre = '';
	echo $musique->get_musique($id, $titre, $all_musique);
	echo "<br>------------------------------------------------------------------------------<br>";
	//***********************Connection a lespace jeux*****************************//
	$jeux = $macth_init->init_jeux();
	echo $jeux->list_jeux();
}
?>