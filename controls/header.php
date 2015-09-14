<?php
defined("_nova_district_token_") or die('');

//gestion déconnexion
if(isset($_GET['a']) AND $_GET['a'] == "deco")
{
	UsersManager::instance()->disconnect();
}

//gestion connexion
if(!isset($_SESSION['user']) AND isset($_POST['connection'])) 
{
	if(isset($_POST['email']) AND $_POST['email'] != "" AND isset($_POST['pass']) AND $_POST['pass'] != "")
	{
		$result = UsersManager::instance()->connect($_POST['email'], Tools::hash($_POST['pass']));
		if(Tools::getParentClass($result) == "User"){
			$_SESSION['user'] = $result; // la session est ouverte
		}
		else if(Tools::getClass($result) == "Error")
			$errors["connection"] = $result;
		else
			$errors["connection"] = new Error("Echec : Email ou mot de passe invalide");
	}
}

//On inclut la vue
include(dirname(__FILE__).'/../views/header.php');
?>