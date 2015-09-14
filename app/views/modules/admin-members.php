<?php
defined("_nova_district_token_") or die('');
?>

<script type='text/javascript'>

$(document).ready(function()
{
	$(".send_message_to_user").click(function(){
		var url = $(this).attr("href");
		var idRecup = $(this).attr("data-dialog-info"); 
		console.log(idRecup); 
		App.Dialog.add(
			"MESSAGE A L'ATTENTION D'UN UTILISATEUR DU SITE", 
			'<form action="index.php?p=admin&m=members&msg_id='+idRecup+ '" method="post">'+
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

	$(".delete_user").click(function(){
		var url = $(this).attr("href");
		var idRecup = $(this).attr("data-dialog-info"); 
		console.log(idRecup); 
		App.Dialog.add(
			"SUPPRESSION DEFINITIVE DE L'UTILISATEUR", 
			'<p>'+
				'Etes-vous sûr de vouloir supprimer définitivement cet utilisateur?<br />'+ 
				'Si vous cliquez sur oui, il ne sera plus possible de récupérer les informations effacées.'+
			'</p>',
			new Array('oui', 'non'),
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
$memberList = "";
if (isset($member_result))
{
	$memberList = '<table class="liste-search">
					<tr>
						<th>Nom</th>
						<th>Prénom</th>
						<th>Ville</th>
						<th>Code Postal</th>
						<th>Complément d\'adresse</th>
						<th>Adresse mail</th>
						<th>envoyer un message</th>
						<th>Supprimer le membre</th>
					</tr>';
	if (Tools::getClass($member_result) != "Error" AND is_array($member_result))
	{
		foreach ($member_result as $member)
		{
			$memberList.='<tr>
							<td>'.$member->getProfile('nom').'</td>
							<td>'.$member->getProfile('prénom').'</td>
							<td>'.$member->getProfile('ville').'</td>
							<td>'.$member->getProfile('code postal').'</td>
							<td>'.$member->getProfile('complément d\'adresse').'</td>
							<td>'.$member->getEmail().'</td>
							<td><a class="send_message_to_user" href="index.php?p=admin&m=members&msg_id='.$member->getIdMember().'" data-dialog-info="'.$member->getIdMember().'">envoyer un message</a></td>
							<td><a class="delete_user" href="index.php?p=admin&m=members&del_id="'.$member->getIdMember().'" data-dialog-info="'.$member->getIdMember().'">annuler</a></td>
						</tr>';
		}
	}
	$memberList .= "</table>";
}
?>

<div id='content'>
	<?php include('views/admin.php'); ?>

	<div class="content-block daily-search">
		<h2 class='h2-block-grey'>Trouver un membre</h2>
		<div id="recherche-user">
			<form action ="index.php?p=admin&m=members" method="post" id='recherche-name'>
				<input type="text" name="search-username" value="" data-form-control="name" />
				
				<input class="custom-submit custom-submit-orange" type="submit" name="searchuser" value="rechercher"/>
			</form>
		</div>
	</div>

	<div class="content-block" id='recherche'>
		<h2 class='h2-block-grey'>Résultat de la recherche</h2>

			<?php
				if(isset($errors["msg-admin-members"]))
					echo $errors["msg-admin-members"] ->getHTML()."<br />";
					
				if(isset($errors["delete_member"]))
					echo $errors["delete_member"] ->getHTML()."<br />";
			
				if(isset($errors["admin-members"]))
					echo $errors["admin-members"]->getHTML()."<br />";
				
				if($memberList != "")
					echo $memberList;
			?>
	</div>			
		
</div>