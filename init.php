<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); 

defined("_nova_district_token_") or die('');

ini_set('session.use_trans_sid', "0"); // Spécifie si le support du SID est transparent ou pas
ini_set('session.use_cookies', "1"); // Spécifie si le module utilisera les cookies pour stocker les données de session sur le client
ini_set('session.use_only_cookies', "1"); // Spécifie si le module doit utiliser seulement les cookies pour stocker les identifiants de sessions du côté du navigateur. En l'activant, vous éviterez les attaques qui utilisent des identifiants de sessions dans les URL. Cette configuration a été ajoutée en PHP 4.3.0
ini_set("url_rewriter.tags",""); // Ne plus rien réécrire dans la source
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR');

require_once("Autoload.class.php"); //avant la session_start sinon problemes de désérialisation

session_start();

$_SESSION['token'] = md5(uniqid().time());
?>