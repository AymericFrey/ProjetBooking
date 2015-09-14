<?php
define("_nova_district_token_", TRUE);
include_once "init.php";

include_once "models/Tools.class.php";

if(isset($_GET['p']))
	$page = Tools::secure($_GET['p']);
else
	$page = "home";

//header
include_once "controls/header.php";

//traitement de la vue courante (corps de la page)
if(isset($page) AND $page != ""){
	$link = "controls/".$page.".php";
	
	if(file_exists($link) AND $page != "header" AND $page != "footer"){
		include_once $link;
	}
	else
		include_once "controls/home.php";
}
else {
	include_once "controls/home.php";
}

//footer
include_once "controls/footer.php";
?>

