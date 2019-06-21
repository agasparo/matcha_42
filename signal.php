<?php

require_once('setup/database.php');
extract($_POST);
session_start();

if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
	$req_signal = $bdd->prepare("INSERT INTO signaler(id_signaler, id_signaleur) VALUES(?, ?)");
	$req_signal->execute(array($identifiant, $_SESSION['id']));
	echo "ok";
}

?>