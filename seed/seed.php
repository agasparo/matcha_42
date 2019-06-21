<head>
	<meta charset="utf-8">
</head>
<?php

if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
	if ($_SESSION['admin'] != 1) {
		exit(0);
	}
} else {
	exit(0);
}

require_once('../setup/database.php');

$tab_musique = musique_add();

$des_homme = array(
	"Mec sympa et pas prise de tête qui cherche à connaître de nouvelles personnes",
	"Je veux faire des rencontres dans ma région, selon le feeling que j’aurais avec mes matchs",
	"Jeune golden boy à la fois beau et intelligent, qui cherche une femme qui a un peu de sens critique",
	"J’ai beau être vietnamien je te promet de ne pas manger ton chien. Par contre, je connais toutes les meilleures adresse de la ville et j’ai hâte de te les faire découvrir",
	"Capitaine de mon propre trois mois, j’ai besoin d’un moussaillon qui n’a pas peur de partir en voyage, à l’autre bout du monde"
);

$des_femme = array(
	"Si tu aimes prendre l'apéro, qu'entendre mon réveil snoozer en semaine ne te dérange pas, et que tu conçois de partir en week-end aussi bien à côté de Paris qu'à l'autre bout du monde (ok pas tous les week-ends ! ), c'est un bon début ! ;-)",
	"Danseuse professionnelle, je sais parfaitement exécuter les chorégraphie de Beyonce. Je suis la femme parfaite pour rendre tes ex jalouses ;)",
	"Je suis attachiante",
	"Parle moi, je ne mords pas",
	"On dira qu’on s’est rencontrés au cinéma/musée/autre"
);

$url_base = "https://burst.shopify.com/man?page=1";
$get_page = file_get_contents($url_base);


$get_web_page = file_get_contents("https://motperdu.fr/top-1000-des-prenoms-de-garcons");
$get_list = explode("<ol>", $get_web_page);
$fin_list = explode("</ol>", $get_list[1]);
$list_name_masc = explode("\n", strip_tags($fin_list[0]));
$i = 0;
while ($i < 250) {
	insert_in_bdd(create_data($list_name_masc[$i + 1], "M/A", "F/A/M", $des_homme, $des_femme, $get_page, $i, get_pos(), $tab_musique), $bdd);
	if ($i == 50) {
		$url_base = "https://burst.shopify.com/man?page=2";
		$get_page = file_get_contents($url_base);
	}
	if ($i == 100) {
		$url_base = "https://burst.shopify.com/man?page=3";
		$get_page = file_get_contents($url_base);
	}
	if ($i == 150) {
		$url_base = "https://burst.shopify.com/man?page=4";
		$get_page = file_get_contents($url_base);
	}
	if ($i == 200) {
		$url_base = "https://burst.shopify.com/man?page=5";
		$get_page = file_get_contents($url_base);
	}
	$i++;
}

$get_web_page = file_get_contents("https://motperdu.fr/top-1000-des-prenoms-de-filles");
$get_list = explode("<ol>", $get_web_page);
$fin_list = explode("</ol>", $get_list[1]);
$list_name_fem = explode("\n", strip_tags($fin_list[0]));
$url_base = "https://burst.shopify.com/woman?page=1";
$get_page = file_get_contents($url_base);
$i = 0;
while ($i < 250) {
	insert_in_bdd(create_data($list_name_fem[$i + 1], "F/A", "F/A/M", $des_homme, $des_femme, $get_page, $i, get_pos(), $tab_musique), $bdd);
	if ($i == 50) {
		$url_base = "https://burst.shopify.com/woman?page=2";
		$get_page = file_get_contents($url_base);
	}
	if ($i == 100) {
		$url_base = "https://burst.shopify.com/woman?page=3";
		$get_page = file_get_contents($url_base);
	}
	if ($i == 150) {
		$url_base = "https://burst.shopify.com/woman?page=4";
		$get_page = file_get_contents($url_base);
	}
	if ($i == 200) {
		$url_base = "https://burst.shopify.com/woman?page=5";
		$get_page = file_get_contents($url_base);
	}
	$i++;
}

function insert_in_bdd($data, $bdd) {
	$req = $bdd->prepare("INSERT INTO membre(nom, prenom, mail, mdp, sexe, attirance, description, photos, photos_pro, age, verif, nom_utilisteur, interets, oauth) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$req->execute(array($data['nom'], $data['prenom'], $data['mail'], $data['mdp'], $data['sexe'], $data['attirance'], $data['description'], $data['photos'], $data['photos_pro'], $data['age'], $data['verif'], $data['utilisateur'], $data['interets'], $data['oauth']));

	$req_user = $bdd->query('SELECT id FROM membre ORDER BY id DESC');
	$get_id = $req_user->fetch();

	$req_pos = $bdd->prepare('INSERT INTO local(id_user, lon, lat, city, region, pays) VALUES(?, ?, ?, ?, ?, ?)');
	$req_pos->execute(array($get_id['id'], $data['position']['long'], $data['position']['lat'], $data['position']['city'], $data['position']['region'], $data['position']['pays']));

	if (!empty($data['game'])) {
		$req_game = $bdd->prepare('INSERT INTO played(id_user, game, time_played) VALUES(?, ?, ?)');
		$req_game->execute(array($get_id['id'], $data['game'][0], $data['game'][1]));
	}

	if (!empty($data['follow'])) {
		$req_game = $bdd->prepare('INSERT INTO insta(id_user, nb_followers) VALUES(?, ?)');
		$req_game->execute(array($get_id['id'], $data['follow']));
	}

	$req_co = $bdd->prepare('INSERT INTO connected(id_user, co) VALUES(?, ?)');
	$req_co->execute(array($get_id['id'], $data['co']));

	$req_barre_matchs = $bdd->prepare('INSERT INTO barre_matchs(id_user, val_barre, time_barre) VALUES(?, ?, ?)');
	$req_barre_matchs->execute(array($get_id['id'], $data['val_barre'], $data['time_barre']));

	if (!empty($data['musique'])) {
		$e = explode('|', $data['musique']);
		$req_m = $bdd->prepare('SELECT * FROM musique WHERE id_musique = ?');
		$req_m->execute(array($e[0]));
		if ($req_m->rowCount() == 0) {
			$req_musique = $bdd->prepare('INSERT INTO musique(id_user, id_musique, name_musique) VALUES(?, ?, ?)');
			$req_musique->execute(array($get_id['id'], $e[0], $e[1]));
		}
	}
	if (!empty($data['song'])) {
		foreach ($data['song'] as $key => $value) {
			$e = explode('|', $value);
			$req_musique = $bdd->prepare('INSERT INTO song(id_user, id_musique, nb_fois) VALUES(?, ?, ?)');
			$req_musique->execute(array($get_id['id'], $e[0], $e[2]));
		}
	}

}


function create_nom($length) {
	$caracteres = 'abcdefghijklmnopqrstuvwxyz';
	$longueurMax = strlen($caracteres);
	$chaineAleatoire = '';
	$i = 0;
	while ($i < $length) {
			$chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
		$i++;
	}
	return ($chaineAleatoire);
}

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

function get_des($sexe, $des_homme, $des_femme) {
	if ($sexe == "M") {
		return ($des_homme[rand(0, 4)]);
	} else if ($sexe == "F") {
		return ($des_femme[rand(0, 4)]);
	} else {
		$rd = rand(0, 1);
		if ($rd == 0) {
			return ($des_homme[rand(0, 4)]);
		} else {
			return ($des_femme[rand(0, 4)]);
		}
	}
}

function get_photos_pro($get_page, $i) {
	if ($i >= 51 && $i < 101) {
		$a = 50;
	} else if ($i >= 101 && $i < 151) {
		$a = 100;
	} else if ($i >= 151 && $i < 201) {
		$a = 150;
	} else if ($i >= 201) {
		$a = 200;
	} else {
		$a = 0;
	}
	$deb = explode('<div class="photo-tile">', $get_page);
	$get = explode('data-srcset="', $deb[$i - $a]);
	if (isset($get[1])) {
		$get_fin = explode('"', $get[1]);
		$final = explode(",", $get_fin[0]);
		$photos = explode(" ", $final[1]);
		return ($photos[1]);
	} else {
		return ('none');
	}
}

function get_pos() {
	$file = file('../data/worldcities.csv');
	$is_take = [];
	$a = 0;
	while ($a == 0) {
		$rand = rand(1, 12892);
		if (!in_array($rand, $is_take)) {
			$is_take[count($is_take)] = $rand;
			$a = 1;
		}
	}
	$inf = explode(",", $file[$rand]);
	return ($inf);
}

function get_game() {
	if (rand(0, 3) == 1) {
		$data = str_replace("<td>", "", file_get_contents("../data/jeux"));
		$data = str_replace("</td>", "", $data);
		$data = str_replace("</tr>", "", $data);
		$tab = explode("<tr>", $data);
		$diff = explode("\n", $tab[rand(0, 1000)]);
		$final_tab[0] = $diff[1];
		$final_tab[1] = rand(1, 186000);
		return ($final_tab);
	} else {
		return ("");
	}
}

function get_folowers($is) {
	if (empty($is)) {
		if (rand(0, 3) == 1)
			return (rand(0, 10000));
		else
			return ("");
	} else {
		return ("");
	}
}

function connected() {
	if (rand(0, 1) == 1)
		return (1);
	$now = time();
	$enlever = rand(0, 100) * 60;
	return ($now - $enlever);
}

function val_barre() {
	$val = rand(0, 10);
	return ($val * 10);
}

function time_barre($val) {
	if ($val == 100)
		return (0);
	$now = time();
	$enlever = rand(0, 100) * 60;
	return ($now - $enlever);
}

function musique_add() {
	$gets = file('../data/musique.csv');
	$tab_m = [];
	foreach ($gets as $key => $value) {
		$ligne = explode(',', $value);
		$id = $ligne[2];
		$name = $ligne[3];
		array_push($tab_m, $id.'|'.$name);
	}
	return ($tab_m);
}

function get_musique($tab) {
	if (rand(1, 4) == 3)
		return ($tab[rand(0, count($tab) - 1)]);
	return(0);
}

function get_song($tab, $var) {
	if (empty($var))
		return (0);
	$nb_song = rand(0, count($tab) - 1);
	if ($nb_song == 0)
		return (0);
	$songs = [];
	while ($nb_song > 0) {
		$nb_fois = rand(1, 50);
		array_push($songs, $tab[rand(0, count($tab) - 1)].'|'.$nb_fois);
		$nb_song--;
	}
	return ($songs);
}

function create_data($name, $genre, $att, $des_homme, $des_femme, $get_page, $i, $local, $tab_musique) {
	$inf['nom'] = create_nom(rand(2, 15));
	$inf['prenom'] = $name;
	$inf['mail'] = $name."@gmail.com";
	$inf['mdp'] = sha1(create_mdp(rand(6, 13)));
	$or = explode("/", $genre);
	$inf['sexe'] = $or[rand(0, 1)];
	$or = explode("/", $att);
	$inf['attirance'] = $or[rand(0, 2)];
	$inf['description'] = get_des($inf['sexe'], $des_homme, $des_femme);
	if (get_photos_pro($get_page, $i) != 'none')
		$inf['photos'] = get_photos_pro($get_page, $i);
	else
		$inf['photos'] = "https://parismatch.be/app/uploads/2018/04/Macaca_nigra_self-portrait_large-e1524567086123-1100x715.jpg";
	$inf['photos_pro'] = $inf['photos'];
	$inf['age'] = rand(18, 30);
	$inf['verif'] = 1;
	$inf['utilisateur'] = $inf['prenom']." ".$inf['nom'];
	$inf['interets'] = "#amour #matcha #gg";
	$inf['position']['lat'] = str_replace('"', '', $local[2]);
	$inf['position']['long'] = str_replace('"', '', $local[3]);
	$inf['position']['city'] = $local[1];
	$inf['position']['region'] = $local[5];
	$inf['position']['pays'] = $local[4];
	$inf['game'] = get_game();
	$inf['follow'] = get_folowers($inf['game']);
	if (!empty($inf['game']) || !empty($inf['follow']))
		$inf['oauth'] = 1;
	else
		$inf['oauth'] = 0;
	$inf['co'] = connected();
	$inf['val_barre'] = val_barre();
	$inf['time_barre'] = time_barre($inf['val_barre']);
	$inf['musique'] = get_musique($tab_musique);
	$inf['song'] = get_song($tab_musique, $inf['musique']);
	return ($inf);
}

?>