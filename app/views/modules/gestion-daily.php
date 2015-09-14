<?php
defined("_nova_district_token_") or die('');


//affichage partie gauche
$days = "";
$nbr = 14;
$startDay = $day - 5 * 24 * 3600; //3 jours avant le start
for($i = 0; $i < $nbr; $i++){
	$w = Calendar::instance()->getDays()[intval((date('w', $startDay + ($i * 24 * 3600))))];
	$jour = date('d', $startDay + ($i * 24 * 3600));
	$mois = Calendar::instance()->getMonths()[date('n', $startDay + ($i * 24 * 3600)) - 1];
	
	$addClass = "";
	if($startDay + ($i * 24 * 3600)== $day)
		$addClass = " daily-day-selected";
	if($i+1 == $nbr)
		$addClass .= " daily-day-last";
		
	$days .= "<div class='daily-day".$addClass."'><a href='index.php?p=gestion&chosen-date=".date('Y-m-d', $startDay + ($i * 24 * 3600))."'>".$w." ".$jour." ".$mois."</a></div>";
}

?>
<script type='text/javascript'>
$(document).ready(function() {
	$(".daily-schedule-delete").click(function(){
		var url = $(this).attr("href");
		App.Dialog.add(
			"Etes-vous sûr de vouloir supprimer ce rendez-vous ?", 
			"<br />Vous supprimerez définitivement ce rendez-vous, le client en sera informé.", 
			new Array('oui', 'annuler'),
			function (result){ if(result) document.location.href = url; }
		);
		return false;
	});	
	
});
</script>

<div id="content">
	<?php include "views/gestion.php"; ?>
	
	<div class="content-block daily-search">
		<h2 class='h2-block-grey'>Choisissez la date des rendez-vous que vous souhaitez consulter</h2>
		
		<form action="index.php" method="get">
			<input type="hidden" name="p" value="gestion" />
			
			<label>Jour</label>
			<input type="date" name="chosen-date" value='<?php echo date('Y-m-d', $day); ?>' />
			
			<input class="custom-submit custom-submit-orange" type="submit" value="rechercher"/>
		</form>
	</div>
	
	
	<div class="content-block" id='daily-left'>
		<?php echo $days; ?>
	</div>
	<div class="content-block" id='daily-right'>
		<h2 class='h2-block-grey'>Liste des rendez-vous du <?php echo date('d-m-Y', $day); ?></h2>
		
		<div class='custom-button-orange custom-button'>
			<a href='pdf/pdf.php?date=<?php echo date("Y-m-d", $day); ?>' target='_blank'><img src='img/pdf_icon.png' alt='pdf bouton'/> Exporter PDF</a>
		</div>
		<div class='clear'></div>
		
		<?php
		if(isset($errors['daily-validate']))
			echo $errors['daily-validate']->getHTML()."<br />";
		if(isset($errors['daily-delete']) && $errors['daily-delete']->getMessage() != "")
			echo $errors['daily-delete']->getHTML()."<br />";
		
		foreach ($dailyScheduleList as $schedule){ ?>
			<div class='daily-schedule <?php if($schedule['validate'] != 1) echo "daily-waiting"; ?>'>
				<div class='daily-schedule-left'>
					<img src='img/<?php if(isset($schedule['sexe'])) echo $schedule['sexe']; else echo "homme"; ?>_icon.jpg' alt='' />
					<div>
						<div><strong><span><?php echo $schedule['nom']."</span> ".ucfirst($schedule['prénom']); ?></strong></div>
						<div><?php echo Tools::getAge($schedule['date de naissance'])['y']." (".$schedule['date de naissance'].")";?></div>
						<div>Adresse : <?php echo $schedule['complément d\'adresse']; ?></div>
						<div>Ville : <?php echo $schedule['ville']; ?> (<?php echo $schedule['code postal']; ?>)</div>
					</div>
				</div>
				<div class='daily-schedule-right'>
					<div class='daily-schedule-small'><p><?php echo date("H:i", $schedule['date_start']); ?></p></div>
					<div class='daily-schedule-small'>Durée : <?php echo ($schedule['date_stop'] - $schedule['date_start']) / 60; ?>mn</div>
					<div class='daily-schedule-big'>
						<?php 
						if($schedule['note'] != "")
							echo $schedule['note']; 
						else echo "Aucune note particulière sur ce rendez-vous";?>
					</div>
				</div>
				<div class='clear'></div>
				<div class='daily-schedule-links'>
					<?php if($schedule['validate'] != 1){ ?>
						<a href='index.php?p=gestion&chosen-date=<?php echo date("Y-m-d", $day); ?>&a=val&rdv=<?php echo $schedule['id_schedule']; ?>'>Valider</a>
					<?php } ?>
					<a class='daily-schedule-delete' href='index.php?p=gestion&chosen-date=<?php echo date("Y-m-d", $day); ?>&a=del&rdv=<?php echo $schedule['id_schedule']; ?>'>Supprimer</a>
				</div>
			</div>
		<?php 
		} ?>
		
		
	</div>
	<div class='clear'></div>
</div>



	

















