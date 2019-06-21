<?php

session_start();

if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
	if ($_SESSION['admin'] != 1) {
		exit(0);
	}
} else {
	exit(0);
}

require_once('database.php');
extract($_POST);

if (isset($_POST['val']) && !empty($_POST['val']) && is_numeric($val))
	$val = htmlspecialchars($_POST['val']);
else
	$val = 0;

$run = explode("|", get_inf($bdd, $val));
if (isset($run[1])) {
	if ($run[1] == 0)
		echo "data exec : success";
	else
		echo "data exec : fail";
} else
	echo implode("", $run);


function get_inf($bdd, $val) {
	$get = $bdd->query('SELECT * FROM membre');
	if (empty($get)) {
		executeQueryFile('membres.sql', $bdd);
		return ("data exec : success");
	} else {
		if ($val == 3) {
			executeQueryFile('membres.sql', $bdd);
			return (create_seed('../seed/seed.php', $bdd));
		}
		else if ($val == 2)
			return(executeQueryFile('data.sql', $bdd));
		else if ($val == 0) {
			if ($get->rowCount() == 0)
				return ("<a class='btn btn-primary btn-lg' href='#'' role='button' id='regenere'>Regenerer une seed</a><br><br><a class='btn btn-primary btn-lg' href='#'' role='button' id='prendre'>Prendre une seed deja existante</a>");
			else
				return ("Votre base de donnee est deja set");
		}
	}
}

function executeQueryFile($filesql, $bdd) {
	$query = file_get_contents($filesql);
	$array = explode(";\n", $query);
	$b = true;
	for ($i=0; $i < count($array) ; $i++) {
		$str = $array[$i];
		if ($str != '') {
			$str .= ';';
			$b = $bdd->exec($str);  
		}  
	}
	return ("|".$b);
}

function create_seed($path, $bdd) {
	include($path);
	return("|"."0");
}

?>