<?php

require_once('setup/database.php');
session_start();
extract($_POST);

if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
	$req_get_user = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
	$req_get_user->execute(array($_SESSION['id']));
	$profil_to_show = $req_get_user->fetch();
	if (!preg_match("#https://#", $profil_to_show['photos_pro']) && !preg_match("#https://#", $profil_to_show['photos_pro']))
		$profil_to_show['photos_pro'] = "photos/".$profil_to_show['photos_pro'];
} else {
	$req_get_user = $bdd->query('SELECT * FROM membre ORDER BY RAND() LIMIT 1');
	$profil_to_show = $req_get_user->fetch();
}

$get_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND match_p1 = ? AND match_p2 = ?');
$get_match->execute(array($profil_to_show['id'], 1, 1));
$matchs = $get_match->rowCount();
if ($matchs == 0) {
	$get_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p2 = ? AND match_p1 = ? AND match_p2 = ?');
	$get_match->execute(array($profil_to_show['id'], 1, 1));
	$matchs = $get_match->rowCount();
}

if ($matchs < 10)
	$grade = 'Novice &#x270C;';
if ($matchs > 10 && $matchs <= 50) {
	$grade = 'Pro &#x2764;';
}
if ($matchs > 50) {
	$grade = "Bg &#128536;";
}

$get_follow = $bdd->prepare('SELECT * FROM insta WHERE id_user = ?');
$get_follow->execute(array($profil_to_show['id']));
$follow = $get_follow->fetch();

$get_game = $bdd->prepare('SELECT * FROM played WHERE id_user = ?');
$get_game->execute(array($profil_to_show['id']));
$games = "<select size='1' class='form-control font'>";
while ($game = $get_game->fetch()) {
	$games = $games."<option>".$game['game']." --> ".(round(intval($game['time_played']) / 60))." heures</option>";
}
if ($games == "<select size='1' class='form-control'>")
	$games = $games."<option>none</option>";
$games = $games."</select>";

echo $profil_to_show['id']."{".$profil_to_show['sexe']."$".$profil_to_show['attirance']."$".$profil_to_show['description']."$".$profil_to_show['age']."$".$profil_to_show['photos']."$".$grade."$".$follow['nb_followers']."$".$profil_to_show['interets']."$".$games.'$signaler / bloquer'."{";
if (preg_match('#https://#', $profil_to_show['photos_pro']) || preg_match('#http://#', $profil_to_show['photos_pro']))
	$imgs = $profil_to_show['photos_pro'];
else
	$imgs = 'photos/'.$profil_to_show['photos_pro'];
?>
<h3 style="color: #EB6864; text-align: center;"><?= $profil_to_show['prenom']; ?></h3>
<div class="img_pro">
	<div align="center">
		<img src="<?= $imgs; ?>" style="width: auto; max-width: 100%; height: 100%;">
	</div>
</div>
<div class="carte_txt">
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active show" data-toggle="tab" href="#" id="home">Informations</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#" id="profile">Photos</a>
		</li>
	</ul>
	<div id="myTabContent" class="tab-content"></div>
</div>