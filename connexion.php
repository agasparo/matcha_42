<?php
require_once('setup/database.php');
session_start();
extract($_POST);

if (isset($nom_utilisteur) && !empty($nom_utilisteur) && isset($mdps) && !empty($mdps)) {
	$mdps = sha1($mdps);
	$nom_utilisteur = htmlspecialchars($nom_utilisteur);
	$req_user = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
	$req_user->execute(array($nom_utilisteur));
	$count = $req_user->rowCount();
	if ($count > 0) {
		$req_user = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ? AND mdp = ?');
		$req_user->execute(array($nom_utilisteur, $mdps));
		$count = $req_user->rowCount();
		if ($count > 0) {
			$get_user = $req_user->fetch();
			$req_verif = $bdd->prepare('SELECT * FROM membre WHERE id = ?');
			$req_verif->execute(array($get_user['id']));
			$check_verif = $req_verif->fetch();
			if ($check_verif['verif'] == 1) {
				$is_connected = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
				$is_connected->execute(array($get_user['id']));
				if ($is_connected->rowCount() > 0) {
					$connected = $bdd->prepare('UPDATE connected SET co = ? WHERE id_user = ?');
					$connected->execute(array(1, $get_user['id']));
				} else {
					$connected = $bdd->prepare('INSERT INTO connected(id_user, co) VALUES(?, ?)');
					$connected->execute(array($get_user['id'], 1));
				}
				$barre = $bdd->prepare('SELECT * FROM barre_matchs WHERE id_user = ?');
				$barre->execute(array($get_user['id']));
				if ($barre->rowCount() > 0) {
					$date = $barre->fetch();
					if ($date['time_barre'] > 0) {
						$now = time();
						$time_pass = $now - $date['time_barre'];
						if (gmdate("H", $time_pass) >= '05') {
							$update_barre = $bdd->prepare("UPDATE barre_matchs SET val_barre = ?, time_barre = ? WHERE id_user = ?");
							$update_barre->execute(array(100, 0, $get_user['id']));
						}
					}
				}
				$_SESSION['id'] = $get_user['id'];
				$_SESSION['prenom'] = $get_user['prenom'];
			} else {
				echo "Verifie ton nom mail avant de te connecter";
			}
		} else {
			echo "Mauvais mot de passe ou mauvais nom d'utilisteur";
		}
	} else {
		echo "ok";
		?>
		<form>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">S'inscrire</h5>
					<p><font id="errors" color="red"></font></p>
					<button type="button" class="close" id="close_form" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="exampleInputnom_utilisateur">Nom d'utilisateur: </label>
						<input type="text" class="form-control" id="exampleInputnom_utilisateur" aria-describedby="emailHelp" placeholder="Entrer votre nom">
					</div>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="exampleInputnom1">Nom : </label>
						<input type="text" class="form-control" id="exampleInputnom1" aria-describedby="emailHelp" placeholder="Entrer votre nom">
					</div>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="exampleInputPrenom1">Prenom : </label>
						<input type="text" class="form-control" id="exampleInputPrenom1" aria-describedby="emailHelp" placeholder="Entrer votre prenom">
					</div>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="exampleInputEmail1">Email : </label>
						<input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Entrer votre email">
						<small id="emailHelp" class="form-text text-muted">Votre mail doit etre du type : exemple@gmail.com</small>
					</div>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="exampleInputpass">Mot de passe : </label>
						<input type="password" class="form-control" id="exampleInputpass" placeholder="Entrer votre mot de passe" autocomplete="">
					</div>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="exampleInputpass1">Repeter votre mot de passe : </label>
						<input type="password" class="form-control" id="exampleInputpass1" placeholder="Repeter votre mot de passe" autocomplete="">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="ins">S'inscrire</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_form1">Annuler</button>
				</div>
			</div>
		</div>
	</form>
		<?php
	}
} else {
	echo "Veuillez remplir tous les champs";
}

?>