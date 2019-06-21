<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="icon" type="image/png" href="../photos/logo.png"/>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<title>Matcha - connection api</title>
	<style type="text/css">
		body {
			background-color: #F5F5F5;
		}
		#form_post {
			position: fixed;
		    background-color: #FFF;
		    width: 50%;
		    height: auto;
		    top: 25%;
		    left: 25%;
		    border: 1px solid #DCDCDC;
		    border-radius: 5px;
		    text-align: center;
		    overflow-y: auto;
		}
	</style>
</head>
<?php

function create_mdp($length) {
	$caracteres = 'abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMOPQRSTUVWXYZ';
	$longueurMax = strlen($caracteres);
	$chaineAleatoire = '';
	$i = 0;
	while ($i < $length) {
			$chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
		$i++;
	}
	return ($chaineAleatoire);
}

require_once('../setup/database.php');
require_once 'verif_user/autoload.php';

if (isset($_POST['sub_api'])) {
	$mdp = $_POST['pass_api'];
	$login = htmlspecialchars($_POST['util_api']);
	if (!empty($mdp) && !empty($login)) {
		$req_user = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ? AND mdp = ?');
		$req_user->execute(array($login, sha1($mdp)));
		if ($req_user->rowCount() > 0) {
			$tab = [];
			$userinfos = $req_user->fetch();
			foreach ($userinfos as $key => $value) {
				if (!is_numeric($key) && $key != 'mdp' && $key != 'verif' && $key != 'nom_utilisteur' && $key != 'oauth' && $key != 'id') {
					$tab['user'][$key] = $value;
				}
			}
			$secret = "6LeirqQUAAAAAMw_Smh7KmuVLYkjMffL879hLA52";
			$recaptcha = new \ReCaptcha\ReCaptcha($secret);
			$resp = $recaptcha->verify($_POST['g-recaptcha-response']);
			if ($resp->isSuccess()) {
				if (isset($_GET['url']) && !empty($_GET['url'])) {
					$chaine = serialize($tab);
					$pass = create_mdp(12);
					$chaine = openssl_encrypt($chaine, 'BF-CFB', $pass);
					$url = $_GET['url']."?acces_token=".urlencode($chaine)."&pass_token=".urlencode($pass);
					header("Location:".$url);
				} else {
					$erreur = "Erreur 30 : requette crash";
				}
			} else {
			    $erreur = "reCaptcha non rempli";
			}
		} else {
			$erreur = "Erreur Auth : Mauvais mot de passe ou mauvais nom d'utilisateur";
		}
	}
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<a class="navbar-brand" href="#">Connexion api</a>
</nav>
<div id="form_post">
	<br>
	<h3>Se connecter avec l'API</h3>
	<hr>
	<form method="POST" style="position: relative; width: 90%; left: 5%;">
		<div class="form-group">
			<label for="util_api">Nom d'utilisateur</label>
			<input type="text" class="form-control" id="util_api" name="util_api" placeholder="Nom d'utilisateur">
		</div>
		<div class="form-group">
			<label for="pass_api">Mot de passe</label>
			<input type="password" class="form-control" id="pass_api" name="pass_api" placeholder="Mot de passe">
		</div>
		<div class="g-recaptcha" data-sitekey="6LeirqQUAAAAALfnJS5UZK98VCpD5dBZtvppTNc0"></div>
		<br>
		<button type="submit" class="btn btn-primary" name="sub_api">Se connecter</button>
	</form>
	<?php
	if (isset($erreur) && !empty($erreur)) {
		?><font color="red"><?= $erreur; ?></font><?php
	}
	?>
</div>