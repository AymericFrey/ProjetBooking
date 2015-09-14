<?php 
defined("_nova_district_token_") or die('');

$listeOptionsMedicines = "<option value='0'>- Je ne suis pas praticien -</option>";
foreach($medicinesList as $medicine)
	$listeOptionsMedicines .= "<option value='".$medicine['id_medicine']."'>".ucfirst($medicine['medicine_name'])."</option>";
	
?>
<script type='text/javascript'>
$(document).ready(function() {
	$("#form-welcome").submit(App.Form.check);
});
</script>

<div id='content'>
	<div id='welcome'>
		<div >
			<h1 class='title-color title-color-blue'>Bienvenue sur Booking</h1>
		</div>
		<span>Merci de compléter ce formulaire pour accéder aux services</span>
		
		<p>
			Ce formulaire est obligatoire. Vos données ne seront communiquées qu'aux praticiens pour améliorer le service. 
			Ces données ne sont pas utilisées à des fins commerciales et elles ne sont pas communiquées à des tiers.
		</p>
		<form action = "index.php?p=welcome" method="post" id='form-welcome'>
			<?php
				if(isset($errors['profil']))
					echo $errors['profil']->getHTML();
			?>
		
			<div class='formLine'>
				<label>Votre nom </label><input type="text" name="name" value="" data-form-control="name" />
				<label>Votre sexe</label>
				<select name='sexe'>
					<option value='homme'>Homme</option>
					<option value='femme'>Femme</option>
				</select>
				
				<label>Date de naissance (jj-mm-aaaa)</label><input type="date" name="birthday" data-form-control="date" />
				<label>Votre adresse </label><input type="text" name="address" data-form-control="name" />
			</div>
			<div class='formLine'>
				<label>Votre prénom </label><input type="text" name="firstname" data-form-control="name" />
				<label>Je suis</label>
				<select name='pratician'>
					<?php echo $listeOptionsMedicines; ?>
				</select>
				
				<label>Votre numéro de téléphone</label><input type="text" name="phone" data-form-control="phone" />
				<label>Votre ville </label><input type="text" name="city" data-form-control="name" />
				<label>Votre code postal </label><input type="number" name="zip" data-form-control="zip" />
			</div>
			
			<input type="submit" class='custom-submit-blue custom-submit' name="subscriptionform" value="confirmer"/>
		</form>
	</div>


</div>