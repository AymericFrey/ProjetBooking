<?php
defined("_nova_district_token_") or die('');
?>
<div id="content">
	<?php include "views/gestion.php"; ?>
	
	<div class="content-block">
		<h2 class='h2-block-darkgrey'>Paramètrage de vos horaires</h2>
		<!-- Formulaire   Gestions Horaires - Heure début / Heure fin -->
			<form action="index.php?p=gestion&m=config" method='post' class='form-padding'>
				<div class='formLine'>
					<label for='heuredebut'>Heure de début des rendez-vous</label>
					<input type='time' name='hdebut' step="300" value='<?php echo Tools::formatTime($_SESSION['user']->getStartHour("h")).":".Tools::formatTime($_SESSION['user']->getStartHour("m")); ?>' />
				</div>
				<div class='formLine'>
					<label for='heurefin'>Heure de fin des rendez-vous</label>
					<input type='time' name='hfin' step="300" value='<?php echo Tools::formatTime($_SESSION['user']->getEndHour("h")).":".Tools::formatTime($_SESSION['user']->getEndHour("m")); ?>' />
				</div>
				<div class='formLine'>
					<label for='dur'>Durée d'un Rendez-vous (min)</label>
					<input type='number' step='5' name='dureerdv' value='<?php echo intval($_SESSION['user']->getRdvDuration()); ?>'/>
				</div>
				
				<div class='formBlock'>
					<input type="checkbox" name="confirmrdv" <?php if($_SESSION['user']->getRdvConfirm() == 0) {echo '';} else {echo 'checked="checked"';}?>> Demander la confirmation d'un rendez-vous	
				</div>
				<input type='submit'  name='form-horaires' value="Valider" class='custom-submit custom-submit-blue' />
			</form>

		<?php 
		//affichage de l'erreur ou de l'info
		if(isset($errors['gestion-edit']))
			echo $errors['gestion-edit']->getHTML();
		?>
	</div>	
		
	<div class="content-block">
		<h2 class='h2-block-darkgrey'>Edition des informations publiques</h2>
		
		<form action="index.php?p=gestion&m=config" method='post' class='form-padding'>
			<div class='formBlock'>
				<label> Informations professionnelles </label>
				<textarea name="infopro" rows="15"><?php echo Tools::secureHTML($_SESSION['user']->getInfoPro()); ?></textarea>
			</div>
			
			<input type='submit' name='form-infopro' value="Mettre à jour" class='custom-submit custom-submit-blue' />
		</form>
	</div>
</div>
