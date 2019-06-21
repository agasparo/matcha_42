<?php

$DB_DSN = 'mysql:host=localhost;dbname=membres';
$DB_USER = 'root';
$DB_PASSWORD = '';

$bdd = new PDO($DB_DSN , $DB_USER, $DB_PASSWORD);

session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
	exit(0);

echo '<div class="return_div"><a href="#" class="list__link" id="back_r" style="color: #d30320;"><i class="fa fa-undo"></i></a></div>';

extract($_POST);

if ($_POST['id'] != "gg" && $_POST['name'] != "gg") {
	$is_in = $bdd->prepare('SELECT * FROM musique WHERE id_musique = ?');
	$is_in->execute(array($id));
	if ($is_in->rowCount() == 0) {
		$put_in = $bdd->prepare('INSERT INTO musique(id_user, id_musique, name_musique) VALUES(?, ?, ?)');
		$put_in->execute(array($_SESSION['id'], $id, $name));
	}
}

$get_in = $bdd->query('SELECT * FROM musique');
$i = 0;
echo "<table class='table_in_m'>";
while ($inf = $get_in->fetch()) {
	echo "<tr><td><img src='https://img.youtube.com/vi/".$inf['id_musique']."/0.jpg' class='img_musique_b' style='width: 100%; height: auto; min-height: 0;'></td><td><p id='".$inf['id_musique']."' class='musique' name='".$i."'>".toUtf8($inf['name_musique'])."</p></td></tr>";
	$i++;
}
echo "</table>";

function toUtf8($string){
	$string = str_replace('Ã¢','â', $string);
	$string = str_replace('Ã¢','â', $string);
	$string = str_replace('Ã©','é', $string);
	$string = str_replace('à©','é', $string);
	$string = str_replace('í©','é', $string);
	$string = str_replace('Ã ','à', $string);
	$string = str_replace('Ã¨','è', $string);
	$string = str_replace('Ã§','ç', $string);
	$string = str_replace('Â«','«', $string);
	$string = str_replace('Â»','»', $string);
	$string = str_replace("â€™","'", $string);
	$string = str_replace('Ãª','ê', $string);
	$string = str_replace('àª','ê', $string);
	$string = str_replace('â‚¬','€', $string);
	$string = str_replace('Ã´','ô', $string);
	$string = str_replace('Ã¤','ä', $string);
	$string = str_replace('Ã¹','ù', $string);
	$string = str_replace('Ã®','î', $string);
	$string = str_replace('à¨','è', $string);
	$string = str_replace('àª','ê', $string);
	$string = str_replace('Å“','œ', $string);
	$string = str_replace('à§','ç', $string);
	$string = str_replace('à»','û', $string);
	$string = str_replace('à®','î', $string);
	$string = str_replace('à´','ô', $string);
	$string = str_replace('à‰','é', $string);
	$string = str_replace('à€','à', $string);
	$string = str_replace('Â','', $string);
	$string = str_replace('Ã€', 'À', $string);
	$string = str_replace('Ã‰', 'É', $string);
	$string = str_replace('â€"', '-', $string);
	return ($string);
}

?>