<?php

require_once('setup/database.php');
extract($_POST);
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id']))
    exit(0);

$e = explode("_", $id);
$id = $e[1];


if (isset($msg) && !empty($msg)) {
	$msg = htmlspecialchars($msg);
	$msg = str_replace(":)", "&#128512;", $msg);
	$msg = str_replace("^^", "&#128513;", $msg);
	$msg = str_replace(":')", "&#128514;", $msg);
	$msg = str_replace(";)", "&#128527;", $msg);
	$msg = str_replace("(-_-)", "&#128542;", $msg);
	$msg = str_replace("&lt;3", "&hearts;", $msg);
	$msg = str_replace("8)", "&#129299;", $msg);
	$msg = str_replace(":p", "&#128539;", $msg);
	$msg = str_replace(";p", "&#128540;", $msg);
	$msg = str_replace(":(", "&#128577;", $msg);
	$msg = str_replace(":o", "&#128559;", $msg);
	if (preg_match("#http://#", $msg) || preg_match("#https://#", $msg)) {
		$t_l = explode("http", $msg);
		$t_l[1] = 'http'.$t_l[1];
		if (filter_var($t_l[1], FILTER_VALIDATE_URL)) {
			$link = explode("/", $t_l[1]);
			if (isset($link[3])) {
				$ch = explode("=", $link[3]);
				if (preg_match("#watch?#", $ch[0])) {
						$t_l[1] = str_replace("watch?v=", "embed/", $t_l[1]);
						$msg = $t_l[0]."<br>".'<iframe src="'.$t_l[1].'"></iframe>';
				} else if (preg_match("#.jpg#", $t_l[1]) || preg_match("#.jpeg#", $t_l[1]) || preg_match("#.png#", $t_l[1])) {
                    $msg = $t_l[0]."<br>".'<img src="'.$t_l[1].'" class="img_in_chat">';
                } else {
                    $msg = $t_l[0]."<br><div class='div_site'>".get_page($t_l[1])."</div>";
                }
			} else if (preg_match("#.jpg#", $t_l[1]) || preg_match("#.jpeg#", $t_l[1]) || preg_match("#.png#", $t_l[1])) {
				$msg = $t_l[0]."<br>".'<img src="'.$t_l[1].'" class="img_in_chat">';
			} else {
				$msg = $t_l[0]."<br><div class='div_site'>".get_page($t_l[1])."</div>";
			}
		}
	}
    if (check_tab($id, $bdd) && check_bloque($id, $bdd)) {
        $insert_msg = $bdd->prepare("INSERT INTO message(id_p1, id_p2, message, vu, id_envoyeur) VALUES(?, ?, ?, ?, ?)");
	   $insert_msg->execute(array($_SESSION['id'], $id, $msg, 0, $_SESSION['id']));
    }
}
if (check_tab($id, $bdd) && check_bloque($id, $bdd)) {
    $get_msg = $bdd->prepare("SELECT * FROM message WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?) ORDER BY id DESC");
    $get_msg->execute(array($_SESSION['id'], $id, $id, $_SESSION['id']));
    $msgs = $get_msg->fetch();
    if ($msgs['id_p1'] == $_SESSION['id']) {
    	echo "<span class='right'>".$msgs['message']."</span><div class='clear'></div>";
    } else {
    	echo "<span class='left'>".$msgs['message']."</span><div class='clear'></div>";
    }
} else {
    echo "<span class='right'>Vous ne matchez plus avec l'utilisateur ou l'utilisateur vous a bloque</span><div class='clear'></div>";
}

function check_bloque($id, $bdd) {
    $get_b = $bdd->prepare("SELECT * FROM bloquer WHERE (id_bloquer = ? OR id_bloquer = ?) AND (id_bloqueur = ? OR id_bloqueur = ?)");
    $get_b->execute(array($_SESSION['id'], $id, $_SESSION['id'], $id));
    if ($get_b->rowCount() > 0)
        return (0);
    return (1);
}

function check_tab($id, $bdd) {
    $get_m = $bdd->prepare('SELECT * FROM matchs WHERE (id_p1 = ? OR id_p1 = ?) AND (id_p2 = ? OR id_p2 = ?)AND match_p1 = ? AND match_p2 = ?');
    $get_m->execute(array($_SESSION['id'], $id, $_SESSION['id'], $id, 1, 1));
    if ($get_m->rowCount() > 0)
        return (1);
    return (0);
}


function get_page($url) {
	// Extract HTML using curl
    if (!url_exists($url)) {
        return ('Lien mort');
    }
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    
    $data = curl_exec($ch);
    curl_close($ch);
    
    // Load HTML to DOM Object
    $dom = new DOMDocument();
    @$dom->loadHTML($data);
    
    // Parse DOM to get Title
    $nodes = $dom->getElementsByTagName('title');
    $title = $nodes->item(0)->nodeValue;
    
    // Parse DOM to get Meta Description
    $metas = $dom->getElementsByTagName('meta');
    $body = "";
    for ($i = 0; $i < $metas->length; $i ++) {
        $meta = $metas->item($i);
        if ($meta->getAttribute('name') == 'description') {
            $body = $meta->getAttribute('content');
        }
    }
    
    // Parse DOM to get Images
    $image_urls = array();
    $images = $dom->getElementsByTagName('img');
     
    for ($i = 0; $i < $images->length; $i ++) {
        $image = $images->item($i);
        $src = $image->getAttribute('src');
         
        if(filter_var($src, FILTER_VALIDATE_URL)) {
            $image_src[] = $src;
        }
    }
    if (isset($image_src)) {
        if (isset($image_src[1]))
            $link = "<a href='".$url."' target='_blank'>".$url."</a><h4>".$title."</h4><br><img src='".$image_src[1]."' class='img_in_chat1'><br><p>".$body."</p>";
        else
            $link = "<a href='".$url."' target='_blank'>".$url."</a><h4>".$title."</h4><br><img src='".$image_src[0]."' class='img_in_chat1'><br><p>".$body."</p>";
    } else
    	$link = "<a href='".$url."' target='_blank'>".$url."</a><h4>".$title."</h4><br><p>".$body."</p>";
    return ($link);
}

function url_exists($url_a_tester) {
    $F = @fopen($url_a_tester,"r");
     
    if($F) {
        fclose($F);
        return true;
    }
    else return false;
}

?>