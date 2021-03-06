<?php
/*  GOOGLE LOGIN BASIC - Tutorial
 *  file            - index.php
 *  Developer       - Krishna Teja G S
 *  Website         - http://packetcode.com/apps/google-login/
 *  Date            - 28th Aug 2015
 *  license         - GNU General Public License version 2 or later
*/
// REQUIREMENTS - PHP v5.3 or later
// Note: The PHP client library requires that PHP has curl extensions configured. 
/*
 * DEFINITIONS
 *
 * load the autoload file
 * define the constants client id,secret and redirect url
 * start the session
 */
require_once __DIR__.'/vendor/autoload.php';
const CLIENT_ID = '';
const CLIENT_SECRET = '';
const REDIRECT_URI = 'http://192.168.99.100.xip.io:41062/www/matcha/google_connexion/index.php';
require_once('../setup/database.php');
session_start();
/* 
 * INITIALIZATION
 *
 * Create a google client object
 * set the id,secret and redirect uri
 * set the scope variables if required
 * create google plus object
 */
$client = new Google_Client();
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(REDIRECT_URI);
$client->setScopes('email');
$plus = new Google_Service_Plus($client);
/*
 * PROCESS
 *
 * A. Pre-check for logout
 * B. Authentication and Access token
 * C. Retrive Data
 */
/* 
 * A. PRE-CHECK FOR LOGOUT
 * 
 * Unset the session variable in order to logout if already logged in    
 */
if (isset($_REQUEST['logout'])) {
 session_unset();
}
/* 
 * B. AUTHORIZATION AND ACCESS TOKEN
 *
 * If the request is a return url from the google server then
 *  1. authenticate code
 *  2. get the access token and store in session
 *  3. redirect to same url to eleminate the url varaibles sent by google
 */
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}
/* 
 * C. RETRIVE DATA
 * 
 * If access token if available in session 
 * load it to the client object and access the required profile data
 */
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  $me = $plus->people->get('me');
  // Get User data
  $name =  $me['displayName'];
  $email =  $me['emails'][0]['value'];
  $profile_image_url = $me['image']['url'];
} else {
  // get the login url   
  $authUrl = $client->createAuthUrl();
}


function create_mdp($length) {
  $caracteres = 'abcdefghijklmnopqrstuvwxyz123456789!@#$%^&*';
  $longueurMax = strlen($caracteres);
  $chaineAleatoire = '';
  $i = 0;
  while ($i < $length) {
      $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
    $i++;
  }
  return ($chaineAleatoire);
}
?>

<!-- HTML CODE with Embeded PHP-->
<div>
  <?php
    /*
     * If login url is there then display login button
     * else print the retieved data
    */
    if (isset($authUrl)) {
      echo "<a class='login' href='" . $authUrl . "'><img src='photos/google_connexion.jpg' class='img_google_connection'></a>";
    } else {
      $req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
      $req_bdd->execute(array($name));
      if ($req_bdd->rowCount() > 0) {
        $get_user = $req_bdd->fetch();
        if ($get_user['oauth'] == 1) {
          $_SESSION['id'] = $get_user['id'];
         $is_connected = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
         $is_connected->execute(array($get_user['id']));
         if ($is_connected->rowCount() > 0) {
          $connected = $bdd->prepare('UPDATE connected SET co = ? WHERE id_user = ?');
          $connected->execute(array(1, $get_user['id']));
         } else {
          $connected = $bdd->prepare('INSERT INTO connected(id_user, co) VALUES(?, ?)');
          $connected->execute(array($get_user['id'], 1));
         }
          header("Location:../index.php");
        } else {
          $a = 0;
          $i = 1;
          while ($a == 0) {
            $name = $name.$i;
            $req_bdd = $bdd->prepare('SELECT * FROM membre WHERE nom_utilisteur = ?');
            $req_bdd->execute(array($name));
            if ($req_bdd->rowCount() == 0)
              $a = 1;
            $i++;
          }
          $id = explode(" ", $name);
          $req = $bdd->prepare("INSERT INTO membre(nom, prenom, mail, mdp, sexe, attirance, description, photos, photos_pro, age, verif, nom_utilisteur, interets, oauth) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
          $req->execute(array($id[1], $id[0], $email, sha1(create_mdp(rand(6, 20))), 'A', 'A', 0, $profile_image_url, $profile_image_url, 0, 1, $name, 0, 1));
          $is_connected = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
          $is_connected->execute(array($get_user['id']));
          if ($is_connected->rowCount() > 0) {
           $connected = $bdd->prepare('UPDATE connected SET co = ? WHERE id_user = ?');
           $connected->execute(array(1, $get_user['id']));
          } else {
           $connected = $bdd->prepare('INSERT INTO connected(id_user, co) VALUES(?, ?)');
           $connected->execute(array($get_user['id'], 1));
          }
        }
      } else {
        if (!empty($name))
          $id = explode(" ", $name);
        else {
          $id[0] = "";
          $id[1] = 0;
        }
        $req = $bdd->prepare("INSERT INTO membre(nom, prenom, mail, mdp, sexe, attirance, description, photos, photos_pro, age, verif, nom_utilisteur, interets, oauth) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $req->execute(array($id[1], $id[0], $email, sha1(create_mdp(rand(6, 20))), 'A', 'A', 0, $profile_image_url, $profile_image_url, 0, 1, $name, 0, 1));
        $is_connected = $bdd->prepare('SELECT * FROM connected WHERE id_user = ?');
        $is_connected->execute(array($get_user['id']));
        if ($is_connected->rowCount() > 0) {
         $connected = $bdd->prepare('UPDATE connected SET co = ? WHERE id_user = ?');
         $connected->execute(array(1, $get_user['id']));
        } else {
         $connected = $bdd->prepare('INSERT INTO connected(id_user, co) VALUES(?, ?)');
         $connected->execute(array($get_user['id'], 1));
        }
      }
    }
    ?>
  </div>
