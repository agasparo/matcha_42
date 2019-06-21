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
	<script type="text/javascript" src="js/param.js"></script>
	<title>Matcha - parametres</title>
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
			<a class="navbar-brand" href="#">Parametres</a>
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
					<a class="nav-link" href="recherche.php">Recherche</a>
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
				<a class="nav-link" href="recherche.php">Recherche</a>
				<a class="nav-link" href="carte.php">Carte</a>
				<a class="nav-link" href="deco.php">Se deconnecter</a>
			</div>
		</div>
		<h1 style="text-align: center;">Voici la liste de tes parametres <?= $infs['prenom']; ?></h1>
		<div class="musique_lecteur" id="musique_lecteur"><?php include('lecteur/lecteur.html'); ?><div class="in_ou_m"><p id="close_open_lecteur">ouvrir / fermer la fenetre</p></div></div>
		<?php
		$get_loc = $bdd->prepare('SELECT * FROM local WHERE id_user = ?');
		$get_loc->execute(array($infs['id']));
		$loc = $get_loc->fetch();
		if (isset($loc['city']) && !empty($loc['city']))
			$city = $loc['city'];
		if (isset($loc['pays']) && !empty($loc['pays']))
			$pays = $loc['pays'];
		if (isset($loc['region']) && !empty($loc['region']))
			$region = $loc['region'];
		?>
		<div class="jumbotron">
			<h3>Changer sa localisation</h3>
			<form style="position: relative; width: 90%;">
				<div class="form-group">
					<label for="pays_loc">Pays</label>
					<input type="text" class="form-control" id="pays_loc" placeholder="Votre pays" value='<?= $pays; ?>'>
				</div>
				<div class="form-group">
					<label for="ville_loc">Ville</label>
					<input type="text" class="form-control" id="ville_loc" placeholder="votre ville" value='<?= $city; ?>'>
				</div>
				<div class="form-group">
					<label for="region_loc">Region</label>
					<input type="text" class="form-control" id="region_loc" placeholder="votre region" value='<?= $region; ?>'>
				</div>
				<button type="submit" class="btn btn-primary" id="sub_localisation">Changer</button>
			</form>
		</div>
		<div class="jumbotron" id="matchs_infos"></div>
		<div class="jumbotron" id="bloque_infos"></div>
		<div id="notif_show_mobil"></div>
		<div id="profil"></div>
		<div id="res"></div>
		<div id="chat_mes"></div>
	<?php
} else {
	header("Location:index.php");
}


?>