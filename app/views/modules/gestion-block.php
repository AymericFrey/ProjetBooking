<?php
defined("_nova_district_token_") or die('');
?>
<script type='text/javascript'>
$(document).ready(function() {
	var nbrWeeks = $('#number-weeks');
	var checkBox = $('#every-week');
	
	checkBox.change(function(){
		if(checkBox.is(':checked'))
			nbrWeeks.slideDown();
		else
			nbrWeeks.slideUp();
	});
	
});
</script>

<div id="content">
	<?php include "views/gestion.php"; ?>
	
	<div class="content-block">
		<h2 class='h2-block-orange'>Blocage de créneaux horaires</h2>
		<!-- Affichage des messages d'informations ou d'erreurs -->
		<?php 
		if(isset($errors['blocage-horaires']))
			echo $errors['blocage-horaires']->getHTML();
		?>

		<form action="index.php?p=gestion&m=block" method='post' class='form-padding'>
			<div>
				<input type="checkbox" name="everyweek" id='every-week' /> Récursivité tous les 7 jours	
			</div>
		
			<div class='formInline' id='number-weeks'>
				<label for='duree'>Nombre de semaines </label>
				<input type='number' max="52" name='numberweeks' />
			</div>
			
			<div class='formLine'>
				<label>Date de début (jj/mm/aaaa)</label>
				<input type="date" name="ddebut"/>
				<label>Date de fin (jj/mm/aaaa)</label>
				<input type="date" name="dfin"/>
			</div>
			<div class='formLine'>
				<label for='heuredebutb'>Heure de début</label>
				<input type='time' name='hdebut' step="300"/>
				
				<label for='heurefin'>Heure de fin</label>
				<input type='time' name='hfin' step="300" />
			</div>
			<div class='clear'></div>

			<label>Note personnelle sur ce bloquage</label>
			<textarea name="infos"></textarea>
			
			<input type='submit'  name='form-block' value="Valider" class='custom-submit custom-submit-blue' />
		</form>
	</div>
	
	
	<div class="content-block">
		<h2 class='h2-block-orange'>Liste des créneaux uniques bloqués</h2>
		
		<?php
		if(isset($blocks)){
			foreach($blocks as $block){
			?>
				<div class='block-listing'>
					<div class='block-listing-item'><?php echo "Du ".date("d/m/Y à H:i", $block->getDateStart()); ?></div>
					<div class='block-listing-item'><?php echo "Au ".date("d/m/Y à H:i", $block->getDateStop()); ?></div>
					<a href="index.php?p=gestion&m=block&del=delete&id=<?php echo $block->getIdSchedule();?>">supprimer</a>
					<div class='clear'></div>
					
					<?php if($block->getNote() != "") echo "<p>".$block->getNote()."</p>"; ?>
				</div>
			<?php
			}
		}?>
	</div>

	<div class="content-block">
		<h2 class='h2-block-orange'>Liste des créneaux bloqués récursivement</h2>
		<!-- Listing des blocages récursifs -->

		<?php
		if(isset($recursiveblocks)){
			foreach($recursiveblocks as $recursiveblock){?>
				<div class='block-listing'>
					<div class='block-listing-item'><?php echo "Du ".date("d/m/Y à H:i", $recursiveblock->getDateStart()); ?></div>
					<div class='block-listing-item'><?php echo "Au ".date("d/m/Y à H:i", $recursiveblock->getDateStop()); ?></div>
					<a href="index.php?p=gestion&m=block&del=delete&id=<?php echo $recursiveblock->getIdSchedule();?>">supprimer</a>
					<div class='clear'></div>
					
					<?php if($block->getNote() != "") echo "<p>".$recursiveblock->getNote()."</p>"; ?>
				</div>
			<?php
			}
		}?>
	</div>
</div>