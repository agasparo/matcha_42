<?php

require_once('setup/database.php');
extract($_POST);
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$envoyeur = $_SESSION['id'];
if (isset($id))
	$receveur = $id;

if ($type == 1) {
	echo $envoyeur."_".$receveur;
} else if ($type == 2) {
	if (!empty($storage)) {
		$max_window = set_nb_post($w_w);
		$all_values = set_tables($storage, $envoyeur);
		echo set_div($all_values, $max_window, $bdd, $w_w);
	}
}

function set_div($tab, $max_window, $bdd, $w_w) {
	$st = "";
	if ($max_window >= count($tab))
		$max_window = count($tab) - 1;
	if ($max_window == 1)
		$left = 10;
	else
		$left = 5;
	if ($max_window == -1)
		$width = set_width(0, $left, $w_w);
	else
		$width = set_width($max_window, $left, $w_w);
	while ($max_window >= 0) {
		$receveur = explode("_", $tab[$max_window]);
		$receveur = $receveur[1];
		if (check_tab($receveur, $bdd) && check_bloque($receveur, $bdd)) {
			$msg = $bdd->prepare('SELECT * FROM message WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?)');
			$msg->execute(array($_SESSION['id'], $receveur, $_SESSION['id'], $receveur));
			$msgs = "";
			$refresh = $bdd->prepare('SELECT * FROM message WHERE id_p1 = ? AND id_p2 = ?');
			$refresh->execute(array($receveur, $_SESSION['id']));
			while ($get_msg = $msg->fetch()) {
				if ($get_msg['id_p1'] == $_SESSION['id']) {
					$msgs = $msgs."<span class='right'>".$get_msg['message']."</span><div class='clear'></div>";
				} else {
					$msgs = $msgs."<span class='left'>".$get_msg['message']."</span><div class='clear'></div>";
				}
			}
			$member = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
			$member->execute(array(intval($receveur)));
			$get_member = $member->fetch();
			$st = $st."<div class='msg_speak' style='left:".$left."%;width:".$width."%;' id='".$tab[$max_window]."' name='".$refresh->rowCount()."'><div class='header_msg' id='".$tab[$max_window]."_minimalize'><h6 id='".$tab[$max_window]."_minimalize'>".$get_member['prenom']." ".$get_member['nom']."</h6><font id='close_chat".$tab[$max_window]."' class='close_chat_g'>X</font></div><div class='body_msg' id='".$tab[$max_window]."_txt_msg'>".$msgs."</div><div class='footer_msg'><textarea class='txt_msg' id='".$tab[$max_window]."_txt' placeholder='Votre message'></textarea></div></div>[";
			$left = $left + $width+ 5;
		}
		$max_window--;
	}
	return ($st);
}

function check_bloque($id, $bdd) {
    $get_b = $bdd->prepare("SELECT * FROM bloquer WHERE (id_bloquer = ? OR id_bloquer = ?) AND (id_bloqueur = ? OR id_bloqueur = ?)");
    $get_b->execute(array($_SESSION['id'], $id, $_SESSION['id'], $id));
    if ($get_b->rowCount() > 0)
        return (0);
    return (1);
}

function check_tab($id, $bdd) {
	$get_m = $bdd->prepare('SELECT * FROM matchs WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?)AND match_p1 = ? AND match_p2 = ?');
	$get_m->execute(array($_SESSION['id'], $id, $_SESSION['id'], $id, 1, 1));
	if ($get_m->rowCount() > 0)
		return (1);
	return (0);
}

function set_width($max_window, $left, $w_w) {
	$one_f = 80;
	$width = $one_f / ($max_window + 1);
	return ($width);
}

function set_tables($storage, $envoyeur) {
	$tab = [];
	$i = 0;
	foreach ($storage as $key => $value) {
		if (!empty($value)) {
			$e = explode("_", $value);
			if ($e[0] == $envoyeur) {
				$tab[$i] = $value;
				$i++;
			}
		}
	}
	return ($tab);
}

function set_nb_post($w_w) {
	$w_d = 300;
	$tt = round($w_w / $w_d);
	if ($tt <= 0)
		$tt = 1;
	if ($tt >= 4)
		return ($tt - 2);
	else
		return ($tt - 1);
}

?>