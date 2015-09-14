<?php
defined("_nova_district_token_") or die('');

/**
* @class	AdminManager
* @brief 	Singleton qui fournit des outils d'administration supplémentaires
*/
defined("_nova_district_token_") or die('');

final class AdminManager extends Manager
{
	/** 
	* @brief	Instance unique de AdminManager
	* @var		$instance
	*/
	private static $instance = null;
	
	/** 
	* @brief	Retourne l'instance de la classe et permet d'instancier un AdminManager si c'est le premier appel.
	* @return	AdminManager		Retourne l'instance de la classe AdminManager
	*/
	public static function instance()
	{
		if(self::$instance == null)
			self::$instance = new AdminManager();
		return self::$instance;
	}
	
	
	public function add($admin) { }
	
	
	/**
	* @brief 	Compte le nombre de personnes en attente d'être passées en statut 'praticien'
	* @return	int		$value		Retourne le nombre de personnes en attente 
	*/
	public function countDoctorsInWait()
	{
		$req = $this->bdd->query('SELECT COUNT(id_waiting) AS total FROM waiting_doctor');
		$compte = $req->fetch(PDO::FETCH_ASSOC);

		return $compte['total'];
	}	

	/**
	* @brief 	Recherche des membres par mot clef
	* @param	String		$name		Retourne la liste des membres
	* @retval	Member		Retourne la liste des membres
	* @retval	Error		Erreur si aucun membre trouvé
	*/
	public function searchUser($name)
	{
		$req = $this->bdd->prepare('SELECT * FROM member 
			LEFT JOIN profile ON member.id_member = profile.id_member 
			LEFT JOIN profile_key ON profile.id_key = profile_key.id_key
			WHERE value = :name');
		$req->bindValue(':name', $name, PDO::PARAM_STR);
		$req->execute();
		
		$result = array();
		while($rep = $req->fetch())
		{
			$req2 = $this->bdd->prepare('SELECT value, key_name, id_member FROM profile
			NATURAL JOIN profile_key
			WHERE id_member = :id_member');
			$req2->bindValue(':id_member', $rep['id_member'], PDO::PARAM_INT);
			$req2->execute();
			
			while($rep2 = $req2->fetch())
				$rep['profile'][$rep2['key_name']] = $rep2['value'];
			
			$result[] = new Member($rep);
		}
		
		if (count($result)>0)
			return $result;
		else
			return new Error("Aucun membre trouvé");
	}

	/**
	* @brief 	Envoie un message à un membre
	* @param	int		$idUser 	ID du membre qui doit recevoir le message
	* @param	String	$msg 		Message à envoyer
	* @return	Error	Retourne une information
	*/
	public function sendMessage($idUser, $msg)
	{
		$privateAlert = new Alert();
		$privateAlert->setIdMember($idUser);
		$privateAlert->setMessage($msg);
		$privateAlert->setTitle('Message de l\'administrateur');
		
		AlertsManager::instance()->add($privateAlert);
		
		return new Error ("Votre message a bien été envoyé");
	}
	
	/**
	* @brief 	Recherche tous les membres en attente de validation pour obtenir le statut 'praticien'
	* @retval	Array		Retourne la liste des membres
	* @retval	Error		Retourne une information si aucun membre en attente
	*/
	public function searchAllFutureDoctors()
	{	
		$result = array();
		
		$req = $this->bdd->prepare('SELECT key_name, value, medicine_name, waiting_doctor.id_member,  waiting_doctor.id_medicine FROM waiting_doctor
			LEFT JOIN profile ON waiting_doctor.id_member = profile.id_member 
			LEFT JOIN profile_key ON profile.id_key = profile_key.id_key 
			LEFT JOIN medicine ON waiting_doctor.id_medicine = medicine.id_medicine');
		$req->execute();
		
		while($rep = $req->fetch(PDO::FETCH_ASSOC))
		{
			$result[$rep['id_member']][$rep['key_name']] = $rep['value'];
			$result[$rep['id_member']]['medicine_name'] = $rep['medicine_name'];
			$result[$rep['id_member']]['id_medicine'] = $rep['id_medicine'];
		}
		
		if (count($result)>0)
			return $result;
		else
			return new Error("Aucun médecin en attente");
	}

	/**
	* @brief 	Change le statut d'un membre vers le statut 'praticien'
	* @param	int		$idUser			ID de l'utilisateur
	* @param	String	$newStatus		Statut à définir pour l'utilisateur : @a member ou @a doctor
	* @param 	int		$medicine		ID de la medecine pratiquée
	* @return 	Error	Un message contenant le résultat de l'opération
	*/
	public function changeStatus($idUser, $newStatus, $medicine)
	{
		$statusList = array("member", "doctor");
		
		if(in_array($newStatus, $statusList))
		{
			if($newStatus == "doctor")
			{
				$req_check = $this->bdd->prepare ('SELECT * FROM doctor WHERE id_member = :id_member');
				$req_check->bindParam(':id_member', $idUser, PDO::PARAM_INT);
				$req_check->execute();
				$rep_check = $req_check->fetch();
				$req_check->closeCursor();

				if (!isset($rep_check['id_member']))
				{
					$req = $this->bdd->prepare('INSERT INTO doctor (id_member, id_medicine) VALUES (:id_member, :id_medicine)');
					$req->bindParam(':id_member', $idUser, PDO::PARAM_INT);
					$req->bindParam(':id_medicine', $medicine, PDO::PARAM_INT);
					$req->execute();
					$req->closeCursor();
					
					$req = $this->bdd->prepare('DELETE FROM waiting_doctor WHERE id_member = :id_member');
					$req->bindParam(':id_member', $idUser, PDO::PARAM_INT);
					$req->execute();
					$req->closeCursor();
					
					return new Error('la changement vers le statut de médecin a bien été effectué');
				}
				else
					return new Error('la personne est déjà répertoriée dans la catégorie "médecins"');
			}
			else 
			{			
				$req_check = $this->bdd->prepare ('SELECT * FROM doctor WHERE id_member = :id_member');
				$req_check->bindParam(':id_member', $idUser, PDO::PARAM_INT);
				$req_check->execute();
				$rep_check = $req_check->fetch();
				$req_check->closeCursor();	
				
				if(isset($rep_check['id_member']))
				{	
					$req = $this->bdd->prepare('DELETE FROM doctor WHERE id_member = :id_member');
					$req->bindParam(':id_member', $idUser, PDO::PARAM_INT);
					$req->execute();
					$req->closeCursor();
					
					return new Error('l\'annulation du statut de médecin a bien été effectué');
				}
				else
					return new Error('la personne n\'est pas répertoriée dans la catégorie "médecins"');
			}
		}
		return new Error("Impossible de changer votre statut");
	}		
}