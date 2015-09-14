<?php
defined("_nova_district_token_") or die('');

//VERIFICATION DE L'URL & RECUPERATION DU DOCTEUR
$calendrier = "";
if(isset($_GET['doctor']) AND is_numeric($_GET['doctor'])){
	$doctor = DoctorsManager::instance()->getDoctor(intval($_GET['doctor']));

	if(Tools::getClass($doctor) == "Doctor"){
		if(isset($_GET['start'])){ 
			if(preg_match("#[0-9]{4}\-[0-9]{2}\-[0-9]{2}#", $_GET['start'])){  // forme dd-mm-yyyy : index.php?p=booking&start=18-05-2014
				$tmp = explode("-", $_GET['start']);
				$dateStart = new DateTime();
				
				$dateStart->setDate(intval($tmp[0]), intval($tmp[1]), intval($tmp[2]));
			}
		}

		if(!isset($_GET['start']) OR !isset($dateStart))
			$dateStart = new DateTime(date("Y-m-d H:i:s"));
	}
	else
		Tools::redirect("home");
}
else
	Tools::redirect("home");

	
//PRISE D'UN RENDEZ-VOUS
if(isset($_POST['date-rdv']) AND isset($_POST['time-rdv']) AND isset($_POST['tk']) AND $_POST['tk'] == $_SESSION['token2'] AND $_SESSION['user']->getIdMember() != $_GET['doctor'])
{
	if (Tools::checkDate($_POST['date-rdv']) AND Tools::checkTime($_POST['time-rdv']) AND isset($_POST['valid-rdv']) )
	{
		$sc = new Schedule();
		$sc->setIdDoctor($doctor->getIdMember());
		$sc->setIdMember($_SESSION['user']->getIdMember());
		$sc->setDateStart(strtotime($_POST['date-rdv']." ".$_POST['time-rdv'].":00"));
		$sc->setDateStop($sc->getDateStart() + $doctor->getRdvDuration() * 60);
		
		if($doctor->getRdvConfirm() == 0)
			$sc->setValidate(1);
		
		if(isset($_POST['note']))
			$sc->setNote(Tools::secure($_POST['note']));
		
		$dayStartHour = strtotime($_POST['date-rdv']." ".$doctor->getStartHour('h').":".$doctor->getStartHour('m').":00");
		$result = SchedulesManager::instance()->getLastBlockedBlockHour($doctor->getIdMember(), $dayStartHour, $sc->getDateStart());
		
		$calage = abs($sc->getDateStart() - $result);
		$calage = $calage % ($doctor->getRdvDuration() * 60);
		
		if($calage != 0)
			$errors['rdv'] = new Error("Vous ne pouvez pas prendre un rendez-vous en dehors des heures fixées par le calendrier.");
		else {
			$result = SchedulesManager::instance()->add($sc);
			
			if(Tools::getClass($result) == "Error")
				$errors["rdv"] = $result;
			else
				$errors['rdv'] = new Error("Le rendez-vous a bien été pris auprès de votre médecin", "info");
		}
	}
	else
		$errors['rdv'] = new Error("Les informations entrées ne sont pas valides");
}

//AJOUT DU MEDECIN AUX FAVORIS !
if(isset($_POST['add-to-favorite']))
	$errors['add-to-favorite'] = UsersManager::instance()->addDoctorToFavorites($_SESSION['user']->getIdMember(), $doctor->getIdMember());
if(isset($_POST['remove-to-favorite']))
	$errors['add-to-favorite'] = UsersManager::instance()->removeDoctorFromFavorites($_SESSION['user']->getIdMember(), $doctor->getIdMember());

$favoriteButtonAction = "add";
$titleButtonAction = "Ajout aux favoris";
if(UsersManager::instance()->checkFavoritesDoc($_SESSION['user']->getIdMember(), $doctor->getIdMember())) {
	$favoriteButtonAction = "remove";
	$titleButtonAction = "Retirer des favoris";
}

//RECUPERATION DU CALENDRIER
$calendrier = Calendar::instance()->getDoctorCalendar($dateStart, $doctor);
	
//On inclut la vue
include(dirname(__FILE__).'/../views/booking.php');

?>