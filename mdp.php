<?php

require_once('setup/database.php');
extract($_POST);
$get = $value;

if (!empty($get)) {
	if ($check = check_in_bdd(htmlspecialchars($name), $get, $bdd)) {
		$show_mdp = create_pass(rand(6, 15));
		update_bdd($check, $show_mdp, $bdd);
		env_mail($check, $show_mdp);
		echo "Un mail avec votre nouveau mot de passe a ete envoye";
	} else {
		echo "Probleme avec votre requette";
	}
} else {
	if (isset($name) && !empty($name)) {
		if ($val = check_in_bdd(htmlspecialchars($name), $value, $bdd)) {
			echo $val;
		} else {
			echo "Probleme avec votre requette";
		}
	}
}

function create_pass($length) {
	$caracteres = 'abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$longueurMax = strlen($caracteres);
	$chaineAleatoire = '';
	$i = 0;
	while ($i < $length) {
		$chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
		$i++;
	}
	return ($chaineAleatoire);
}

function check_in_bdd($name, $value, $bdd) {
	if (empty($value)) {
		$get_user = $bdd->prepare("SELECT * FROM membre WHERE nom_utilisteur = ?");
		$get_user->execute(array($name));
		if ($get_user->rowCount() > 0) {
			$gets = $get_user->fetch();
			return ($gets['nom_utilisteur']);
		}
		return (0);
	} else {
		$get_user = $bdd->prepare("SELECT * FROM membre WHERE nom_utilisteur = ? AND mail = ?");
		$get_user->execute(array($value, $name));
		if ($get_user->rowCount() > 0) {
			$gets = $get_user->fetch();
			return ($gets);
		}
		return (0);
	}
	return (0);
}

function update_bdd($utils, $show_mdp, $bdd) {
	$mdp = sha1($show_mdp);

	$update_membre = $bdd->prepare("UPDATE membre SET mdp = ? WHERE nom_utilisteur = ? AND mail = ?");
	$update_membre->execute(array($mdp, $utils['nom_utilisteur'], $utils['mail']));
}

function env_mail($utils, $show_mdp) {

	require 'mail/index.php';
	$message='
	<html>
	<body>
	<div align="center">
	<h2><u>Salut '.$utils['prenom'].' '.$utils['nom'].' voici tes identifiants : </u></h2>
	<ul>
	<li>nom d utilisateur : '.$utils['nom_utilisteur'].'</li>
	<br>
	<li>Mot de passe : '.$show_mdp.'</li>
	</ul>
	<p>Tu peux changer ce mot de passe sur ton profil</p>
	</div>
	</body>
	</html>
	';
	file_put_contents('mail/content.html', $message);
	test_gmail_smtp_basic($utils['mail'], 'changement du mot de passe', $utils['nom_utilisteur']);
	file_put_contents('mail/content.html', '');
}

?>