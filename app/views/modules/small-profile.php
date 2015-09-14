<?php 
defined("_nova_district_token_") or die('');

$docProfile = '<span class="profile-empty"'.$_SESSION['user']->getProfile('médecin généraliste').'</span>';

if($_SESSION['user']->getIdGeneralist())
{
	$doctor = DoctorsManager::instance()->getDoctor($_SESSION['user']->getIdGeneralist());
	
	if(Tools::getClass($doctor) == "Doctor")
	{
		$docProfile = '<a href="index.php?p=booking&doctor='.$_SESSION['user']->getIdGeneralist().'">'.ucfirst($doctor->getProfile('nom')).' '.ucfirst($doctor->getProfile('prénom')).'</a>';
	}
}

?>
<div id='small-profile'>
	<div class='center'>
		<div id='small-profile-left'>
			<img src='img/<?php echo $_SESSION['user']->getProfile('sexe'); ?>_icon.jpg' alt=''/>
			<div class="custom-button-orange custom-button">
				<a href="index.php?p=profile">Mon profil</a>
			</div>
		</div>
		<div id='small-profile-right'>
			<h2><?php echo ucfirst($_SESSION['user']->getProfile('nom'))." ".ucfirst($_SESSION['user']->getProfile('prénom')); ?></h2>
			<dl>
				<dt>Nom</dt><dd><?php echo ucfirst($_SESSION['user']->getProfile('nom')); ?></dd>
				<dt>Prénom</dt><dd><?php echo ucfirst($_SESSION['user']->getProfile('prénom')); ?></dd>
				<dt>Date de naissance</dt><dd><?php echo $_SESSION['user']->getProfile('date de naissance'); ?></dd>
				<dt>Médecin généraliste</dt><dd><?php echo $docProfile; ?></dd>
			</dl>
			<dl>
				<dt>Adresse</dt><dd><?php echo ucfirst($_SESSION['user']->getProfile('complément d\'adresse')); ?></dd>
				<dt>Ville</dt><dd><?php echo ucfirst($_SESSION['user']->getProfile('ville')); ?></dd>
				<dt>Code postal</dt><dd><?php echo $_SESSION['user']->getProfile('code postal'); ?></dd>
			</dl>
		</div>
	</div>
</div>