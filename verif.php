<?php

require_once('setup/database.php');

$update_membre = $bdd->prepare("UPDATE membre SET verif = ? WHERE verif = ?");
$update_membre->execute(array(1, $_GET['confirm']));

header("Location:index.php");

?>