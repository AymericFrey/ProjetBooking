<?php
defined("_nova_district_token_") or die('');
?>
<div id="content">
	<?php
		if(isset($errors['favorite-remove']))
			echo $errors['favorite-remove']->getHTML()."<br />";
		else if(isset($errors['favorite-generalist']))
			echo $errors['favorite-generalist']->getHTML()."<br />";
			
			
		foreach($favorites as $key => $fav)
		{ 
			$generalistSubmit = "";
			if($fav['medicine_name'] == "médecin généraliste" && $_SESSION['user']->getIdGeneralist() != $key){
				$generalistSubmit = "<form action='index.php?p=favorite&doctor=$key' method='post'>
					<input type='submit' class='custom-submit-blue custom-submit submit-generalist' name='set-generalist' value='Médecin traitant' />
				</form>";
			}
			else if($_SESSION['user']->getIdGeneralist() == $key)
				$generalistSubmit = "<div class='submit-generalist'><img src='../img/ticked.png' alt='' title='Médecin sélectionné'/><span>Médecin traitant</span></div>";
		?>
			<div class="content-block favorite-block">
				
				<div class='booking-left-profile'>
					<img src='img/user_icon.jpg' alt='' />
					<div>
						<h3><span><?php echo $fav['nom']; ?></span></h3>
						<h3><?php echo ucfirst($fav['prénom']); ?></h3>
						<div><?php echo $fav['medicine_name']; ?></div>
						<strong><?php echo $fav['téléphone']; ?></strong>
					</div>
				</div>
				
				<div class='booking-left-info'>
					<h4>Adresse</h4>
					<div>
						<span>
							<?php echo ucfirst($fav['complément d\'adresse']); ?>
						</span>
						<span>
							<?php echo $fav['code postal']." ".ucfirst($fav['ville']); ?>
						</span>
					</div>
				</div>
				
				
				<form action='index.php?p=favorite&doctor=<?php echo $key; ?>' method='post'>
					<input type="submit" class='custom-submit-orange custom-submit' name="remove-to-favorite" value="Retirer des favoris"/>
				</form>
				
				<?php echo $generalistSubmit; ?>
				
				<div class='clear'></div>
			</div>		
		<?php
		}
	?>
	<div class='clear'></div>
</div>
