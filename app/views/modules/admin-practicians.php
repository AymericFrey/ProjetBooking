<?php
defined("_nova_district_token_") or die('');
?>

<script type='text/javascript'>
$(document).ready(function()
{
	$(".send_message_to_user").click(function(){
		var url = $(this).attr("href");
		var idRecup = $(this).attr("data-dialog-info"); 
		App.Dialog.add(
			"MESSAGE A L'ATTENTION D'UN UTILISATEUR DU SITE", 
			'<form action="index.php?p=admin&m=practicians&msg_id='+idRecup+ '" method="post">'+
				'<label for="message_to_user">Votre message </label>'+
				'<textarea rows="5" cols="20" name="message_to_user" id="message_to_user"></textarea>'+
			'</form>', 
			new Array('oui', 'non'),
			function (result) {
				if(result) {
					$(".dialog-content form").submit(); 
				}
			}
		);
		
		return false;
	});	

	//changement de statut
	$(".change_status").click(function(){
		var url = $(this).attr("href");
		var idRecup = $(this).attr("data-dialog-info"); 
		var idRecup2 = $(this).attr("data-dialog-id"); 
		App.Dialog.add(
			"CHANGEMENT DE STATUT D'UN UTILISATEUR", 
			'<div>Vers quel statut souhaitez-vous effectuer le changement?</div>'+
			'<form action="index.php?p=admin&m=practicians&id='+idRecup+'&idmed='+idRecup2+'" method="post" id="changePratician">'+
				'<input type="radio" name="status" value="member" id="member"/><label for="member">patient classique</label><br />'+
				'<input type="radio" name="status" value="doctor" id="doctor"/><label for="doctor">médecin</label>'+
			'</form>', 
			new Array('ok', 'annuler'),
			function (result) {
				if(result) {
					$(".dialog-content form").submit(); 
				}
			}
		);
		
		return false;
	});	
	
});
</script>

<?php
$doctorList = "";
if (isset($doctor_result) AND is_array($doctor_result) AND count($doctor_result) >=1) 
{
	$adminGestionList = $doctor_result;
	$doctorList = include("views/modules/admin-search-result.php");
}

$futurDoctorList = "";
if (isset($future_doctor_result) AND is_array($future_doctor_result) AND count($future_doctor_result) >= 1 ) 
{
	$adminGestionList = $future_doctor_result;
	$futurDoctorList = include("views/modules/admin-search-result.php");
}
?>


<div id='content'>
	<?php include('views/admin.php'); ?>
	
<div class="content-block daily-search">
	<h2 class='h2-block-grey'>Trouver un médecin</h2>
	<div id="recherche-doctor">
		<form action ="index.php?p=admin&m=practicians" method="post" id='recherche-name'>
			<input type="text" name="search-doctorname" value="" data-form-control="name" />
			
			<input class="custom-submit custom-submit-orange" type="submit" name="searchdoctor" value="rechercher"/>
		</form>

	</div>
</div>

	<div class="content-block">
		<h2 class='h2-block-orange'>Résultat de la recherche</h2>
		<div id="recherche-future-doctors">
			<?php
				if(isset($errors["admin_doctors"]))
					echo $errors["admin_doctors"]->getHTML()."<br />";
				if(isset($errors["msg-admin-members"]))
					echo $errors["msg-admin-members"] ->getHTML()."<br />";
				
				if(isset($errors["status_change"]))
					echo $errors["status_change"] ->getHTML()."<br />";
					
				echo $doctorList;
			?>
		</div>
	</div>

<div class="content-block">
	<h2 class='h2-block-blue'>Praticiens en attente de confirmation de leur statut</h2>
	<div id="recherche-future-doctors">
		<?php 
			if(isset($errors["admin_future_doctors"]))
				echo $errors["admin_future_doctors"] ->getHTML()."<br />";	
			
			echo $futurDoctorList; 
		?>
	</div>
</div>

	
	