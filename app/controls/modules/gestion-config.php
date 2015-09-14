<?php
defined("_nova_district_token_") or die('');

if(isset($_POST['form-horaires'])){
	if(isset($_POST['hdebut']) AND isset($_POST['hfin']) AND isset($_POST['dureerdv']))
	{
		if(Tools::checkTime($_POST['hdebut']) AND Tools::checkTime($_POST['hfin']) AND is_numeric($_POST['dureerdv']))
		{		
			$_SESSION['user']->setRdvDuration($_POST['dureerdv']);
			$_SESSION['user']->setStartHour($_POST['hdebut']);
			$_SESSION['user']->setEndHour($_POST['hfin']);

			if(isset($_POST['confirmrdv']))
				$_SESSION['user']->setRdvConfirm(1);
			else
				$_SESSION['user']->setRdvConfirm(0);

			// METTRE A JOUR les informations dans la BDD : on récupère un objet de type ERROR
			$errors['gestion-edit'] = DoctorsManager::instance()->updateProfile($_SESSION['user']);
		}
		else
			$errors['gestion-horaires'] = new Error("Veuillez indiquer une heure ou durée de RDV correcte (en minutes, ex: 20)");
	}
}
else if (isset($_POST['form-infopro'])){
	if(isset($_POST['infopro']))
	{
	//	$_SESSION['user']->setInfoPro(Tools::secure($_POST['infopro']);
			$_SESSION['user']->setInfoPro($_POST['infopro']);
		// METTRE A JOUR les informations dans la BDD : on récupère un objet de type ERROR
		$errors['gestion-infopro'] = DoctorsManager::instance()->updateProfile($_SESSION['user']);
	}
}



//vue
include(dirname(__FILE__).'/../../views/modules/gestion-config.php');
?>