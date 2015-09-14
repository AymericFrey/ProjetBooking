<?php
defined("_nova_district_token_") or die('');

//token 2
$_SESSION['token2'] = $_SESSION['token'];

$registersMembers = UsersManager::instance()->countMembers();
$registersDoctors = DoctorsManager::instance()->countDoctors();

//On inclut la vue
include(dirname(__FILE__).'/../views/footer.php');
?>