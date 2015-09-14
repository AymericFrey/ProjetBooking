<?php
defined("_nova_district_token_") or die('');

?>
<script type='text/javascript'>
$(document).ready(function() {
	App.Booking.Calendar.Display.registerUI("calendar-selection");
	App.Booking.Calendar.Action.init();
	App.Booking.Calendar.Action.registerForm({date: "date-rdv", time: "time-rdv"});
	App.Booking.Calendar.Data.setDoctorId(<?php if(isset($_GET['doctor'])) echo $_GET['doctor']; else echo 0; ?>);
	
	$("#form-rdv").submit(App.Form.check);
	
	//1 = success, 2 = fail, 0 = nothing
	var addRdv = <?php if(isset($errors['rdv']) AND $errors['rdv']->getType() == "info") echo 1; else echo 0; ?>;
	
	if(addRdv != 0){		
		App.Dialog.add(
			"Le rendez-vous a été pris", 
			"Le rendez-vous a bien été pris auprès du praticien.<br /> Vous serez informé par un message lorsque celui-ci l'aura confirmé.", 
			new Array('ok'),
			function (result){ }
		);
	}
});
</script>

<div id='content'>

	<div class='booking-left'>
		<div class="content-block">
			<h2 class='h2-block-grey'>Profil</h2>
			
			<div class='booking-left-profile'>
				<img src='img/<?php echo $doctor->getProfile("sexe"); ?>_icon.jpg' alt='' />
				<div>
					<h3><span><?php echo $doctor->getProfile('nom'); ?></span></h3>
					<h3><?php echo ucfirst($doctor->getProfile('prénom')); ?></h3>
					<div><?php echo $doctor->getMedicineName(); ?></div>
					<strong><?php echo $doctor->getProfile('téléphone'); ?></strong>
				</div>
			</div>
			
			<div class='booking-left-info'>
				<h4>Adresse</h4>
				<div>
					<span>
						<?php echo ucfirst($doctor->getProfile('complément d\'adresse')); ?>
					</span>
					<span>
						<?php echo $doctor->getProfile('code postal')." ".ucfirst($doctor->getProfile('ville')); ?>
					</span>
				</div>
			</div>
			
			<?php
			if($_SESSION['user']->getIdMember() != $doctor->getIdMember()) { ?>
				<form action='index.php?p=booking&doctor=<?php echo $doctor->getIdMember(); ?>' method='post'>
					<input type="submit" class='custom-submit-orange custom-submit' name="<?php echo $favoriteButtonAction; ?>-to-favorite" value="<?php echo $titleButtonAction; ?>"/>
				</form>
			<?php } ?>
			<div class='clear'></div>
			<?php
			if(isset($errors['add-to-favorite']))
				echo $errors['add-to-favorite']->getHTML()."<br />";
			?>
		</div>
	</div>
	
	<div class='booking-right'>
		<div class="content-block">
			<h2 class='h2-block-grey'>Informations diverses</h2>
			<p>
				<?php 
					if($doctor->getInfoPro() == "")
						echo "Aucune information.";
					else
						echo Tools::secure($doctor->getInfoPro());
				?>
			</p>
			<!--div class='special-button'>
				<img src='img/look_icon.png' alt='image bouton'/>
				<p>Trouver les premiers rendez-vous disponibles</p>
			</div-->
			<div class='clear'></div>
		</div>
	</div>
	
	<div class='clear'></div>
	
	<div class="content-block" id='booking-calendar'>
		<h2 class='h2-block-blue'>Emploi du temps du praticien</h2>
		
		<div class="recherche-a">
			<form action="index.php?p=booking&doctor=<?php echo $doctor->getIdMember(); ?>" method="get">
				<input type="hidden" name="p" value="booking" />
				<input type="hidden" name="doctor" value="<?php echo $doctor->getIdMember(); ?>" />
				
				<label for='start-date'>Afficher à partir de cette date</label>
				<input type="date" name="start" value="<?php echo $dateStart->format("Y-m-d"); ?>" id='start-date'/>
				
				<input class="custom-submit custom-submit-orange" type="submit" value="afficher"/>
			</form>
		</div>
		
		<div class="recherche-b">
			<form action ="index.php?p=search" method="post">
				<div>
					<label>Créneaux disponibles tels jours (Service bientôt disponible)</label>
					<input type="checkbox" name="department" id='Lundi' disabled />
					<label for="Lundi" class='label-inline'>Lundi</label>
					<input type="checkbox" name="department" id='Mardi' disabled />
					<label for="Mardi" class='label-inline'>Mardi</label>
					<input type="checkbox" name="department" id='Mercredi' disabled  />
					<label for="Mercredi" class='label-inline'>Mercredi</label>
					<input type="checkbox" name="department" id='Jeudi' disabled />
					<label for="Jeudi"class='label-inline'>Jeudi</label>
					<input type="checkbox" name="department" id='Vendredi'disabled  />
					<label for="Vendredi" class='label-inline'>Vendredi</label>
					<input type="checkbox" name="department" id='Samedi' disabled  />
					<label for="Samedi" class='label-inline'>Samedi</label>
				</div>
				
				<div class='float-block'>
					<label for='hour-start'>Entre</label>
					<input type="time" name="hour-start" value="" id='hour-start' disabled />
				</div>
				<div class='float-block'>
					<label for='hour-end'>Et</label>
					<input type="time" name="hour-end" value="" id='hour-end' disabled />
				</div>
				
				<input class="custom-submit custom-submit-orange" type="submit" name="searchsp" value="rechercher" disabled />
				<div class='clear'></div>
			</form>
		</div>	
		<div class='clear'></div>
		
		<?php 
		if(isset($doctor))
			include_once "views/modules/booking-selection.php";
		
		echo $calendrier;
		?>
	</div>
	
	<?php if($_SESSION['user']->getIdMember() != $_GET['doctor']){ ?>
	<div class="content-block" id='rdv-form'>
		<h2 class='h2-block-orange'>Choix du rendez-vous</h2>
		
		<p>
			<!-- Quelques remarques concernant la prise du rendez-vous (délais de changement, d’annulation etc..). Et des rappels que si la personne ne vient pas elle devra payer quand même son rendez-vous... -->
		</p>
		
		<h3>Prendre un rendez-vous avec <span><?php echo $doctor->getProfile('nom')." ".$doctor->getProfile('prénom'); ?></span></h3>
		<form action='index.php?p=booking&doctor=<?php echo $doctor->getIdMember(); ?>' method='post' id='form-rdv'>
			<input type="hidden" name="tk" value="<?php echo $_SESSION['token']; ?>" />
			
			<?php
			if(isset($errors['rdv']))
				echo $errors['rdv']->getHTML();
			?>
			
			<p>La durée du rendez-vous est de : <strong><?php echo $doctor->getRdvDuration(); ?>mn</strong></p>
			<div id='rdv-left'>
				<label for='date-rdv'>Le</label>
				<input type="date" name="date-rdv" value="" id='date-rdv' data-form-control='date' />
				
				<label for='time-rdv'>à</label>
				<input type="time" name="time-rdv" value="" id='time-rdv'  data-form-control='time'/>
			</div>
			
			<div id='rdv-right'>
				<label>Résumez la raison de votre rendez-vous pour améliorer votre prise en charge (optionnel)</label>
				<textarea name='note'></textarea>
			</div>
			
			<div class='clear'></div>
			<div class='rdv-note'>
				<input type="checkbox" class='label-inline' name="valid-rdv" id='valid-rdv'  data-form-control='box' />
				<label for="valid-rdv" >
					En cochant cette case j’accepte les CGU du site booking ainsi que les conditions particulières du praticien auprès duquel je prend mon rendez-vous. <br />
					Je suis conscient que tout abus est punissable par la loi et que mes informations seront communiquées aux autorités si besoin.<br />
					Mon IP : <span><?php echo $_SERVER['REMOTE_ADDR']; ?></span>
				</label>
			</div>
			
			<input class="custom-submit custom-submit-orange" type="submit" value="Prendre ce rdv"/>
		</form>
	</div>
	<?php } ?>
</div>
	