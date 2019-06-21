<?php
require_once('setup/database.php');
session_start();

if (isset($_SESSION['id']) AND !empty($_SESSION['id'])) {
	$check_pro = $bdd->prepare("SELECT * FROM membre WHERE id = ?");
	$check_pro->execute(array($_SESSION['id']));
	$infs = $check_pro->fetch();

	foreach ($infs as $key => $value) {
		if (empty($value) && $key != 'oauth' && $key != 14) {
			header("Location:profil/".$infs['prenom'].$infs['nom']);
		}
	}
}

?>
<html>
<head>
	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="icon" type="image/png" href="photos/logo.png"/>
	<title>Matcha</title>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		<a class="navbar-brand" href="#">Matcha</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon" id="small_btn"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav mr-auto">
				<?php
				if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
					?>
					<li class="nav-item">
						<a class="nav-link" href="<?= 'profil/'.$infs['prenom'].$infs['nom'];?>">Mon profil</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#" id="notif_bar">Notifications</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="parametres.php">Parametres</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="recherche.php">Recherche</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="carte.php">Carte</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="deco.php">Se deconnecter</a>
					</li>
					<?php
				} else {
					?>
					<li class="nav-item">
						<a class="nav-link" href="#" id="co_bar">S'inscrire / Se connecter</a>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
	</nav>
	<div id="small_menu">
		<div class="small_meu">
		<?php
		if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
			?>
				<a class="nav-link" href="<?= 'profil/'.$infs['prenom'].$infs['nom'];?>">Mon profil</a>
				<a class="nav-link" href="#" id="notif_bar1">Notifications</a>
				<div id="notif_php1" style="display: none;">
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
				<a class="nav-link" href="parametres.php">Parametres</a>
				<a class="nav-link" href="recherche.php">Recherche</a>
				<a class="nav-link" href="carte.php">Carte</a>
				<a class="nav-link" href="deco.php">Se deconnecter</a>
			<?php
		} else {
			?>
				<br><a href="#" id="co_bar1">S'inscrire / Se connecter</a><br><br>
			<?php
		}
		?>
		</div>
	</div>
	<?php if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
		?><div class="musique_lecteur" id="musique_lecteur"><?php include('lecteur/lecteur.html'); ?><div class="in_ou_m"><p id="close_open_lecteur">ouvrir / fermer la fenetre</p></div></div><?php
	} ?>
	<ul class="list-group" id="notif_menu">
		<div id="notif_php">
			<?php
			if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
				?><div id="cache"></div><?php
			}

			$req_msg = $bdd->prepare('SELECT * FROM messages WHERE id_p1 = ? AND vu = ?');
			$req_msg->execute(array($_SESSION['id'], 0));
			$count_messages = $req_msg->rowCount();
			if ($count_messages < 1) {
				$req_msg = $bdd->prepare('SELECT * FROM messages WHERE id_p2 = ? AND vu = ?');
				$req_msg->execute(array($_SESSION['id'], 0));
				$count_messages = $req_msg->rowCount();
				while ($get_envoyeur = $req_msg->fetch()) {
					if ($get_envoyeur['id_envoyeur'] == $_SESSION['id'])
						$count_messages--;
				}
			} else {
				while ($get_envoyeur = $req_msg->fetch()) {
					if ($get_envoyeur['id_envoyeur'] == $_SESSION['id'])
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

			$req_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p1 = ? AND vu_p1 = ? AND match_p1 = ? AND match_p2 = ?');
			$req_match->execute(array($_SESSION['id'], 0, 1, 1));
			$count_match = $req_match->rowCount();
			$req_match = $bdd->prepare('SELECT * FROM matchs WHERE id_p2 = ? AND vu_p2 = ? AND match_p1 = ? AND match_p2 = ?');
			$req_match->execute(array($_SESSION['id'], 0, 1, 1));
			$count_match = $count_match + $req_match->rowCount();
			$req_suppr = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p2 = ? AND vu_p2 = ?');
			$req_suppr->execute(array($_SESSION['id'], 0));
			$count_match = $count_match + $req_suppr->rowCount();
			$req_suppr = $bdd->prepare('SELECT * FROM suppr_m WHERE id_p1 = ? AND vu_p1 = ?');
			$req_suppr->execute(array($_SESSION['id'], 0));
			$count_match = $count_match + $req_suppr->rowCount();


			?>
			<li class="list-group-item d-flex justify-content-between align-items-center">
				Messages
				<span class="badge badge-primary badge-pill"><?= $count_messages; ?></span>
			</li>
			<li class="list-group-item d-flex justify-content-between align-items-center" id="show_macths_ok">
				Matchs
				<span class="badge badge-primary badge-pill"><?= $count_match; ?></span>
			</li>
			<li class="list-group-item d-flex justify-content-between align-items-center">
				Visites
				<span class="badge badge-primary badge-pill"><?= $count_visites; ?></span>
			</li>
		</div>
	</ul>
		<div class="alert alert-dismissible alert-success" style="display: none;" id="alert_sign">
			Signalement envoye
		</div>
		<div class="alert alert-dismissible alert-success" style="display: none;" id="alert_bloq"></div>
	<div id="fond"></div>
	<ul class="list-group" id="match_infos">
	</ul>
	<div id="notif_show_all" class="notif_show_all"></div>
	<div id="co_menu">
		<div id="voile"></div>
		<div class="modal" style="display: block; top: 25%;" id="momo">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">S'inscrire / Se connecter</h5>
						<p><font id="errors" color="red"></font></p>
						<button type="button" class="close" id="close_form" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="input_nom_utilisteur">Nom d'utilisteur : </label>
							<input type="text" class="form-control" id="input_nom_utilisteur" placeholder="Entrer votre nom d'utilisteur">
						</div>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<form>
							<label for="exampleInputpass">Mot de passe : </label>
							<input type="password" class="form-control" id="exampleInputpass" placeholder="Entrer votre mot de passe" autocomplete="">
							</form>
						</div>
						<small style="cursor: pointer;" id="reset_mdp">Mot de passe oublie ?</small>
						<div id="mdp_oublier">
							<label for="nom_util" id="nom_util_for">Nom d'utilisateur : </label>
							<input type="text" class="form-control" id="nom_util" placeholder="Entrer votre nom d'utilisateur">
						</div>
					</div>
					<div id="autre_connexions">
						<div class="modal-header">
							<h5 class="modal-title">S'inscrire / Se connecter avec les reseaux : </h5>
						</div>
						<font id="google_connection"></font>
						<font id="steam_connection"></font>
						<font id="insta_connection"><img src="photos/insta_connexion.png" class="insta_connexion"></font>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="ins_co">S'inscrire / Se connecter</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_form1">Annuler</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="carte" id="dehors" style="display: none; left: 20%;"></div>

	<div class="carte" id="in">
		<div class="img_pro"></div>
		<div class="carte_txt">
			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#" id="home">Informations</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active show" data-toggle="tab" href="#" id="profile">Photos</a>
				</li>
				<li class="nav-item">
					<a class="nav-link disabled" href="#">Jsp</a>
				</li>
			</ul>
			<div id="myTabContent" class="tab-content"></div>
		</div>
	</div>


	<div class="alert alert-dismissible alert-danger" style="display: none;" id="alert_min_match">
		<button type="button" class="close" data-dismiss="alert" id="close_alert_match_min">&times;</button>
		<strong>Attention </strong>Il te reste moins de 3 match pour aujourd'hui.
	</div>
	<div class="alert alert-dismissible alert-primary" style="display: none;" id="alert_fin_match">
		<button type="button" class="close" data-dismiss="alert" id="close_alert_match_fin">&times;</button>
		<strong>Oh Non </strong>Il ne te reste plus aucun match pour aujourd'hui.
	</div>
	<div class="alert alert-dismissible alert-primary" style="display: none;" id="alert_allready_match">
		<button type="button" class="close" data-dismiss="alert" id="close_alert_match_fin">&times;</button>
		Tu as deja envoye une demande de match a cette personne.
	</div>
	<div class="alert alert-dismissible alert-primary" style="display: none;" id="alert_allready_match_m">
		Tu ne peux pas t'auto matcher.
	</div>
	<div class="alert alert-dismissible alert-primary" style="display: none;" id="alert_allready_match_b">
		l'utilisateur vous a bloque ou vous l'avez bloque.
	</div>
	<div class="alert alert-dismissible alert-success" style="display: none;" id="alert_mail">
		Un mail de confirmation a ete envoye
	</div>
	<div class="alert alert-dismissible alert-success" style="display: none;" id="alert_match">
		<button type="button" class="close" data-dismiss="alert" id="close_alert_match_fin">&times;</button>
		Demande de match envoyee
	</div>
	<div class="alert alert-dismissible alert-primary" style="display: none;" id="alert_mdp_prob"></div>
	<div class="alert alert-dismissible alert-success" style="display: none;" id="alert_mdp_ok"></div>
	<div class="place_btn_left">
		<button type="button" class="btn btn-primary" id="next">&#9587;</button>
	</div>
	<div class="place_btn_right">
		<button type="button" class="btn btn-primary" id="love">&#9825;</button>
	</div>
	<div class="pos_progress_bar">
		<div class="progress">
			<?php
			if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
				$req_barre = $bdd->prepare("SELECT * FROM barre_matchs WHERE id_user = ?");
				$req_barre->execute(array($_SESSION['id']));
				$infos = $req_barre->fetch();
				$cot = $req_barre->rowCount();
				if ($cot == 0) {
					$infos['val_barre'] = 100;
				}
				?>
				<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="<?= 'width :'.$infos['val_barre'].'%;'; ?>" id="barre_next"></div>
				<?php
			} else {
				?>
				<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%" id="barre_next"></div>
				<?php
			}
			?>
		</div>
	</div>
	<div id="notif_show_mobil"></div>
	<div id="res"></div>
	<div id="chat_mes"></div>
	<script type="text/javascript" src="js/index.js"></script>
	<script type="text/javascript" src="js/index_autre.js"></script>
</body>
<script type="text/javascript">
	setInterval('load_geo()', 3000);
	function load_geo() {
		$.getJSON('http://www.geoplugin.net/json.gp?jsoncallback=?', function(data) {
			$.post('geo.php', data, function(data) {
			});
		});
	}
</script>
</html>