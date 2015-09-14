<?php
defined("_nova_district_token_") or die('');


//RECUPERATION DE LA DATE SELECTIONNEE OU PAR DEFAUT
$day = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
if(isset($_GET['chosen-date']) AND Tools::checkDate($_GET['chosen-date']))
{
	$temp = explode('-', $_GET['chosen-date']);
	if(isset($temp[0]) AND isset($temp[1]) AND isset($temp[2]))
		$day = mktime(0, 0, 0, intval($temp[1]), intval($temp[2]), intval($temp[0]));
}

//SUPPRESSION D'UN RDV
if(isset($_GET['a']) AND $_GET['a'] == "del" AND isset($_GET['rdv']) AND is_numeric($_GET['rdv']))        
{
	$result = SchedulesManager::instance()->cancelSchedule($_SESSION['user']->getIdMember(), intval($_GET['rdv']));
	
	if(Tools::getClass($result) == "Error")
		$errors['daily-delete'] = $result;
	else
		AlertsManager::instance()->generateAutomaticMessage($result->getIdMember(), $result, 4);
}

//VALIDATION D'UN RDV
if(isset($_GET['a']) AND $_GET['a'] == "val" AND isset($_GET['rdv']) AND is_numeric($_GET['rdv']))   
{
	$result = SchedulesManager::instance()->confirmSchedule($_SESSION['user']->getIdMember(), intval($_GET['rdv']));
	
	if(Tools::getClass($result) == "Error")
		$errors['daily-validate'] = $result;
	else
		AlertsManager::instance()->generateAutomaticMessage($result->getIdMember(), $result, 5);
}


$dailyScheduleList = SchedulesManager::instance()->getScheduleList($_SESSION['user']->getIdMember(), $day, $day + 24*3600);

//vue
include(dirname(__FILE__).'/../../views/modules/gestion-daily.php');
?>