<?php

require_once('setup/database.php');
extract($_POST);
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$req_user = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
$req_user->execute(array($identifiant));
$userinfos = $req_user->fetch();

if ($userinfos['id'] !== $_SESSION['id']) {
	$req_histo = $bdd->prepare('INSERT INTO historique(id_visiter, id_visiteur, vu) VALUES(?, ?, ?)');
	$req_histo->execute(array($userinfos['id'], $_SESSION['id'], 0));
}

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
	$fj = $fj."<option>".$jeux['game']." --> ".floor(intval($jeux['time_played']) / 60).' heures'."</option>";
}
if ($fj == "<select>")
	$fj = '<select><option>none</option>';
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

$get_connect = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
$get_connect->execute(array($userinfos['id']));

$time = 0;

if ($get_connect->rowCount() > 0) {
	$connect = $get_connect->fetch();
	if ($connect['co'] == 1)
		$time = '<span style="background: green; position: relative; width: 11px;height: 11px;border-radius: 50%;"></span>';
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
		$time = '<span style="background: red; position: relative; width: 11px;height: 11px;border-radius: 50%;"></span><span style="font-size: 1vh;">Connecte il y a '.$pas.'</span>';
	}
}

function return_date($val, $txt) {
	if ($val > 1) {
		return ($val." ".$txt);
	} else {
		return ($val." ".str_replace($txt[strlen($txt) - 1], '', $txt));
	}
}


?><h3><?php if ($_SESSION['id'] == $userinfos['id']) { echo $userinfos['prenom']." (vous) ".$time; } else { echo $userinfos['prenom']." ".$time; } ?></h3>
<p id="close" style="position: absolute; right: 1%; top: 0.2%; cursor: pointer;">X</p>
<div class="black">
	<?php
	if(preg_match("#https://#", $userinfos['photos_pro']) || preg_match("#http://#", $userinfos['photos_pro'])) {
		?><img src="<?= $userinfos['photos_pro']; ?>" style='height: 100%; width: auto; max-width: 100%;'><?php
	} else {
		?><img src="<?= 'photos/'.$userinfos['photos_pro']; ?>" style='height: 100%; width: auto; max-width: 100%;'><?php
	}
	?>
</div><br><br>
<table class='table table-hover'>
	<tr>
		<td>Age : <?= $userinfos['age']; ?> ans</td>
		<td>Sexe : <?= $sexe; ?></td>
		<td>Attirance : <?= $att; ?></td>
	</tr>
	<tr>
		<td><?= $ff; ?></td>
		<td><?= $fj; ?></td>
		<td><?= $fl; ?></td>
	</tr>
	<tr>
		<td>Grade : <?= $grade; ?></td>
		<?php 
		if ($_SESSION['id'] == $userinfos['id']) {
			?><td class="pointer">Signaler</td><?php
			?><td class="pointer">Bloquer</td><?php
		} else {
			?><td class="pointer signal" id="<?= $userinfos['id']; ?>">Signaler</td><?php
			$list_bloque = bloquer($bdd);
			if (in_array($userinfos['id'], $list_bloque)) {
				?><td class="pointer bloq" id="<?= $userinfos['id']; ?>">Debloquer</td><?php
			} else {
				?><td class="pointer bloq" id="<?= $userinfos['id']; ?>">Bloquer</td><?php
			}
		} 

		?>
	</tr>
<table>
<div>
	<?php
	$imgs = explode("|", $userinfos['photos']);
	$i = 0;
	while (isset($imgs[$i])) {
		if (!empty($imgs[$i])) {
			if(preg_match("#https://#", $imgs[$i]) || preg_match("#http://#", $imgs[$i])) {
				?><img src="<?= $imgs[$i]; ?>" style='position: relative; margin-left: 1%; float: left; width: auto; height: 20%;'><?php
			} else {
				?><img src="<?= 'photos/'.$imgs[$i]; ?>" style='position: relative; margin-left: 1%; float: left; width: auto; height: 20%;'><?php
			}
		}
		$i++;
	}
	?>
</div>
<?php

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

if ($_SESSION['id'] != $userinfos['id']) {
	$list_bloque = bloquer($bdd);
	if (in_array($userinfos['id'], $list_bloque)) {
		?><p style="position: absolute;width: 100%; text-align: center; font-size: 1.5vw; bottom: 0;"><?= $userinfos['description']; ?><br><?= $userinfos['interets']; ?><font class='matchs pointer' id="<?= $userinfos['id'].'matchs'; ?>"></font></p><?php
	} else {
		$is_match = $bdd->prepare('SELECT * FROM matchs WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?) AND match_p1 = ? AND match_p2 = ?');
		$is_match->execute(array($_SESSION['id'], $userinfos['id'], $_SESSION['id'], $userinfos['id'], 1, 1));
		if ($is_match->rowCount() == 1) {
			?><p style="position: absolute;width: 100%; text-align: center; font-size: 1.5vw; bottom: 0;"><?= $userinfos['description']; ?><br><?= $userinfos['interets']; ?><br><font class='msg pointer' id="<?= $userinfos['id'].'matchs'; ?>">Envoyer un message</font></p><?php
		} else {
			$is_match = $bdd->prepare('SELECT * FROM matchs WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?)');
			$is_match->execute(array($_SESSION['id'], $userinfos['id'], $_SESSION['id'], $userinfos['id']));
			if ($is_match->rowCount() == 0) {
				?><p style="position: absolute;width: 100%; text-align: center; font-size: 1.5vw; bottom: 0;"><?= $userinfos['description']; ?><br><?= $userinfos['interets']; ?><br><font class='matchs pointer' id="<?= $userinfos['id'].'matchs'; ?>">match</font></p><?php
			} else {
				$inf = $is_match->fetch(0);
				if ($inf['id_p1'] == $_SESSION['id'] AND $inf['match_p1'] == 0) {
					?><p style="position: absolute;width: 100%; text-align: center; font-size: 1.5vw; bottom: 0;"><?= $userinfos['description']; ?><br><?= $userinfos['interets']; ?><br><font class='matchs pointer' id="<?= $userinfos['id'].'matchs'; ?>">matcher en retour</font></p><?php
				} else if ($inf['id_p2'] == $_SESSION['id'] AND $inf['match_p2'] == 0) {
					?><p style="position: absolute;width: 100%; text-align: center; font-size: 1.5vw; bottom: 0;"><?= $userinfos['description']; ?><br><?= $userinfos['interets']; ?><br><font class='matchs pointer' id="<?= $userinfos['id'].'matchs'; ?>">matcher en retour</font></p><?php
				} else {
					?><p style="position: absolute;width: 100%; text-align: center; font-size: 1.5vw; bottom: 0;"><?= $userinfos['description']; ?><br><?= $userinfos['interets']; ?><br><font class='matchs pointer' id="<?= $userinfos['id'].'matchs'; ?>">match</font></p><?php
				}
			}
		}
	}
}
?>