<?php
defined("_nova_district_token_") or die('');

//RETIRER UN PRATICIEN DES FAVORIS
if(isset($_POST['remove-to-favorite'])){
	if(isset($_GET['doctor']) AND is_numeric($_GET['doctor'])){
		$idDoc = intval($_GET['doctor']);
		
		$errors['favorite-remove'] = UsersManager::instance()->removeDoctorFromFavorites($_SESSION['user']->getIdMember(), $idDoc);
	}
}

//DEFINIR UN MEDECIN TRAITANT
if(isset($_POST['set-generalist'])){
	if(isset($_GET['doctor']) AND is_numeric($_GET['doctor'])){
		$idDoc = intval($_GET['doctor']);
		
		$result = UsersManager::instance()->setGeneralist($_SESSION['user']->getIdMember(), $idDoc);
		if($result === true){
			$errors['favorite-generalist'] = new Error("Vous venez de mettre à jour votre médecin généraliste", 'info');
			$_SESSION['user']->setIdGeneralist($idDoc);
		}
		else
			$errors['favorite-generalist'] = new Error("Ce médecin n'est pas un médecin généraliste");
	}
}

// $favorites= FavoritesManager::instance()->getFavoritesDoctor($_SESSION['user']->getIdMember());
$favorites = UsersManager::instance()->getFavoritesDoc($_SESSION['user']->getIdMember());

//inclusion du sous-controleur qui se chargera d'inclure la vue correspondante !
include(dirname(__FILE__).'/../views/favorite.php');

?>