<?php
require_once('setup/database.php');
session_start();
extract($_POST);

if (isset($mails) && !empty($mails) && isset($mdps) && !empty($mdps) && isset($mdps1) && !empty($mdps1) && isset($noms) && !empty($noms) && isset($prenoms) && !empty($prenoms)) {
    $mails = htmlspecialchars($mails);
    $prenoms = htmlspecialchars($prenoms);
    $noms = htmlspecialchars($noms);
    $show_mdp = $mdps;
    $test = preg_replace('/[1234567890]/iu', "", $show_mdp);
    if (ctype_alnum($mdps) AND preg_match('/([0-9]+)/', $mdps) AND ctype_alpha($test)) {
        $mdps = sha1($mdps);
        $mdps1 = sha1($mdps1);
        if ($mdps == $mdps1) {
            if (strlen($mdps) >= 5 || strlen($mdps1) >= 5) {
                if (strlen($prenoms) >= 3) {
                    if (strlen($noms) >= 2) {
                        if (filter_var($mails, FILTER_VALIDATE_EMAIL)) {
                            $req_user = $bdd->prepare('SELECT * FROM membre WHERE mail = ?');
                            $req_user->execute(array($mails));
                            $count = $req_user->rowCount();
                            if ($count == 0) {
                                $set_id = uniqid();
                                $req = $bdd->prepare("INSERT INTO membre (nom, prenom, mail, mdp, sexe, attirance, description, photos, photos_pro, age, verif, nom_utilisteur, interets, oauth) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                $req->execute(array($noms, $prenoms, $mails, $mdps, 'A', 'A', 0, "", "", 0, $set_id, $utilisateur, 0, 0));
                                require 'mail/index.php';
                                $message ='
                                <html>
                                    <body>
                                        <div align="center">
                                            <h2><u>Salut '.$prenoms.' '.$noms.' voici tes identifiants : </u></h2>
                                            <ul>
                                                <li>nom d utilisateur : '.$utilisateur.'</li>
                                                <br>
                                                <li>Mot de passe : '.$show_mdp.'</li>
                                            </ul>
                                            <a href="http://192.168.99.100.xip.io:41062/www/matcha/verif.php?confirm='.$set_id.'">Clicke sur ce lien pour confirmer ton mail</a>
                                        </div>
                                    </body>
                                </html>
                                ';
                                file_put_contents('mail/content.html', $message);
                                test_gmail_smtp_basic($mails, 'confirmation d\'iscription', $utilisateur);
                                file_put_contents('mail/content.html', '');
                            } else {
                                echo "Votre mail est deja utilise";
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
                echo "Votre mot de passe doit faire au moins 5 caracteres";
            }
        } else {
            echo "Vos mots de passe ne concordent pas";
        }
    } else {
        echo "Votre mot de passe doit contenir chiffres et lettres et pas de 0";
    }
} else {
    echo "Veuillez remplir tous les champs";
}
?>