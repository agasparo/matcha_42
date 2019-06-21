<?php

require_once('setup/database.php');
session_start();
extract($_POST);

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
	echo "pas de sessions";
	exit(0);
}

$count_per = $bdd->query('SELECT * FROM membre');

if ($profil_min >= $count_per->rowCount() - 1) {
	$_SESSION['profils'] = "";
	$profil_min = 0;
}

require 'create_attente.php';

$req_get_user = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
$req_get_user->execute(array($_SESSION['profils'][$profil_min]));
$profil_to_show = $req_get_user->fetch();

$profil_min++;

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
if ($matchs >= 10 && $matchs <= 50) {
	$grade = 'Pro &#x2764;';
}
if ($matchs > 50) {
	$grade = "Bg &#128536;";
}

$erreur = '';

function isnt_bloquer($bdd, $profil_to_show, $debu) {
	$es = 0;
	if ($debu == 0)
		return (1);
	$bloc = $bdd->prepare('SELECT * FROM bloquer WHERE id_bloqueur = ? OR id_bloquer = ?');
	$bloc->execute(array($_SESSION['id'], $profil_to_show['id']));
	if ($bloc->rowCount() == 0)
		$es = 1;
	if ($es == 1) {
		$bloc = $bdd->prepare('SELECT * FROM bloquer WHERE id_bloqueur = ? OR id_bloquer = ?');
		$bloc->execute(array($profil_to_show['id'], $_SESSION['id']));
		if ($bloc->rowCount() == 0)
			return (1);
		else
			return (0);
	}
	return (0);
}

if ($id == 1 && isnt_bloquer($bdd, $profil_to_show, $debu)) {

	$user_barre_exist = $bdd->prepare("SELECT * FROM barre_matchs WHERE id_user = ?");
	$user_barre_exist->execute(array($_SESSION['id']));
	$count_u = $user_barre_exist->rowCount();
	$get_time = $user_barre_exist->fetch();
	if ($count_u < 1) {
		$t = explode("%", $taille);
		$insert_barre = $bdd->prepare('INSERT INTO barre_matchs(id_user, val_barre, time_barre) VALUES(?, ?, ?)');
		$insert_barre->execute(array($_SESSION['id'], $t[0], 0));
	} else {
		$t = explode("%", $taille);
		if ($get_time['time_barre'] == 0) {
			$update_barre = $bdd->prepare("UPDATE barre_matchs SET val_barre = ?, time_barre = ? WHERE id_user = ?");
			$update_barre->execute(array($t[0], time(), $_SESSION['id']));
		} else {
			$update_barre = $bdd->prepare("UPDATE barre_matchs SET val_barre = ? WHERE id_user = ?");
			$update_barre->execute(array($t[0], $_SESSION['id']));
		}
	}

	$get_matchs_user = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND id_p2 = ?');
	$get_matchs_user->execute(array($_SESSION['id'], $profil_to_show['id']));
	$count = $get_matchs_user->rowCount();
	if ($count > 0) {
		$t = explode("%", $taille);
		$tai = intval($t[0]) + 10;
		if ($get_time['time_barre'] == 0) {
			$update_barre = $bdd->prepare("UPDATE barre_matchs SET val_barre = ?, time_barre = ? WHERE id_user = ?");
			$update_barre->execute(array($tai, time(), $_SESSION['id']));
		} else {
			$update_barre = $bdd->prepare("UPDATE barre_matchs SET val_barre = ? WHERE id_user = ?");
			$update_barre->execute(array($tai, $_SESSION['id']));
		}
		$erreur = "deja_match";
	} else {
		if ($debu == 0) {
			$t = explode("%", $taille);
			$tai = intval($t[0]) + 10;
			if ($get_time['time_barre'] == 0) {
				$update_barre = $bdd->prepare("UPDATE barre_matchs SET val_barre = ?, time_barre = ? WHERE id_user = ?");
				$update_barre->execute(array($tai, time(), $_SESSION['id']));
			} else {
				$update_barre = $bdd->prepare("UPDATE barre_matchs SET val_barre = ? WHERE id_user = ?");
				$update_barre->execute(array($tai, $_SESSION['id']));
			}
			$erreur = 'auto_m';
		} else {
			$get_matchs_user = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND id_p2 = ? AND match_p2 = ?');
			$get_matchs_user->execute(array($profil_to_show['id'], $_SESSION['id'], 1));
			$count = $get_matchs_user->rowCount();
			if ($count > 0) {
				$erreur = "deja_match";
			} else {
				$get_matchs_user = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND id_p2 = ?');
				$get_matchs_user->execute(array($profil_to_show['id'], $_SESSION['id']));
				$count = $get_matchs_user->rowCount();
				if ($count > 0) {
					$update_matchs = $bdd->prepare("UPDATE matchs SET match_p2 = ?, vu_p1 = ? WHERE id_p1 = ? AND id_p2 = ?");
					$update_matchs->execute(array(1, 0, $profil_to_show['id'], $_SESSION['id']));
					$erreur = "env_match";
				} else {
					$insert_match = $bdd->prepare('INSERT INTO matchs(id_p1, id_p2, match_p1, match_p2, vu_p1, vu_p2) VALUES(?, ?, ?, ?, ?, ?)');
					$insert_match->execute(array($_SESSION['id'], $profil_to_show['id'], 1, 0, 0, 0));
					$erreur = "env_match";
				}
			}
		}
	}
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

$bloq = "<font class='pointer signal' id='".$profil_to_show['id']."'>Signaler</font>";
$list_bloque = bloquer($bdd);
if (in_array($profil_to_show['id'], $list_bloque)) {
	$bloq .= " / <font class='pointer bloq' id='".$profil_to_show['id']."'>Debloquer</font>";
} else {
	$bloq .= " / <font class='pointer bloq' id='".$profil_to_show['id']."'>Bloquer</font>";
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

if ($profil_min >= $count_per->rowCount() - 1) {
	$_SESSION['profils'] = "";
	$profil_min = 0;
}

echo $profil_min."{";
echo $profil_to_show['sexe']."$".$profil_to_show['attirance']."$".$profil_to_show['description']."$".$profil_to_show['age']."$".$profil_to_show['photos']."$".$grade."$".$follow['nb_followers']."$".$profil_to_show['interets']."$".$games."$".$bloq."$".$erreur."{";

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
			<a class="nav-link" data-toggle="tab" href="#" id="home">Informations</a>
		</li>
		<li class="nav-item">
			<a class="nav-link active show" data-toggle="tab" href="#" id="profile">Photos</a>
		</li>
	</ul>
	<div id="myTabContent" class="tab-content"></div>
</div>