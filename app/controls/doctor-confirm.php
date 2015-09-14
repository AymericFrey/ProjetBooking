<?php
defined("_nova_district_token_") or die('');

if(isset($_GET['rdva']))
	$id_schedule = intval($_GET['rdva']);
	
$message = "";

if (isset($id_schedule) AND $id_schedule != "")
{
	$result = SchedulesManager::instance()->cancelSchedule($_SESSION['user']->getIdMember(), $_GET['rdvc']);
	
	if(Tools::getClass($result) == "Schedule") //succès : on envoi une alerte à la personne concernée
	{
		$errors['confirm'] = new Error("Le rendez-vous a bien été confirmé. Le patient recevra bientôt averti de cette confirmation", "info");
		AlertsManager::instance()->generateAutomaticMessage($_SESSION['user']->getIdMember(), $result, 5);
	}
	else
		$errors['confirm'] = $result;
}


//On inclut la vue
include(dirname(__FILE__).'/../views/doctor-confirm.php');

?>