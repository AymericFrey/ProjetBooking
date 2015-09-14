<?php
defined("_nova_district_token_") or die('');

//contrôle du formulaire d'inscription modifié
if(isset($_POST['name']) AND isset($_POST['firstname']) AND isset($_POST['sexe']) AND isset($_POST['zip']) AND isset($_POST['city']) AND isset($_POST['address']) 
	AND isset($_POST['phone']) AND isset($_POST['birthday']))
{	
	if (Tools::checkName($_POST['name']) AND Tools::checkSexe($_POST['sexe']) AND Tools::checkName($_POST['firstname']) AND Tools::checkZip($_POST['zip']) AND Tools::checkCity($_POST['city']) 
		AND Tools::checkAddress($_POST['address']) AND Tools::checkPhone($_POST['phone']) AND Tools::checkBirthday($_POST['birthday']))
	{
		$tab['sexe'] = Tools::secure(strtolower($_POST['sexe']));
		$tab['nom'] = Tools::secure(strtolower($_POST['name']));
		$tab['prénom'] = Tools::secure(strtolower($_POST['firstname']));
		$tab['code postal'] = Tools::secure($_POST['zip']);
		$tab['ville'] = strtolower($_POST['city']);
		$tab["complément d'adresse"] = Tools::secure(strtolower($_POST['address']));
		$tab['téléphone'] = $_POST['phone'];
		$tab['date de naissance'] = $_POST['birthday'];

		//modification de la session en cours
		$profileTemp = $_SESSION['user']->getProfile();
		
		$_SESSION['user']->addToProfile($tab);
		$result = UsersManager::instance()->saveProfile($_SESSION['user']);
		
		if($result == true) //ok
			Tools::redirect("profile");
		else 
		{
			$_SESSION['user']->setProfile($profileTemp);
			$errors['profil'] = new Error("Une erreur est survenue, merci de recommencer. Si celle-ci persiste, contactez un administrateur !");
		}
	}
	else 
		$errors['profil'] = new Error("Attention aux types des champs, certains ne sont pas valides");
}


//On inclut la vue
include(dirname(__FILE__).'/../views/profile.php');

?>