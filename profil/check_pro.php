<?php

require_once('../setup/database.php');
session_start();
extract($_POST);

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    exit(0);
}

if ($mdps == "") {
	$req = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
	$req->execute(array($_SESSION['id']));
	$mdpa = $req->fetch();
	$mdps = $mdpa['mdp'];
    $show_mdp = $mdps;
} else {
    $show_mdp = $mdps;
	$mdps = sha1($mdps);
}

if (isset($mails) && !empty($mails) && isset($mdps) && !empty($mdps) && isset($noms) && !empty($noms) && isset($prenoms) && !empty($prenoms) && isset($interets) && !empty($interets) && isset($descriptions) && !empty($descriptions) && isset($attirances) && !empty($attirances) && isset($sexes) && !empty($sexes) && isset($ages) && !empty($ages)) {

    $mails = htmlspecialchars($mails);
    $prenoms = htmlspecialchars($prenoms);
    $noms = htmlspecialchars($noms);
    $ages = htmlspecialchars($ages);
    $attirances = htmlspecialchars($attirances);
    $interets = htmlspecialchars($interets);
    $descriptions = htmlspecialchars($descriptions);
    $sexes = htmlspecialchars($sexes);

    if (strlen($mdps) >= 5) {
        if (check_mdp($show_mdp)) {
        	if (strlen($prenoms) >= 3) {
        		if (strlen($noms) >= 2) {
        			if (filter_var($mails, FILTER_VALIDATE_EMAIL)) {
        				if ($attirances == "M" || $attirances == "F" || $attirances == "A") {
        					if ($sexes == "M" || $sexes == "F" || $sexes == "A") {
        						$update_membre = $bdd->prepare("UPDATE membre SET nom = ?, prenom = ?, mail = ?, mdp = ?, sexe = ?, attirance = ?, description = ?, age = ?, interets = ? WHERE id = ?");
    							$update_membre->execute(array($noms, $prenoms, $mails, $mdps, $sexes, $attirances, $descriptions, $ages, norm_interts($interets), $_SESSION['id']));
    							echo "ok";
        					} else {
        						echo "Votre sexe doit etre M (homme) ou F (femme) ou A (autre)";
        					}
        				} else {
        					echo "Votre attirance doit etre M (homme) ou F (femme) ou A (autre)";
        				}
        			} else {
        				echo "Votre mail n'est pas valide";
        			}
        		} else {
        			echo "Votre nom doit faire au moins 2 caracteres";
        		}
        	} else {
        		echo "Votre prenom doit faire au moins 3 caracteres";
        	}
        } else {
            echo "Votre mot de passe doit contenir chiffres et lettres et pas de 0";
        }
    } else {
    	echo "Votre mot de passe doit faire au moins 5 caracteres";
    }
} else {
	echo "Veuillez remplir tous les champs";
}

function check_mdp($show_mdp) {
    $test = preg_replace('/[1234567890]/iu', "", $show_mdp);
    if (ctype_alnum($show_mdp) AND preg_match('/([0-9]+)/', $show_mdp) AND ctype_alpha($test))
        return (1);
    return (0);
}

function norm_interts($interets) {
    $e = explode("#", $interets);
    foreach ($e as $key => $value) {
        if (empty($value))
            unset($e[$key]);
        else {
            $value = trim($value);
            $value = str_replace(" ", "", $value);
            $e[$key] = $value;
        }
    }
    $e = array_values($e);
    return ("#".implode(" #", $e));
}

?>