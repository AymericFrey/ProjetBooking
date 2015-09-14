<?php
defined("_nova_district_token_") or die('');


// Suppression des vieilles Alertes de plus de 2 mois
AlertsManager::instance()->clean();


// Suppression d'une alerte 
if(isset($_GET['a']) AND isset($_GET['id']))
{
	if($_GET['a']=="del")
	{
		AlertsManager::instance()->delete($_SESSION['user']->getIdMember(),$_GET['id']);
		// AlertsManager::instance()->deleteAlert($_SESSION['user']->getIdMember(),$_GET['id']);
	}

}

if(isset($_GET['a']))
{
	if($_GET['a']=="deleteall")
	{
		AlertsManager::instance()->delete($_SESSION['user']->getIdMember());
		//permet de recharger la page sans les indications de _GET dans l'url
		Tools::redirect("alert");
	}
}


$alerts= AlertsManager::instance()->getAlert($_SESSION['user']->getIdMember());

AlertsManager::instance()-> changeState($_SESSION['user']->getIdMember());

//On inclut la vue
include(dirname(__FILE__).'/../views/alert.php');
?>