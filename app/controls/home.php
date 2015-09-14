<?php
defined("_nova_district_token_") or die('');

//suppression d'un RDV par le membre
if(isset($_GET['rdv']) AND is_numeric($_GET['rdv']) AND isset($_GET['doctor']) AND is_numeric($_GET['doctor']))
{
	$result = SchedulesManager::instance()->cancelSchedule($_SESSION['user']->getIdMember(), intval($_GET['rdv']));
	
	if(Tools::getClass($result) == "Schedule")  //succès : on envoi une alerte à la personne concernée
	{
		AlertsManager::instance()->generateAutomaticMessage(intval($_GET['doctor']), $result, 4);
		AlertsManager::instance()->generateAutomaticMessage($_SESSION['user']->getIdMember(), $result, 0);
	}
	else
		$errors['cancel'] = $result;
}

//On inclut le modèle
$futursRdv = SchedulesManager::instance()->getFutureSchedule($_SESSION['user']->getIdMember());
$favorites = UsersManager::instance()->getFavoritesDoc($_SESSION['user']->getIdMember());

//On inclut la vue
include(dirname(__FILE__).'/../views/home.php');

?>