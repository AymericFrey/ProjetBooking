<?php
defined("_nova_district_token_") or die('');


//RECUPERATION DE LA RECHERCHE
if (isset($_POST['search-doctorname']) AND $_POST['search-doctorname'] != "")
{
	$doctor_result = DoctorsManager::instance()->searchByName($_POST['search-doctorname']);
	if(count($doctor_result) == 0)
	{
		$error = new Error('Aucun médecin trouvé à ce nom');
		$errors['admin_doctors'] = $error;
	}	
}

	
//ENVOI D'UN MSG
if(isset($_POST['message_to_user']) AND isset($_GET['msg_id']))
{
	$message = AdminManager::instance()->sendMessage($_GET['msg_id'], $_POST['message_to_user'] );
	$errors["msg-admin-members"] = $message;
}

//CHANGEMENT DE STATUT
if(isset($_GET['id']) AND isset($_GET['idmed']) AND isset($_POST['status']))
{
	$statusChange = AdminManager::instance()->changeStatus($_GET['id'], $_POST['status'], $_GET['idmed']);
	if(Tools::getClass($statusChange) == "Error")
		$errors["status_change"] = $statusChange;

}

//RECUPERATION DES DEMANDES EN COURS
$future_doctor_result = AdminManager::instance()->searchAllFutureDoctors();
if(Tools::getClass($future_doctor_result) == "Error")
	$errors["admin_future_doctors"] = $future_doctor_result;
	

//inclusion de la vue correspondante
include(dirname(__FILE__).'/../../views/modules/admin-practicians.php');
?>