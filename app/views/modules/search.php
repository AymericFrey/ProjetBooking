<?php
defined("_nova_district_token_") or die('');

$medCat = DoctorsManager::instance()->getMedicines();

$categories = "";
foreach($medCat as $cat){
	if(isset($_POST['rechsp']) AND $_POST['rechsp'] == $cat['id_medicine'])
		$categories.='<option value="'.$cat['id_medicine'].'" selected="selected" >'.$cat['medicine_name'].'</option>';	
	else
		$categories.='<option value="'.$cat['id_medicine'].'">'.$cat['medicine_name'].'</option>';	
}

$departmentSelected = "";
if(isset($_POST['department']) AND $_POST['department'] == "on")
	$departmentSelected = "checked";
	
$nameSearch = "";
if(isset($_POST['searchname']) AND $_POST['searchname'] != "")
	$nameSearch = Tools::secure($_POST['searchname']);

?>
<script type='text/javascript'>
$(document).ready(function() {
	$("#recherche-name").submit(App.Form.check);
	$("#recherche-speciality").submit(App.Form.check);
});
</script>

<div class="content-block" id='recherche'>
	<h2 class='h2-block-grey'>Trouver un médecin pour prendre un rendez-vous</h2>
	<div id="recherche-doc">
		<form action ="index.php?p=search" method="post" id='recherche-name'>
			<label for='searchname'>Par médecin</label>
			<input type="text" name="searchname" value="<?php echo $nameSearch; ?>" data-form-control="name" />
			
			<input class="custom-submit custom-submit-orange" type="submit" name="searchdoc" value="rechercher"/>
		</form>
	</div>
	
	<div id="recherche-sp">
		<form action ="index.php?p=search" method="post" id='recherche-speciality'>
			<label for='rechsp'>Par spécialité</label>
			<select name="rechsp">
				<?php echo $categories; ?>
			</select>
			
			<input type="checkbox" name="department" id='department' <?php echo $departmentSelected;?> />
			<label for="department"class='label-inline'>Dans mon département uniquement</label>
			
			<input class="custom-submit custom-submit-orange" type="submit" name="searchsp" value="rechercher"/>
		</form>
	</div>	
</div>