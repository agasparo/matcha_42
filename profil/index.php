<?php
require_once('../setup/database.php');
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	header("Location:../index.php");

$get_inf_pro = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
$get_inf_pro->execute(array($_SESSION['id']));
$userinfos = $get_inf_pro->fetch();

?>
<html>
<head>
	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<?php
	if (!empty($userinfos['photos_pro'])) {
		if (preg_match("#https://#", $userinfos['photos_pro']) || preg_match("#http://#", $userinfos['photos_pro'])) {
			?><link rel="icon" type="image" href="<?= $userinfos['photos_pro']; ?>"/><?php
		} else {
			?><link rel="icon" type="image/png" href="<?= '../photos/'.$userinfos['photos_pro']; ?>"/><?php
		}
	} else {
		?><link rel="icon" type="image/png" href="../photos/logo.png"/><?php
	}
	?>
	<title>Matcha - <?= $userinfos['prenom']." profil"; ?></title>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		<a class="navbar-brand" href="#">Bienvenue sur ton profil <?= $userinfos['prenom']; ?></a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation" id="btn-btn-mobil">
			<span class="navbar-toggler-icon" id="small_btn"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a class="nav-link" href="../index.php" id="home">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#" id="notif_bar">Notifications</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="../parametres.php">Parametres</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="../recherche.php">Recherche</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="../carte.php">Carte</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="../deco.php">Se deconnecter</a>
				</li>
			</ul>
		</div>
	</nav>
	<ul class="list-group" id="notif_menu">
		<div id="notif_php">
			<?php
			if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
				?><div id="cache"></div><?php
			}

			$req_msg = $bdd->prepare('SELECT * FROM message WHERE id_p2 = ? AND vu = ?');
			$req_msg->execute(array($_SESSION['id'], 0));
			$count_messages = $req_msg->rowCount();
			while($get_messages = $req_msg->fetch()) {
				if ($get_messages['id_envoyeur'] == $_SESSION['id']) {
					$count_messages--;
				}
			}

			$req_histo = $bdd->prepare('SELECT * FROM historique WHERE id_visiter = ? AND vu = ?');
			$req_histo->execute(array($_SESSION['id'], 0));
			$count_visites = $req_histo->rowCount();
			while ($get_visiteur = $req_histo->fetch()) {
				if ($get_visiteur['id_visiteur'] == $_SESSION['id'])
					$count_visites--;
			}

			$req_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND vu_p1 = ? AND match_p2 = ?');
			$req_match->execute(array($_SESSION['id'], 0, 1));
			$count_match = $req_match->rowCount();
			$req_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p2 = ? AND vu_p2 = ? AND match_p1 = ?');
			$req_match->execute(array($_SESSION['id'], 0, 1));
			$count_match = $count_match + $req_match->rowCount();
			$req_suppr = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p2 = ? AND vu_p2 = ?');
			$req_suppr->execute(array($_SESSION['id'], 0));
			$count_match = $count_match + $req_suppr->rowCount();
			$req_suppr = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p1 = ? AND vu_p1 = ?');
			$req_suppr->execute(array($_SESSION['id'], 0));
			$count_match = $count_match + $req_suppr->rowCount();
			?>
			<li class="list-group-item d-flex justify-content-between align-items-center notifications">
				Messages
				<span class="badge badge-primary badge-pill"><?= $count_messages; ?></span>
			</li>
			<li class="list-group-item d-flex justify-content-between align-items-center notifications">
				Matchs
				<span class="badge badge-primary badge-pill"><?= $count_match; ?></span>
			</li>
			<li class="list-group-item d-flex justify-content-between align-items-center notifications">
				Visites
				<span class="badge badge-primary badge-pill"><?= $count_visites; ?></span>
			</li>
		</div>
	</ul>
	<div id="notif_show_all" class="notif_show_all"></div>
	<div class="musique_lecteur" id="musique_lecteur"><?php include('lecteur/lecteur.html'); ?><div class="in_ou_m"><p id="close_open_lecteur">ouvrir / fermer la fenetre</p></div></div>
	<div id="small_menu">
		<div class="small_meu">
			<a class="nav-link" href="../index.php">Home</a>
			<a class="nav-link" href="#" id="notif_bar1">Notifications</a>
			<div id="notif_php1">
				<?php
				if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
					?><div id="cache"></div><?php
				}

				$req_msg = $bdd->prepare('SELECT * FROM message WHERE id_p2 = ? AND vu = ?');
				$req_msg->execute(array($_SESSION['id'], 0));
				$count_messages = $req_msg->rowCount();
				while($get_messages = $req_msg->fetch()) {
					if ($get_messages['id_envoyeur'] == $_SESSION['id']) {
						$count_messages--;
					}
				}

				$req_histo = $bdd->prepare('SELECT * FROM historique WHERE id_visiter = ? AND vu = ?');
				$req_histo->execute(array($_SESSION['id'], 0));
				$count_visites = $req_histo->rowCount();
				while ($get_visiteur = $req_histo->fetch()) {
					if ($get_visiteur['id_visiteur'] == $_SESSION['id'])
						$count_visites--;
				}

				$req_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND vu_p1 = ? AND match_p2 = ?');
				$req_match->execute(array($_SESSION['id'], 0, 1));
				$count_match = $req_match->rowCount();
				$req_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p2 = ? AND vu_p2 = ? AND match_p1 = ?');
				$req_match->execute(array($_SESSION['id'], 0, 1));
				$count_match = $count_match + $req_match->rowCount();
				$req_suppr = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p2 = ? AND vu_p2 = ?');
				$req_suppr->execute(array($_SESSION['id'], 0));
				$count_match = $count_match + $req_suppr->rowCount();
				$req_suppr = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p1 = ? AND vu_p1 = ?');
				$req_suppr->execute(array($_SESSION['id'], 0));
				$count_match = $count_match + $req_suppr->rowCount();
				?>
				<a class="nav-link" style="cursor: pointer;">Messages :<span class="badge badge-primary badge-pill"><?= $count_messages; ?></a></span>
				<a class="nav-link" style="cursor: pointer;">Matchs :<span class="badge badge-primary badge-pill"><?= $count_match; ?></span></a>
				<a class="nav-link" style="cursor: pointer;">Visites :<span class="badge badge-primary badge-pill"><?= $count_visites; ?></span></a>
			</div>
			<a class="nav-link" href="../parametres.php">Parametres</a>
			<a class="nav-link" href="../recherche.php">Recherche</a>
			<a class="nav-link" href="../carte.php">Carte</a>
			<a class="nav-link" href="../deco.php">Se deconnecter</a>
		</div>
	</div>
	<div id="err"></div>
	<div id="modif_in">
		<form>
		<br><label>Mail :</label><input type="email" value="<?= $userinfos['mail']; ?>" id='mail_input' class="form-control">
		<br><label>Mot de passe : </label><input type="password"  id='mdp_input' class="form-control" placeholder="Votre nouveau mdp" autocomplete="">
		</form>
	</div>
	<div id="modif">
		<br><label>Prenom : </label><input type="text" value="<?= $userinfos['prenom']; ?>" id='prenom_input' class="form-control" maxlength="15">
		<label>Nom : </label><input type="text" value="<?= $userinfos['nom']; ?>" class="form-control" id='nom_input'>
		<label>Age : </label><input type="number" min="18" max="100" value="<?= $userinfos['age']; ?>" id='age_input' class="form-control">
		<label>Sexe : </label><input type="text" value="<?= $userinfos['sexe']; ?>" class="form-control" maxlength="1" id="sexe_input" placeholder="H/F/A">
		<label>Attirance : </label><input type="text" value="<?= $userinfos['attirance']; ?>" class="form-control" maxlength="1" id='attirance_input' placeholder="H/F/A">
		<label>Decription : </label><textarea class="form-control" id="description_input" maxlength="273"><?= $userinfos['description']; ?></textarea>
		<label>Interets : </label><textarea class="form-control" id="interets_input" maxlength="51" placeholder="#exemple #unautreexemple"><?= $userinfos['interets']; ?></textarea>
	</div>
	<div id="notif_show_mobil"></div>
	<div id="profil">
		<?php
		if (strlen($userinfos['photos_pro']) < 2) {
			?><div id="photos_bloc"><h1 class="txt_img">Ajouter une image</h1></div><?php
		} else {
			if (preg_match("#https://#", $userinfos['photos_pro']) || preg_match("#http://#", $userinfos['photos_pro'])) {
				?><div class="img_pro"><img src="<?= $userinfos['photos_pro']; ?>" class="img_pro_id"></div><?php
			} else {
				?><div class="img_pro"><img src="<?= '../photos/'.$userinfos['photos_pro']; ?>" class="img_pro_id"></div><?php
			}
		}
		?>
		<div id="prenom"><h3><?= $userinfos['prenom']; ?></h3></div>
		<table class='table table-hover' id="tab_pro">
			<tr>
				<td>Age : <font id="age"><?= $userinfos['age']; ?></font> ans</td>
				<td>Sexe : <font id="sexe"><?= $userinfos['sexe']; ?></font></td>
				<td>Attirance : <font id="attirance"><?= $userinfos['attirance']; ?></font></td>
			</tr>
			<tr>
				<td></td>
				<td><div id="interets"><?= $userinfos['interets']; ?></div></td>
				<td></td>
			</tr>
			<div class="div_img">
				<?php
				$data = explode("|", $userinfos['photos']);
				$i = 0;
				while ($i < 5) {
					if (isset($data[$i]) && !empty($data[$i])) {
						if (preg_match("#https://#", $data[$i]) || preg_match("#http://#", $data[$i])) {
							?><div class='black_img_aj'><img src="<?= $data[$i]; ?>" class="img_match" id="<?= $i; ?>"></div><?php
						} else {
							?><div class='black_img_aj'><img src="<?= '../photos/'.$data[$i]; ?>" class="img_match" id="<?= $i; ?>"></div><?php
						}
					} else {
						?><div class="img_manque" id="<?= $i; ?>"><p class="txt_img" id="<?= $i; ?>">+</p></div><?php
					}
					$i++;
				}
				?>
			</div>
			<div id="description">
				<blockquote class="blockquote text-center">
					<p class="mb-0" id="des_p"><?= $userinfos['description']; ?></p>
				</blockquote>
			</div>
		</div>
		<div id="chat_mes"></div>
		<input type="file" id="file_add" hidden="hidden">
		<button type="button" class="btn btn-primary btn-lg" id="valider">Mettre a jour</button>
		<script type="text/javascript" src="js/profil.js"></script>
		<script type="text/javascript" src="js/autre.js"></script>
	</body>