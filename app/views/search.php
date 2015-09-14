<?php
defined("_nova_district_token_") or die('');

?>

<div id="content">
	<?php include "views/modules/search.php"; ?>
	
	<div class="content-block">
		<h2 class='h2-block-orange'>prendre un rendez-vous</h2>

		<?php 
		if(is_array($docsearch) AND count($docsearch) > 0){
			foreach ($docsearch as $key => $value) {
		?>
				<div class="content-block search-profile">
					<div class='booking-left-profile'>
						<img src='img/user_icon.jpg' alt='' />
						<div>
							<h3><span><?php echo $value['nom']; ?></span></h3>
							<h3><?php echo ucfirst($value['prénom']); ?></h3>
							<div><?php echo ucfirst($value['medicine_name']); ?></div>
							<strong><?php echo $value['téléphone']; ?></strong>
						</div>
					</div>
					
					<div class='booking-left-info'>
						<h4>Adresse</h4>
						<div>
							<span>
								<?php echo ucfirst($value['complément d\'adresse']); ?>
							</span>
							<span>
								<?php echo $value['code postal']." ".ucfirst($value['ville']); ?>
							</span>
						</div>
					</div>
					<div class='clear'></div>
					<div class='custom-button-orange custom-button'><a href='index.php?p=booking&doctor=<?php echo $key; ?>'>Voir</a></div>
				</div>
		<?php }
		}
		else
			echo "<br /><p class='error'>Aucun résultat !</p><br />";?>
		
		<div class='clear'></div>
	</div>
</div>