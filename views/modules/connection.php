<?php
defined("_nova_district_token_") or die('');

$title = "Accédez à votre espace";
if(UsersManager::instance()->isConnected())
	$title = "Bienvenue";
?>
<div id='header-connection' class='small-block' >
	<div class='small-block-title'><h2><?php echo $title; ?></h2></div>
	<div class='small-block-content'>
		<?php 
		if(isset($errors['connection']))
			echo $errors['connection']->getHTML();
		
		if(!UsersManager::instance()->isConnected())
		{ ?>
			<form action='index.php' method='post'>
				<label for=''>Identifiant (email)</label>
				<input type='email' name='email' value='' />
				
				<label for=''>Mot de passe</label>
				<input type='password' name='pass' value='' />
				
				<input type='submit' class='custom-submit-blue custom-submit' name='connection'  value='Se connecter'/>
			</form>
		<?php 
		}
		else 
		{ ?>
			<ul>
				<li><a href='app/index.php'>Accéder à la plateforme</a></li>
				<li><a href='app/index.php?p=profile'>Voir mon profil</a></li>
				<li><a href='index.php?a=deco'><strong>Me déconnecter</strong></a></li>
			</ul>
		<?php 
		} ?>
	</div>
</div>