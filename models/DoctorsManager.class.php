<?php
/*
* DoctorsManager - Singleton
* Fonctions spécifiques aux docteurs
*/
defined("_nova_district_token_") or die('');

/**
* @class	DoctorsManager	
* @brief	Singleton qui gère les praticiens
*/
final class DoctorsManager extends Manager
{
	/** 
	* @brief	Instance unique de DoctorsManager
	* @var		$instance
	*/
	private static $instance = null;
	
	/** 
	* @brief	Retourne l'instance de la classe et permet d'instancier un DoctorsManager si c'est le premier appel.
	* @return	DoctorsManager		Retourne l'instance de la classe DoctorsManager
	*/
	public static function instance()
	{
		if(self::$instance == null)
			self::$instance = new DoctorsManager();
		return self::$instance;
	}
	
	
	/** 
	* @brief	Non implémenté
	* @see		AdminManager::changeStatus()
	*/
	public function add($doctor)
	{
		
	}
	
	/** 
	* @brief	Récupère un praticien grâce à son ID
	* @param	int			$idDoctor	ID du praticien
	* @retval	Doctor		Le praticien trouvé
	* @retval	Error		Si le praticien n'existe pas, une erreur est retournée
	*/
	public function getDoctor($idDoctor)
	{
		$req = $this->bdd->prepare('SELECT medicine.medicine_name, doctor.*, member.* FROM member 
		LEFT JOIN doctor ON doctor.id_member = member.id_member
		LEFT JOIN medicine ON medicine.id_medicine = doctor.id_medicine
		WHERE member.id_member = :id_member');
		$req->bindParam(':id_member', $idDoctor, PDO::PARAM_INT);
		$req->execute();
		
		$rep = $req->fetch();
		$req->closeCursor();
		
		if(isset($rep['id_medicine'])){ //on a trouvé un praticien !
			$req = $this->bdd->prepare('SELECT value, key_name FROM profile
			NATURAL JOIN profile_key
			WHERE id_member = :id_member');
			$req->bindValue(':id_member', $rep['id_member'], PDO::PARAM_INT);
			$req->execute();
			
			while($rep2 = $req->fetch())
				$rep['profile'][$rep2['key_name']] = $rep2['value'];
		
			return new Doctor($rep);
		}
		else
			return new Error("Ce membre n'existe pas !");
	}
	
	/** 
	* @brief	Compte les praticiens inscrits
	* @return	int		Nombre de praticiens
	*/
	public function countDoctors()
	{
		$req = $this->bdd->query('SELECT COUNT(id_member) as nbr FROM doctor');
		$rep = $req->fetch();
		return $rep['nbr'];
	}
	
	/** 
	* @brief	Récupère la liste des médecines enregistrées
	* @return	Array		Liste des medecines
	*/
	public function getMedicines()
	{
		$req = $this->bdd->query('SELECT * FROM medicine ORDER BY medicine_name');
		return $req->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/** 
	* @brief	Recherche la liste des praticiens qui ont pour nom le mot clef renseigné
	* @param	String		$name		Mot clef de recherche
	* @return	Array		Liste des praticiens trouvés
	*/
	public function searchByName($name)
	{
		$result = array();
		
		$req = $this->bdd->prepare('SELECT key_name, value, doctor.id_medicine, medicine_name, profile.id_member FROM doctor 
		LEFT JOIN profile ON doctor.id_member = profile.id_member 
		LEFT JOIN profile_key ON profile.id_key = profile_key.id_key 
		LEFT JOIN medicine ON doctor.id_medicine = medicine.id_medicine
		WHERE doctor.id_member IN
		(
			SELECT id_member FROM doctor WHERE id_member IN 
			(
				SELECT id_member FROM profile WHERE id_key IN 
				(
					SELECT id_key FROM profile_key WHERE key_name = "nom" AND value = :name
				)
			)
		)');
		$req->bindParam(':name', $name, PDO::PARAM_STR);	
		$req->execute();
		
		while($rep = $req->fetch(PDO::FETCH_ASSOC))
		{
			$result[$rep['id_member']][$rep['key_name']] = $rep['value'];
			$result[$rep['id_member']]['medicine_name'] = $rep['medicine_name'];
			$result[$rep['id_member']]['id_medicine'] = $rep['id_medicine'];
		}
		
		return $result;
	}
	
	/** 
	* @brief	Recherche des praticiens selon une médecine et un département (optionnel)
	* @param	int			$categorie		ID de la médecine recherchée
	* @param	String		$departement	Département de recherche
	* @return	Array		Liste des praticiens trouvés
	*/
	public function searchBySpeciality($categorie, $departement = null)
	{
		$result = array();
		
		$sql = "SELECT key_name, value, medicine_name, profile.id_member FROM doctor 
			LEFT JOIN profile ON profile.id_member = doctor.id_member
			LEFT JOIN profile_key ON profile_key.id_key = profile.id_key
			LEFT JOIN medicine ON medicine.id_medicine = :id_medicine
			WHERE doctor.id_medicine = :id_medicine";
		
		if(isset($departement)){
			$sql .= " AND doctor.id_member IN 
					(SELECT id_member FROM profile WHERE id_key IN 
						(SELECT id_key FROM profile_key WHERE key_name = 'code postal' AND value LIKE :dep)
					)";
		}
			
		$req = $this->bdd->prepare($sql);
		if(isset($departement))
			$req->bindValue(':dep', $departement.'%', PDO::PARAM_STR);	
		$req->bindParam(':id_medicine', $categorie, PDO::PARAM_INT);	
		$req->execute();
		
		while($rep = $req->fetch(PDO::FETCH_ASSOC)){
			$result[$rep['id_member']][$rep['key_name']] = $rep['value'];
			$result[$rep['id_member']]['medicine_name'] = $rep['medicine_name'];
		}
		
		return $result;
	}
	
	/** 
	* @brief	Met à jour le profil d'un praticien
	* @param	Doctor		$doctor		Praticien
	* @return	Error		Succès sous forme d'information
	*/
	public function updateProfile(Doctor $doctor)
	{
		$req = $this->bdd->prepare("UPDATE doctor SET 
		  rdv_duration = :rdv_duration,			/* Mise à jour de la durée d un rendez-vous */
		  info_pro = :info_pro,					/* Mise à jour des informations professionnelles */
		  start_hour = :str_hour,				/* Mise à jour de l heure de début */
		  end_hour = :end_hour,					/* Mise à jour de l heure de fin */
		  rdv_confirm = :rd_confirm				/* mise à jour de l'option de confirmation d'un RDV */
		WHERE id_member = :id_member");
		$req->bindValue(':rdv_duration',$doctor->getRdvDuration(),PDO::PARAM_INT); 
		$req->bindValue(':info_pro',$doctor->getInfoPro(),PDO::PARAM_STR); 
		$req->bindValue(':str_hour', $doctor->getStartHour('h').":".$doctor->getStartHour('m').":".$doctor->getStartHour('s'),PDO::PARAM_STR); 
		$req->bindValue(':end_hour',$doctor->getEndHour('h').":".$doctor->getEndHour('m').":".$doctor->getEndHour('s'),PDO::PARAM_STR); 
  		$req->bindValue(':rd_confirm',$doctor->getRdvConfirm(),PDO::PARAM_INT);
 		$req->bindValue(':id_member',$doctor->getIdMember(),PDO::PARAM_INT);
 		$req->execute();

		return new Error("Le profil a été mis à jour", "info");	
	}
}