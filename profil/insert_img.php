<?php

require_once('../setup/database.php');
session_start();
extract($_POST);

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

$get_inf_pro = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
$get_inf_pro->execute(array($_SESSION['id']));
$userinfos = $get_inf_pro->fetch();

$i = 0;
$table_img = explode("|", $userinfos['photos']);

while ($i < 5) {
	if (!isset($table_img[$i]))
		$table_img[$i] = "";
	$i++;
}

$decoded = base64_decode(substr($line, 22));
$fichier = $_SESSION['id'].'_'.$place.'.png';
$fd = fopen("../photos/".$fichier, "w+");
file_put_contents("../photos/".$fichier, $decoded);
fclose($fd);
$table_img[$place] = $fichier;

$ph = implode("|", $table_img);

$update_membre = $bdd->prepare("UPDATE membre SET photos = ?, photos_pro = ? WHERE id = ?");
$update_membre->execute(array($ph, $fichier, $_SESSION['id']));

?>