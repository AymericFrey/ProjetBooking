<?php
defined("_nova_district_token_") or die('');

?>
<script type='text/javascript'>
$(document).ready(function() {
	$("#form-profile").submit(App.Form.check);
});
</script>

<div id='content'>	
	<div class='booking-left'>
		<div class="content-block">
			<h2 class='h2-block-grey'>Profil</h2>
			
			<div class='booking-left-profile'>
				<img src='img/<?php echo $_SESSION['user']->getProfile('sexe'); ?>_icon.jpg' alt='' />
				<div>
					<h3><span><?php echo $_SESSION['user']->getProfile('nom'); ?></span></h3>
					<h3><?php echo ucfirst($_SESSION['user']->getProfile('prénom')); ?></h3>
					<strong><?php echo $_SESSION['user']->getProfile('téléphone'); ?></strong>
				</div>
			</div>
			
			<div class='booking-left-info'>
				<h4>Date de naissance</h4>
				<div>
					<span>
						<?php echo ucfirst($_SESSION['user']->getProfile('date de naissance')); ?>
					</span>
				</div>
				<h4>Adresse</h4>
				<div>
					<span>
						<?php echo ucfirst($_SESSION['user']->getProfile('complément d\'adresse')); ?>
					</span>
					<span>
						<?php echo $_SESSION['user']->getProfile('code postal')." ".ucfirst($_SESSION['user']->getProfile('ville')); ?>
					</span>
				</div>
			</div>
			<div class='clear'></div>
		</div>
	</div>
	
	<div class='booking-right profile-right'>
		<div class="content-block">
			<h2 class='h2-block-grey'>Modifier mes informations membre</h2>
			<form action="index.php?p=profile" method="post" id='form-profile'>
				<?php
					if(isset($errors['profil']))
						echo $errors['profil']->getHTML();
				?>
			
				<div>
					<label>Votre nom </label>
					<input type="text" name="name" value="<?php echo $_SESSION['user']->getProfile('nom'); ?>" data-form-control="name" />
					
					<label>Votre prénom </label>
					<input type="text" name="firstname" value="<?php echo $_SESSION['user']->getProfile('prénom'); ?>" data-form-control="name" />
					
					<label>Votre sexe</label>
					<select name='sexe'>
						<option value='homme' <?php if($_SESSION['user']->getProfile('sexe') == "homme") echo "selected=selected"; ?> >Homme</option>
						<option value='femme' <?php if($_SESSION['user']->getProfile('sexe') == "femme") echo "selected=selected"; ?> >Femme</option>
					</select>
					
					<label>Votre numéro de téléphone</label>
					<input type="telephone" name="phone" value="<?php echo $_SESSION['user']->getProfile('téléphone'); ?>" data-form-control="phone" />
					
					<label>Date de naissance (jj/mm/aaaa)</label>
					<input type="date" name="birthday" value="<?php echo $_SESSION['user']->getProfile('date de naissance'); ?>" data-form-control="date" />
					
					<label>Votre adresse </label>
					<input type="text" name="address" value="<?php echo $_SESSION['user']->getProfile('complément d\'adresse'); ?>" data-form-control="name" />
					
					<label>Votre code postal </label>
					<input type="number" name="zip" value="<?php echo $_SESSION['user']->getProfile('code postal'); ?>" data-form-control="zip" />
					
					<label>Votre ville </label>
					<input type="text" name="city" value="<?php echo $_SESSION['user']->getProfile('ville'); ?>" data-form-control="name" />
				</div>
				
				<input type="submit" class='custom-submit-blue custom-submit' name="subscriptionform" value="confirmer"/>
			</form>
		</div>
	</div>
	
	<div class='clear'></div>
	
</div> <!-- content -->