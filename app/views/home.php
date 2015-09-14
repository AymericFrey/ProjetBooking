<?php
defined("_nova_district_token_") or die('');


include "views/modules/small-profile.php"; 
?>
<script type='text/javascript'>
$(document).ready(function() {
	$(".cancel-rdv-by-user").click(function(){
		var url = $(this).attr("href");
		var tel = $(this).attr('data-dialog-info');
		App.Dialog.add(
			"Etes-vous sûr de vouloir supprimer ce rendez-vous ?", 
			"Vous supprimerez définitivement ce rendez-vous, le médecin en sera informé.<br /><br />"+
			"<strong>Attention !</strong> <br />Si le rendez-vous intervient dans moins de <?php echo SchedulesManager::instance()->getMaxTimeToCancel(); ?>h vous ne pourrez pas le supprimer. "+
			"<br />Il faudra contacter votre praticien à ce numéro de téléphone : <strong>" + tel + "</strong>", 
			new Array('oui', 'non'),
			function (result){ if(result) document.location.href = url; }
		);
		return false;
	});	
	
});
</script>

<div id="content">
	<?php include "views/modules/search.php"; ?>

	<div class="content-block left-block">
		<h2 class="h2-block-orange">mes rendez-vous confirmés</h2>
		<?php 
		$found = false;
		foreach($futursRdv as $rdv) {
			if($rdv['validate'] == 1)
				include "views/modules/schedule-display.php";
		} 
		if(!$found) 
			echo "<div class='schedule-display'><p>Vous n'avez pas à ce jour, de rendez-vous confirmés</p></div>"; ?>
	</div>
	
	<div class="content-block right-block">
		<h2 class="h2-block-blue">mes rendez-vous en attente</h2>
		<?php 
		$found = false;
		foreach($futursRdv as $rdv) {
			if($rdv['validate'] == 0)
				include "views/modules/schedule-display.php";
		} 
		if(!$found) 
			echo "<div class='schedule-display'><p>Vous n'avez pas à ce jour, de rendez-vous en attente</p></div>"; ?>
	</div>
	<div class='clear'></div>
	
	<div class="content-block right-block">
		<h2 class="h2-block-darkgrey">mes médecins</h2>
		
		<div id='small-profiles'>
			<?php
			$i = 0;
			foreach($favorites as $key => $fav)
			{ 
				$i++;
			?>
				<div class="small-profile">
					<a href='index.php?p=booking&doctor=<?php echo $key; ?>'><img src="img/<?php echo $fav['sexe']; ?>_icon.jpg" alt=""/></a>
					<div class="small-profile-name">
						<a href='index.php?p=booking&doctor=<?php echo $key; ?>'>
							<span><?php echo $fav['nom']; ?></span> <?php echo ucfirst($fav['prénom']); ?>
						</a>
					</div>
					<div class="small-profile-medicine"><?php echo ucfirst($fav['medicine_name']); ?></div>
				</div>
			<?php 
			} 
			if($i == 0)
				echo "<div class='schedule-display'><p>Vous n'avez ajouté aucun praticien à vos favoris</p></div>";
			?>
			<div class='clear'></div>
			<div class='custom-button-orange custom-button'><a href='index.php?p=favorite'>gérer</a></div>
		</div>
		
	</div>
	<div class='clear'></div>
</div>
<div class="clear"></div>