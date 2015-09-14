<?php
defined("_nova_district_token_") or die('');

$inscriptionField = "<div class='custom-button-orange custom-button'><a href='index.php?p=inscription'>s'inscrire</a></div>";
if(UsersManager::instance()->isConnected())
	$inscriptionField = $_SESSION['user']->getEmail();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />	
	<title>Booking</title>
	<meta name='description' content="" />
	<meta name='keywords' content='' />
	
	<link href='css/common.css' rel='stylesheet' type='text/css' media='screen' />
	<link href='css/design.css' rel='stylesheet' type='text/css' media='screen' />
	
	<link rel='icon' type='image/png' href='img/favicon.png'/>
	
    <script type='text/javascript' src='js/jquery-1.11.js'></script>
    <script type='text/javascript' src='app/js/common.js'></script>
</head>
<body>
	<header>
		<div id='header-top'>
			<div id='header-logo'>
				<a href='index.php'><img src='img/logo.png' alt='logo' /></a>
			</div>
			<ul>
				<li><a href='index.php'>accueil</a></li>
				<li><?php echo $inscriptionField; ?></li>
			</ul>
		</div>
		<div id='header-image'>
			<div id='header-image-content'>
				<div id='header-image-infos'>
					<h2>Vos horaires et vos praticiens</h2>
					<ul>
						<li>Sélectionnez les horaires qui vous conviennent selon vos disponibilités directement dans l’emploi du temps de votre praticien !</li>
						<li>Recevez une confirmation lorsque votre spécialiste valide votre prise de rendez-vous !</li>
						<li>Un problème pour être au rendez-vous ? Modifiez ou supprimez celui-ci quand vous le souhaitez !</li>
					</ul>
				</div>
				<?php include "views/modules/connection.php"; ?>
			</div>
		</div>
	</header>
	<div id='content'>