<?php
defined("_nova_district_token_") or die('');

// doit être défini $rdv
if(isset($rdv) AND isset($found))
{
	if(isset($rdv['medicine_name']) AND isset($rdv['date_start']) AND isset($rdv['nom']) AND isset($rdv['complément d\'adresse']) AND isset($rdv['prénom']) AND
	   isset($rdv['téléphone']) AND isset($rdv['code postal']) AND isset($rdv['ville']) AND isset($rdv['validate']))
	{
		$found = true;
		
		$validate = "";
		if($rdv['validate'] == 0)
			$validate = "profil-info-grey";
	
?>
		<div class='schedule-display'>
			<img src="img/medicines/<?php echo $rdv['medicine_name']; ?>.png" alt="icone-doc" />
			<div class="schedule-display-profil">
				<h3 class="profil-info profil-info-title-orange <?php echo $validate; ?>"><?php echo $rdv['medicine_name']; ?></h3>
				<div class="profil-info profil-info-right profil-info-small"><?php echo date("j-m-Y", $rdv['date_start']).' à '.date('G\hi', $rdv['date_start']); ?></div>
				
				<div class='profil-info-sub'>
					<div class="profil-info profil-info-bold"><a href='index.php?p=booking&doctor=<?php echo $rdv['id_doctor']; ?>'><span><?php echo $rdv['nom']."</span> ".ucfirst($rdv['prénom']); ?></a></div>
					<div class="profil-info profil-info-right profil-info-grey"><?php echo ucfirst($rdv['complément d\'adresse']); ?></div>
					
					<div class="profil-info"><?php echo $rdv['téléphone']; ?></div>
					<div class="profil-info profil-info-right profil-info-grey"><?php echo $rdv['code postal']." ".ucfirst($rdv['ville']); ?></div>
				</div>
				
				<div class="profil-info"></div>
				<div class="profil-info profil-info-right"><a class="cancel-rdv-by-user" data-dialog-info="<?php echo $rdv['téléphone']; ?>" href="index.php?p=home&doctor=<?php echo $rdv['id_doctor']; ?>&rdv=<?php echo $rdv['id_schedule']; ?>">Annuler ce rendez-vous</a></div>		
			</div>
		</div>
<?php
	}
}
?>