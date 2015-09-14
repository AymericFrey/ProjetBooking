<?php
defined("_nova_district_token_") or die('');

?>
<div id='calendar-selection'>
	<div id='close-btn'>X</div>
	<div class='clear'></div>
	
	<div class='selection-duration'>Durée d'un rendez-vous : <?php echo $doctor->getRdvDuration(); ?>mn</div>

	<div id='selection-hours'>
		
	</div>
	<p>
		Les cases blanches sont les créneaux disponibles. Cliquez dessus et le formulaire en bas de cette page sera mis à jour automatiquement. 
		Vous n'aurez plus qu'à confirmer votre choix en validant ce formulaire.
	</p>
</div>