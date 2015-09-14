<?php
defined("_nova_district_token_") or die('');

// formulaire d'inscription
if(isset($_POST['inscription']) AND isset($_POST['email']) AND isset($_POST['pass']))
{
	if(Tools::checkEmail($_POST['email']) AND Tools::checkPassword($_POST['pass']) AND $_POST['pass'] == $_POST['pass2']){
		$mb = new Member();
		
		$mb->setEmail(Tools::secure($_POST['email']));
		$mb->setPassword(Tools::secure(Tools::hash($_POST['pass'])));

		$result = UsersManager::instance()->add($mb);
		
		if(Tools::getParentClass($result) == "User"){
			$_SESSION['user'] = $result;
			die('<meta http-equiv="refresh" content="0;URL=app/index.php?p=welcome">');
		}
		else if(Tools::getClass($result) == "Error"){
			$errors["inscription"] = $result;
		}
		else
			$errors['inscription'] = new Error();
	}
	else
		$errors['inscription'] = new Error("Votre email est invalide ou vos mots de passes ne sont pas identiques !");
}

//On inclut la vue
include(dirname(__FILE__).'/../views/inscription.php');

?>