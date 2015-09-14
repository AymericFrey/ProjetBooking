<?php
defined("_nova_district_token_") or die('');

if (isset($_POST['search-username']) AND $_POST['search-username'] != "")
{
	$member_result = AdminManager::instance()->searchUser($_POST['search-username']);
	
	if(Tools::getClass($member_result) == "Error")
		$errors["admin-members"] = $member_result;
}	
	
if(isset($_GET['del_id']) AND is_numeric($_GET['del_id']))
{
	$delete = UsersManager::instance()->delete(intval($_GET['del_id']));
	$errors['delete_member'] = $delete;
}	
	
	
	
	
if(isset($_POST['message_to_user']) AND isset($_GET['msg_id']) AND is_numeric($_GET['msg_id']))
{
	$message = AdminManager::instance()->sendMessage(intval($_GET['msg_id']), $_POST['message_to_user'] );
	$errors["msg-admin-members"] = $message;
}
	
	
	
	
//inclusion de la vue correspondante
include(dirname(__FILE__).'/../../views/modules/admin-members.php');
?>