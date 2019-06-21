<?php

require_once('setup/database.php');
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
    exit(0);

extract($_POST);

$req = $bdd->prepare('SELECT * FROM local WHERE id_user = ?');
$req->execute(array($_SESSION['id']));
$c = $req->rowCount();
if ($c == 0) {
    $set_geo = $bdd->prepare('INSERT INTO local(id_user, lon, lat, city, region, pays) VALUES(?, ?, ?, ?, ?, ?)');
    $set_geo->execute(array($_SESSION['id'], $_POST['geoplugin_longitude'], $_POST['geoplugin_latitude'], $_POST['geoplugin_city'], $_POST['geoplugin_region'], $_POST['geoplugin_countryName']));
} else {
    $set_geo = $bdd->prepare('UPDATE local SET lon = ?, lat = ?, city = ?, region = ?, pays = ? WHERE id_user = ?');
    $set_geo->execute(array($_POST['geoplugin_longitude'], $_POST['geoplugin_latitude'], $_POST['geoplugin_city'], $_POST['geoplugin_region'], $_POST['geoplugin_countryName'], $_SESSION['id'])); 
}
?>