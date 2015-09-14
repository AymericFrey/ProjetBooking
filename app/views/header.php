<?php
defined("_nova_district_token_") or die('');

$optionsPratician = "";
if(Tools::getClass($_SESSION['user']) == "Doctor")
	$optionsPratician = "<li><a href='index.php?p=gestion'>Gestion</a></li>";
$optionsAdmin = "";
if($_SESSION['user']->getLevel() == "admin")
	$optionsAdmin = "<li><a href='index.php?p=admin'>Admin</a></li>";
	
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />	
	<title>Booking</title>
	<meta name='description' content="" />
	<meta name='keywords' content='' />
	
	<link href='../css/common.css' rel='stylesheet' type='text/css' media='screen' />
	<link href='css/design.css' rel='stylesheet' type='text/css' media='screen' />
	
	<link rel='icon' type='image/png' href='../img/favicon.png'/>
	
    <script type='text/javascript' src='../js/jquery-1.11.js'></script>
	<?php echo JSLoader::loader($_SESSION['page']); ?>
</head>
<body>
	<header>
		<div id='header-top'>
			<div id='header-logo'>
				<a href='index.php'><img src='../img/logo.png' alt='logo' /></a>
			</div>
			<ul>
				<li><a href='index.php'>Panneau principal</a></li>
				<?php echo $optionsPratician; ?>
				<li><a href='index.php?p=alert'>Alertes (<?php echo AlertsManager::instance()->getNumberNewAlert($_SESSION['user']->getIdMember()); ?>)</a></li>
				<li><a href='index.php?p=profile'>Profil</a></li>
				<?php echo $optionsAdmin; ?>
				<li><a href='../index.php?a=deco'>Déconnexion</a></li>
			</ul>
		</div>
		<div id='header-infos'>
			
		</div>
	</header>