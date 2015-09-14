<?php
defined("_nova_district_token_") or die('');

if((isset($_GET['rdvc']) AND $_GET['rdvc'] != "" AND isset($_GET['d']) AND $_GET['d'] != "" AND $_SESSION['id-member'] == $_GET['d'])
	OR 
   (isset($_GET['rdva']) AND $_GET['rdva'] != "" AND isset($_GET['d']) AND $_GET['d'] != "" AND $_SESSION['id-member'] == $_GET['d']))
{
	$result = SchedulesManager::instance()->cancelSchedule($_SESSION['user']->getIdMember(), $_GET['rdvc']);
	
	if(Tools::getClass($result) == "Schedule") //succès : on envoi une alerte à la personne concernée
	{
		AlertsManager::instance()->generateAutomaticMessage($_SESSION['user']->getIdMember(), $result, 4);
		$errors['cancel'] = new Error("Le rendez-vous a bien été annulé. Le patient recevra bientôt une alerte pour le prévenir", 'info');
	}
	else
		$errors['cancel'] = $result;
}


//On inclut la vue
include(dirname(__FILE__).'/../views/doctor-cancel.php');

?>