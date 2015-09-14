<?php
defined("_nova_district_token_") or die('');


?>
<script type='text/javascript'>
$(document).ready(function() {
	$("#form-inscription").submit(App.Form.check);
});
</script>

<div id='inscription'>
	<div class='main-block'>
		<h3>Inscription gratuite</h3>
		<div class='main-block-content'>
			<?php
			if(isset($errors['inscription']))
				echo $errors['inscription']->getHTML();
			?>
			<form action='' method='post' id='form-inscription'>
				<label for='email'>Entrez votre email</label>
				<input type='email' name='email' value='' data-form-control='email' />
				
				<label for='pass'>Entrez un mot de passe (5 caractères minimum)</label>
				<input type='password' name='pass' value='' />
				
				<label for='pass2'>Confirmez le mot de passe</label>
				<input type='password' name='pass2' value='' />
				
				<input type='submit' class='custom-submit custom-submit-blue' name='inscription' value="m'inscrire" />
			</form>
		</div>
	</div>
</div>