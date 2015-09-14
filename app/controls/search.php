<?php
defined("_nova_district_token_") or die('');


if (isset($_POST['searchname']) and Tools::checkName($_POST['searchname']))
	$docsearch = DoctorsManager::instance()->searchByName($_POST['searchname']);
else if (isset ($_POST['rechsp']))
{
	$departement = null;
	if(isset ($_POST['department']) and !isset($_POST['closest']) AND $_SESSION['user']->getProfile("code postal") != "Non renseigné")
		$departement = substr($_SESSION['user']->getProfile("code postal"), 0, 2);
		
	$docsearch = DoctorsManager::instance()->searchBySpeciality($_POST['rechsp'], $departement);
}
else
	$docsearch = "<div><p>Aucun résultat...</p></div>";




//On inclut la vue
include(dirname(__FILE__).'/../views/search.php');

?>