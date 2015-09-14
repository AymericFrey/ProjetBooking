<?php
defined("_nova_district_token_") or die('');


if($_SESSION['user']->getLevel() != "admin")
	Tools::redirect("home");

$menu = 'members';
if (isset($_GET['m'] ))
{
	$sousMenus = array ('members', 'practicians');
	
	if (in_array ($_GET['m'], $sousMenus))
	{
		$menu = $_GET['m'];
	}
}	

$futureDoctors = AdminManager::instance()->countDoctorsInWait();

//On inclut le sous-menu correspondant
include(dirname(__FILE__).'/modules/admin-'.$menu.'.php');
?>