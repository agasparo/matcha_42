<?php
require_once('setup/database.php');
session_start();
?>
<html>
<head>
	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="icon" type="image/png" href="photos/logo.png"/>
	<title>Matcha - recherche</title>
	<style type="text/css">
		input[type="text"]::placeholder {
			font-size: 1.2vh;
		}
		input[type="number"]::placeholder {
			font-size: 1.2vh;
		}
	</style>
</head>
<?php
if (isset($_SESSION['id']) AND !empty($_SESSION['id'])) {

	$check_pro = $bdd->prepare("SELECT * FROM membre WHERE id = ?");
	$check_pro->execute(array($_SESSION['id']));
	$infs = $check_pro->fetch();

	foreach ($infs as $key => $value) {
		if (empty($value) && $key != 'oauth' && $key != 14) {
			header("Location:profil/".$infs['prenom'].$infs['nom']);
		}
	}

	?>
	<body>
		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
			<a class="navbar-brand" href="#">Recherche</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon" id="small_btn"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarColor01">
				<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a class="nav-link" href="index.php" id="home">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?= 'profil/'.$infs['prenom'].$infs['nom'];?>">Profil</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#" id="notif_bar">Notifications</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="parametres.php">Parametres</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="carte.php">Carte</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="deco.php">Se deconnecter</a>
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
		<div id="small_menu">
		<div class="small_meu">
			<a class="nav-link" href="index.php">Home</a>
			<a class="nav-link" href="<?= 'profil/'.$infs['prenom'].$infs['nom'];?>" id="profil_bar">Mon profil</a>
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
				<a class="nav-link" href="carte.php">Carte</a>
				<a class="nav-link" href="deco.php">Se deconnecter</a>
			</div>
		</div>
		<div id="fond"></div>
		<ul class="list-group" id="match_infos">
		</ul>

		<div class="musique_lecteur" id="musique_lecteur"><?php include('lecteur/lecteur.html'); ?><div class="in_ou_m"><p id="close_open_lecteur">ouvrir / fermer la fenetre</p></div></div>
		<div class="alert alert-dismissible alert-success" style="display: none;" id="alert_sign">
			Signalement envoye
		</div>
		<div class="alert alert-dismissible alert-success" style="display: none;" id="alert_bloq">
		</div>
		<div class="alert alert-dismissible alert-primary" style="display: none;" id="alert_allready_match">
		</div>
		<div>
			<br>
			<h1 style="text-align: center;" id="rechercher_des">Rechercher des personnes</h1>
			<br>
			<div id="more">
				<form method="POST" id="form_search">
					<table class='table table-hover' style="text-align: left; font-size: 1.5vh;">
						<tr>
							<td>
								<fieldset class="form-group">
									<legend style="font-size: 1.5vh;">Sexe : </legend>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input sex" type="checkbox" id="sex1" value="M" checked="">
											Homme
										</label>
									</div>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input sex" type="checkbox" id="sex2" value="F" checked="">
											Femme
										</label>
									</div>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input sex" type="checkbox" id="sex3" value="A" checked="">
											Autre
										</label>
									</div>
								</fieldset>
							</td>
							<td>
								<div class="form-group">
									<label for="nom_serach">Prenom</label>
									<input type="text" class="form-control" id="prenom_serach" name="prenom_serach" placeholder="Entrer un prenom">
								</div>
								<div class="form-group">
									<label for="nom_serach">Interets</label>
									<input type="text" class="form-control" id="Interets_serach" name="Interets_serach" placeholder="Entrer un / des interets">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<fieldset class="form-group">
									<legend style="font-size: 1.5vh;">Orientation : </legend>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input ori" type="checkbox" name="ori[]" value="M" checked="">
											Homme
										</label>
									</div>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input ori" type="checkbox" name="ori[]" value="F" checked="">
											Femme
										</label>
									</div>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input ori" type="checkbox" name="ori[]" value="A" checked="">
											Autre
										</label>
									</div>
								</fieldset>
							</td>
							<td>
								<div class="form-group">
									<label for="nom_serach">Age</label>
									<input type="number" class="form-control" id="age_search_min" name="age_search_min" placeholder="age min" min="1" max="99">
									<input type="number" class="form-control" id="age_search_max" name="age_search_max" placeholder="age max" min="1" max="99">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label for="nom_serach">Jeux</label>
									<input type="text" class="form-control" id="Jeux_serach" name="Jeux_serach" placeholder="Entrer un / des jeux">
								</div>
								<div class="form-group">
									<label for="nom_serach">Temps de jeu</label>
									<input type="number" class="form-control" id="time_game_min" name="time_game_min" placeholder="Temps de jeu" min="1" max="10000">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for="nom_serach">Nb followers</label>
									<input type="number" class="form-control" id="Nb_followers" name="Nb_followers" placeholder="Nb followers" min="1" max="100000">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label for="nom_serach">Ville</label>
									<input type="text" class="form-control" id="ville_serach" name="ville_serach" placeholder="Entrer une ville">
								</div>
								<div class="form-group">
									<label for="nom_serach">region</label>
									<input type="text" class="form-control" id="region_serach" name="region_serach" placeholder="Entrer une region">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for="nom_serach">pays</label>
									<input type="text" class="form-control" id="pays_serach" name="pays_serach" placeholder="Entrer un pays">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label for="exampleSelect1">Trier par : </label>
									<select class="form-control" id="type_trie" style="font-size: 1.2vh;">
										<option>none</option>
										<option>age</option>
										<option>localisation</option>
										<option>grade</option>
										<option>popularite</option>
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for="value">Valeur (optionel)</label>
									<select class="form-control" id="order_value" disabled="" style="font-size: 1.2vh;">
										<option>plus grand que</option>
										<option>plus petit que</option>
									</select>
									<input type="number" max="1000000" min="0" class="form-control" id="value" name="value" placeholder="valeur si besion" disabled="">
								</div>
								<div class="form-group">
									<label for="exampleSelect1">ordre : </label>
									<select class="form-control" id="order_trie" disabled="" style="font-size: 1.2vh;">
										<option>croissant</option>
										<option>decroissant</option>
									</select>
								</div>
							</td>
						</tr>
					</table>
					<hr>
					<button type="submit" id="sub" class="btn btn-primary">Rechercher</button>
				</form>
			</div>
		</div>
		<hr>
		<div id="notif_show_mobil"></div>
		<div id="profil"></div>
		<div id="res"></div>
		<div id="chat_mes"></div>
	</body>
	<script type="text/javascript" src="js/req.js"></script>
	<?php
} else {
	header("Location:index.php");
}
?>
</html>