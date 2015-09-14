<?php
defined("_nova_district_token_") or die('');
?>
<div id="content">
	<div id="alert-menu">
		<ul class="menu_horizontal">
    		<li><a href="index.php?p=alert&a=deleteall"> SUPPRIMER TOUTES LES ALERTES</a></li>
    	</ul>
	</div>

	<?php
		foreach ($alerts as $alert) 
		{
			$state="alert-info-title";

			if(!$alert->getState())
				$state.="-lu";
		?>

		<div id="alertinfo">
			<!-- Changement de l'id du DIV en fonction du statut du message -->
			<div id="<?php echo $state; ?>">
				<h2>	<?php echo $alert->getTitle(); ?>	</h2>
			</div>
	
			<div id="alert-info-text">
				<?php 	echo $alert->getmessage();	?>
			</div>

			<div id="alert-info-footer">
				<div id="alert-info-date">
					<?php 	echo '<p><small>'.$alert->getDateAlert().'</small></p>'; ?>
				</div>
				<div id="alert-info-sup">
					<a href="index.php?p=alert&a=del&id=<?php echo $alert->getIdAlert();?>">SUPPRIMER</a>
				</div>
			</div>
		</div>
		<?php
		}
	?>
</div>
