<?php

$DB_DSN = 'mysql:host=localhost;dbname=membres';
$DB_USER = 'root';
$DB_PASSWORD = '';

$bdd = new PDO($DB_DSN , $DB_USER, $DB_PASSWORD);

?>