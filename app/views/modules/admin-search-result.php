<?php
defined("_nova_district_token_") or die('');

$resultAdminList = "";
if(isset($adminGestionList) AND is_array($adminGestionList) AND count($adminGestionList) > 0 AND Tools::getClass($adminGestionList) != "Error") {
	$resultAdminList = '<table class="liste-search">
					<tr>
						<th>Nom</th>
						<th>Prénom</th>
						<th>Spécialité</th>
						<th>Ville</th>
						<th>Code Postal</th>
						<th>Complément d\'adresse</th>
						<th>envoyer un message</th>
						<th>Changement de statut</th>
					</tr>';
		
	foreach ($adminGestionList as $doctor => $value)
	{
		$resultAdminList .= '<tr>
								<td>'.$value['nom'].'</td>
								<td>'.$value['prénom'].'</td>
								<td>'.$value['medicine_name'].'</td>
								<td>'.$value['ville'].'</td>
								<td>'.$value['code postal'].'</td>
								<td>'.$value['complément d\'adresse'].'</td>
								<td><a class="send_message_to_user" href="index.php?p=admin&m=practicians&msg_id='.$doctor.'" data-dialog-info="'.$doctor.'">envoyer un message</a></td>
								<td><a class="change_status" href="index.php?p=admin&m=practicians&id='.$doctor.'&idmed='.$value['id_medicine'].'" data-dialog-info="'.$doctor.'" data-dialog-id="'.$value['id_medicine'].'">changer son statut</a></td>
							</tr>';
	}
	$resultAdminList.='</table>';
	
	return $resultAdminList;
}
?>