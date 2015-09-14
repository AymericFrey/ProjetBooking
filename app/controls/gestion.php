<?php
defined("_nova_district_token_") or die('');

//on filtre si la personne n'est pas un praticien !
if(Tools::getClass($_SESSION['user']) != "Doctor")
	Tools::redirect("home");



//on regarde quel sous-controlleur on inclut ********************************
$menu = "daily";
if(isset($_GET['m']))
{
	$sousmenus = array('block', 'config');

	if(in_array($_GET['m'] , $sousmenus))
		$menu = $_GET['m'];
}

//inclusion du sous-controleur qui se chargera d'inclure la vue correspondante !
include(dirname(__FILE__).'/modules/gestion-'.$menu.'.php');


?>