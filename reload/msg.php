<?php

require_once('../setup/database.php');
extract($_POST);
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$e = explode("_", $id);
$id = $e[1];

$get_msg = $bdd->prepare("SELECT * FROM message WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?)");
$get_msg->execute(array($_SESSION['id'], $id, $id, $_SESSION['id']));
$refresh = $bdd->prepare('SELECT * FROM message WHERE id_p1 = ? AND id_p2 = ?');
$refresh->execute(array($id, $_SESSION['id']));
echo $refresh->rowCount()."{";
while ($msgs = $get_msg->fetch()) {
	if ($msgs['id_p1'] == $_SESSION['id']) {
		echo "<span class='right'>".$msgs['message']."</span><div class='clear'></div>";
	} else {
		echo "<span class='left'>".$msgs['message']."</span><div class='clear'></div>";
	}
}

?>