<?php 
defined("_nova_district_token_") or die('');

if(isset($_SESSION['user']->getProfile()['nom']) AND $_SESSION['user']->getProfile()['nom'] != "")
	Tools::redirect("home");
	

//contrôle du formulaire d'inscription final
if(isset($_POST['name']) AND isset($_POST['sexe']) AND isset($_POST['firstname']) AND isset($_POST['zip']) AND isset($_POST['city']) AND isset($_POST['address']) 
	AND isset($_POST['phone']) AND isset($_POST['birthday']))
{	
	$_POST['birthday'] = str_replace("/", "-", $_POST['birthday']);
	$temp = explode("-", $_POST['birthday']);
	if(intval($temp[0]) <= 31)
		$_POST['birthday'] = implode("-", array_reverse($temp));

	if (Tools::checkSexe($_POST['sexe']) AND Tools::checkName($_POST['name']) AND Tools::checkName($_POST['firstname']) AND Tools::checkZip($_POST['zip']) AND Tools::checkCity($_POST['city']) 
		AND Tools::checkAddress($_POST['address']) AND Tools::checkPhone($_POST['phone']) AND Tools::checkBirthday($_POST['birthday']))
	{
		$tab['nom'] = strtolower($_POST['name']);
		$tab['sexe'] = strtolower($_POST['sexe']);
		$tab['prénom'] = strtolower($_POST['firstname']);
		$tab['code postal'] = ($_POST['zip']);
		$tab['ville'] = strtolower($_POST['city']);
		$tab["complément d'adresse"] = strtolower($_POST['address']);
		$tab['téléphone'] = $_POST['phone'];
		$tab['date de naissance'] = $_POST['birthday'];

		//création du profil
		$_SESSION['user']->addToProfile($tab);
		$result = UsersManager::instance()->saveProfile($_SESSION['user']);
		
		if($result == true) { //ok
			if(isset($_POST['pratician']) AND intval($_POST['pratician']) != 0) //si praticien
				UsersManager::instance()->askToBecomePratician($_SESSION['user']->getIdMember(), intval($_POST['pratician']));
				
			Tools::redirect("home");
		}
		else 
		{
			$_SESSION['user']->setProfile(array()); //vide le profil utilisateur pour être sur qu'on reste sur la page welcome
			$errors['profil'] = new Error("Une erreur est survenue, merci de recommencer. Si celle-ci persiste, contactez un administrateur !");
		}
	}
	else 
		$errors['profil'] = new Error("Attention aux types des champs, certains ne sont pas valides");
}


$medicinesList = DoctorsManager::instance()->getMedicines();

include(dirname(__FILE__).'/../views/welcome.php');
?>