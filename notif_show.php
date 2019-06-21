<?php

require_once('setup/database.php');
extract($_POST);
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

if ($type == "Messages")
	echo get_messages($bdd, 'message', 'message', 'id_envoyeur');
else if ($type == "Matchs")
	echo get_matchs($bdd, 'matchs');
else if ($type == "Visites")
	echo get_visites($bdd, 'historique', 'id_visiteur');
else
	exit(0);

function get_visites($bdd, $table, $exeption) {
	$st = "";
	$req_all = $bdd->prepare('SELECT * FROM '.$table.' WHERE id_visiter = ? AND vu = ?');
	$req_all->execute(array($_SESSION['id'], 0));
	if ($req_all->rowCount() > 0) {
		while($rep = $req_all->fetch()) {
			$update = $bdd->prepare('UPDATE '.$table.' SET vu = ? WHERE id_visiter = ?');
			$update->execute(array(1, $_SESSION['id']));
			if ($rep[$exeption] != $_SESSION['id']) {
				$user = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
				$user->execute(array($rep['id_visiteur']));
				$userinfos = $user->fetch();
				$st = $st.'
				<div class="list-group">
				<a href="#" class="list-group-item list-group-item-action">'.$userinfos['prenom'].' '.$userinfos['nom'].' a visite votre profil</a>
				</div>';
			}
		}
	} else {
		$st = '
		<div class="list-group">
		<a href="#" class="list-group-item list-group-item-action">Aucune nouvelle visite</a>
		</div>';
	}
	return ($st);
}

function get_messages($bdd, $table, $cible, $exeption) {
	$st = "";
	$req_all = $bdd->prepare('SELECT * FROM '.$table.' WHERE id_p2 = ? AND vu = ?');
	$req_all->execute(array($_SESSION['id'], 0));
	if ($req_all->rowCount() > 0) {
		while($rep = $req_all->fetch()) {
			if ($rep[$exeption] != $_SESSION['id']) {
				$user = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
				$user->execute(array($rep['id_p1']));
				$userinfos = $user->fetch();
				if (preg_match("#class='div_site'#", $rep[$cible]))
					$msg = "<br>... lien ...";
				else if (preg_match('#class="img_in_chat"#', $rep[$cible]))
					$msg = '<br>... image ...';
				else if (preg_match("#<iframe src=#", $rep[$cible]))
					$msg = '<br>... video ...';
				else
					$msg = '<br>'.$rep[$cible];
				$st = $st.'
				<div class="list-group">
				<a href="#" class="list-group-item list-group-item-action go_to_msg" id="'.$_SESSION["id"].'_'.$userinfos["id"].'noti_msg">'.$userinfos['prenom'].' '.$userinfos['nom'].' vous a envoye un message : '.$msg.'</a>
				</div>';
			}
		}
	} else {
		$st = '
		<div class="list-group">
		<a href="#" class="list-group-item list-group-item-action">Pas de nouveaux messages</a>
		</div>';
	}
	return ($st);
}

function get_matchs($bdd, $table) {
	$st = "";
	$req_all = $bdd->prepare('SELECT * FROM '.$table.' WHERE id_p1 = ? AND vu_p1 = ?');
	$req_all->execute(array($_SESSION['id'], 0));
	if ($req_all->rowCount() > 0) {
		while ($rep = $req_all->fetch()) {
			$update = $bdd->prepare('UPDATE '.$table.' SET vu_p1 = ? WHERE id_p1 = ? AND id_p2 = ?');
			$update->execute(array(1, $_SESSION['id'], $rep['id_p2']));
			$user = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
			$user->execute(array($rep['id_p2']));
			$userinfos = $user->fetch();
			$st = $st.'
			<div class="list-group">
				<a href="#" class="list-group-item list-group-item-action">'.type_match($rep, 2, $userinfos, 1).'</a>
			</div>';
		}
	} else {
		$req_all = $bdd->prepare('SELECT * FROM '.$table.' WHERE id_p2 = ? AND vu_p2 = ?');
		$req_all->execute(array($_SESSION['id'], 0));
		if ($req_all->rowCount() > 0) {
			while ($rep = $req_all->fetch()) {
				$update = $bdd->prepare('UPDATE '.$table.' SET vu_p2 = ? WHERE id_p1 = ? AND id_p2 = ?');
				$update->execute(array(1, $rep['id_p1'], $_SESSION['id']));
				$user = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
				$user->execute(array($rep['id_p1']));
				$userinfos = $user->fetch();
				$st = $st.'
				<div class="list-group">
					<a href="#" class="list-group-item list-group-item-action">'.type_match($rep, 1, $userinfos, 2).'</a>
				</div>';
			}
		} else {
			$get_suppr_matchs = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p1 = ? OR id_p2 = ?');
			$get_suppr_matchs->execute(array($_SESSION['id'], $_SESSION['id']));
			$gets = $get_suppr_matchs->fetch();
			$vu = 1;
			if ($gets['id_p1'] == $_SESSION['id']) {
				if ($gets['vu_p1'] == 0) {
					$update = $bdd->prepare('UPDATE suppr_m SET vu_p1 = ? WHERE id_p1 = ? AND id_p2 = ?');
					$update->execute(array(1, $gets['id_p1'], $gets['id_p2']));
					$vu = 0;
				}
			} else {
				if ($gets['vu_p2'] == 0) {
					$update = $bdd->prepare('UPDATE suppr_m SET vu_p2 = ? WHERE id_p1 = ? AND id_p2 = ?');
					$update->execute(array(1, $gets['id_p1'], $gets['id_p2']));
					$vu = 0;
				}
			}
			if ($get_suppr_matchs->rowCount() > 0 && $vu == 0) {
				$st = '
				<div class="list-group">
					<a href="#" class="list-group-item list-group-item-action">'.unlike($bdd, $gets).'</a>
				</div>';
			} else {
				$st = '
				<div class="list-group">
					<a href="#" class="list-group-item list-group-item-action">Pas de nouveaux matchs</a>
				</div>';
			}
		}
	}
	return ($st);
}

function unlike($bdd, $req_suppr) {
	if ($req_suppr['id_p1'] == $_SESSION['id']) {
		$user = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
		$user->execute(array($req_suppr['id_p2']));
		$userinfos = $user->fetch();
	} else {
		$user = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
		$user->execute(array($req_suppr['id_p1']));
		$userinfos = $user->fetch();
	}
	return ("Vous ne matchez plus avec ".$userinfos['prenom']." ".$userinfos['nom']);
}

function type_match($tab, $n, $userinfos, $j) {
	if ($_SESSION['id'] == $tab['id_p1']) {
		if ($tab['match_p'.$n] == 1 && $tab['match_p'.$j] == 1)
			return (" Vous avez matche avec ".$userinfos['prenom'].' '.$userinfos['nom']);
		else
			return('Pas de nouveaux matchs');
	} else {
		if ($tab['match_p'.$n] == 1 && $tab['match_p'.$j] == 1)
			return (" Vous avez matche avec ".$userinfos['prenom'].' '.$userinfos['nom']);
		else
			return ($userinfos['prenom'].' '.$userinfos['nom'].' vous a envoye une demande de match');
	}
}

?>