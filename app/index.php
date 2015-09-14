<?php
define("_nova_district_token_", TRUE);
include_once "../init.php";

//controle d'acces 
if(!isset($_SESSION['user']) OR $_SESSION['user']->getIdMember() <= 0)
	header("Location: ../index.php");
	

if(isset($_GET['p']))
	$_SESSION['page'] = Tools::secure($_GET['p']);
else
	$_SESSION['page'] = "home";

	
//première connexion
if(!isset($_SESSION['user']->getProfile()['nom']) OR $_SESSION['user']->getProfile()['nom'] == "")
	$_SESSION['page'] = "welcome";

	
//header
include_once "controls/header.php";

//traitement de la vue courante (corps de la page)
if(isset($_SESSION['page']) AND $_SESSION['page'] != ""){
	$link = "controls/".$_SESSION['page'].".php";
	
	if(file_exists($link) AND $_SESSION['page'] != "header" AND $_SESSION['page'] != "footer"){
		include_once $link;
	}
	else
		include_once "controls/home.php";
}
else {
	include_once "controls/home.php";
}

//footer
include_once "../controls/footer.php";
?>

