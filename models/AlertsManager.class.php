<?php
defined("_nova_district_token_") or die('');

/**
* @class	AlertsManager
* @brief	Singleton qui gère les Alertes
*/
final class AlertsManager extends Manager
{
	/** 
	* @brief	Instance unique de AlertsManager
	* @var		$instance
	*/
	private static $instance = null;
	
	/** 
	* @brief	Retourne l'instance de la classe et permet d'instancier un AlertsManager si c'est le premier appel.
	* @return	AlertsManager		Retourne l'instance de la classe AlertsManager
	*/
	public static function instance()
	{
		if(self::$instance == null)
			self::$instance = new AlertsManager();
		return self::$instance;
	}

	/**
	* @brief	Ajoute une alerte dans la base de données
	* @param 	Alert	$alert	Variable de type Alert
	* @return 	Void
	*/	
	public function add($alert)
	{
		$req = $this->bdd->prepare("INSERT INTO alert (id_member , title , message , state , date_alert) 
		VALUES( :id_member, :id_title, :id_message, :id_state, now() )");
		$req->bindValue(':id_member', $alert->getIdMember(),PDO::PARAM_INT);
		$req->bindValue(':id_title', $alert->getTitle(),PDO::PARAM_STR);
		$req->bindValue(':id_message', $alert->getMessage(),PDO::PARAM_STR);
		$req->bindValue(':id_state', $alert->getState(),PDO::PARAM_INT);
		$req->execute();
	}
	
	/**
	* @brief	Supprime une ou plusieurs alertes dans la base de données
	* @param	int		$idMember	ID du membre concerné
	* @param	int		$idAlert 	ID de l'alerte concernée, si non définie toutes les alertes du membre seront supprimées
	* @return 	Void
	*/	
	public function delete($idMember, $idAlert = NULL)
	{
		if(isset($idAlert) AND is_numeric($idAlert))
		{
			$req = $this->bdd->prepare("DELETE FROM alert where id_member=:id_member and id_alert=:id_alert");
			$req->bindParam('id_member',$idMember,PDO::PARAM_INT);
			$req->bindParam('id_alert',$idAlert,PDO::PARAM_INT);
			$req->execute();
		}
		else
		{
			$req = $this->bdd->prepare("DELETE FROM alert where id_member=:id_member");
			$req->bindParam('id_member',$idMember,PDO::PARAM_INT);
			$req->execute();
		}
	}

	/**
	* @brief	Suppression des alertes de plus de 2 mois
	* @param	int		$idMember	ID du membre concerné, si non renseigné ce sont les alertes de tous les membres qui seront supprimées
	* @return 	Void
	*/
	public function clean($idMember = NULL)
	{
		if($idMember!=NULL)
		{
			$req = $this->bdd->prepare("DELETE FROM alert WHERE date_alert <= (now() - interval 2 month) and id_member = :id_member and state = 0");
			$req->bindParam(':id_member', $idMember,PDO::PARAM_INT);
			$req->execute();
		}
		else
		{
			$req = $this->bdd->query("DELETE FROM alert WHERE date_alert <= (now() - interval 2 month)  and state = 0");
		}
	}
	
	/**
	* @brief	Récupération des alertes
	* @param	int				$idMember	ID du membre concerné
	* @return	Array[Alert]	Retourne une liste d'alertes
	*/	
	public function getAlert($idMember)
	{
		$result = array();

		$req = $this->bdd->prepare("SELECT * from alert where id_member = :id_member ORDER BY date_alert DESC");
		$req->bindParam(':id_member', $idMember,PDO::PARAM_INT);
		$req->execute();

		while($rep = $req->fetch())
		{
			$result[] = new Alert($rep);
		}
		
		return $result;

	}

	/**
	* @brief	Change le statut d'une alerte en lue
	* @param	int		$idMember	ID du membre concerné
	* @return	Void
	*/	
	public function changeState($idMember)
	{
		$req = $this->bdd->prepare("UPDATE alert SET state = 0 WHERE state = 1 AND id_member = :id_member");
		$req->bindParam(':id_member', $idMember,PDO::PARAM_INT);
		$req->execute();
	}

	/**
	* @brief	Récupération du nombre de nouvelles alertes
	* @param	int		$idMember	ID du membre concerné
	* @return	int		Retourne le nombre des nouvelles alertes
	*/	
	public function getNumberNewAlert($idMember)
	{
		$req = $this->bdd->prepare("SELECT COUNT(*) AS NewAlert FROM alert WHERE state = 1  AND id_member = :id_member");
		$req->bindParam(':id_member', $idMember,PDO::PARAM_INT);
		$req->execute();

		$rep = $req->fetch();
		return $rep['NewAlert'];
	}

	/**
	* @brief	Génération d'une alerte prédéfinie 
	* @param	int			$idMember	ID du membre concerné
	* @param	Schedule	$Schedule	Représente un rendez-vous 
	* @param	int			$idAlert	Numéro de l'alerte voulue
	* @return 	Void 
	*
	* Liste des possibilités pour @b $idAlert :
	* 	- 0 : alerte PATIENT pour ANNULATION du rendez-vous (à la demande du patient)
	* 	- 1 : alerte PATIENT pour ANNULATION et FACTURATION du rendez-vous (annulation à moins de 24h du rdv)
	* 	- 2 : alerte MEDECIN pour ANNULATION du rendez-vous
	* 	- 3 : alerte MEDECIN pour ANNULATION et FACTURATION du rendez-vous
	* 	- 4 : alerte PATIENT pour ANNULATION du rendez-vous (à le demande du médecin)
	* 	- 5 : alerte PATIENT pour CONFIRMATION du rendez-vous (par le médecin)
	* 	- 6 : alerte PATIENT pour CONFIRMATION de son rendez-vous, sans attente de confirmation du médecin
	* 	- 7 : alerte PATIENT pour PRISE EN COMPTE de son rendez-vous, avec attente de confirmation de la part du médecin
	* 	- 8 : alerte MEDECIN pour NOUVEAU Rendez-vous sans confirmation
	* 	- 9 : alerte MEDECIN pour NOUVEAU Rendez-vous avec confirmation
	* 	- 10 : alerte PATIENT demande à être PRATICIEN
	*/	
	public function generateAutomaticMessage($idMember, $Schedule, $idAlert){
		$alert = new Alert();
		
		switch ($idAlert)
		{
			case 0: 
				$title = "Annulation de votre rendez-vous";
				$alertMessage ='Votre rendez-vous du '.date("j-m à H:i", $Schedule->getDateStart()).' a bien été annulé.';
				break;
			case 1:
				$title = "Annulation de votre rendez-vous à moins de 24h";
				$alertMessage ='Votre rendez-vous du '.date("j-m à H:i", $Schedule->getDateStart()).' a été annulé dans les 24 heures qui le précèdent. Conformément aux conditions générales d\'utilisation, le rendez-vous peut vous être facturé au tarif réglementaire. Veuillez contacter votre praticien';
				break;
			case 2:	
				$title = "Annulation de rendez-vous par le patient";
				$alertMessage ='Le rendez-vous du '.date("j-m à H:i", $Schedule->getDateStart()).' de M./Mme '.$_SESSION['user']->getProfile('nom').' '.$_SESSION['user']->getProfile('prénom').' a été annulé.';
				break;
			case 3: 
				$title = "Annulation de rendez-vous par le patient à moins de 24h";
				$alertMessage ='Le rendez-vous du '.date("j-m à H:i", $Schedule->getDateStart()).' de M./Mme '.$_SESSION['user']->getProfile('nom').' '.$_SESSION['user']->getProfile('prénom').' a été annulé. Conformément aux conditions générales d\'utilisation, le rendez-vous pourra lui être facturé au tarif réglementaire.';
				break;
			case 4:
				$title = "Annulation de votre rendez-vous par le médecin";
				$alertMessage ='Votre médecin, M./Mme '.$_SESSION['user']->getProfile('nom').' '.$_SESSION['user']->getProfile('prénom').' ne peut malheureusement pas confirmer votre rendez-vous du '.date("j-m à H:i", $Schedule->getDateStart()).', en raison d\'un imprévu. Nous vous invitons à reconsulter son emploi du temps si vous souhaitez prendre un autre rendez-vous. Toutes nos excuses pour la gène occasionnée.';
				break;
			case 5:
				$title = "Confirmation de votre rendez-vous par le médecin";
				$alertMessage ='Votre médecin, M./Mme '.$_SESSION['user']->getProfile('nom').' '.$_SESSION['user']->getProfile('prénom').' a bien confirmé votre rendez-vous du '.date("j-m à H:i", $Schedule->getDateStart());
				break;
			case 6:
				$title = "Confirmation de votre rendez-vous";
				$alertMessage = 'Votre rendez-vous du '.date("j-m à H:i", $Schedule->getDateStart()).' à bien été confirmé. Retrouvez les détails de vos rendez-vous sur votre page personnelle.';
				break;
			case 7:
				$title = "Attente de confirmation de votre rendez-vous";
				$alertMessage = 'Votre rendez-vous du '.date("j-m à H:i", $Schedule->getDateStart()).' à bien été pris en compte. Vous serez informé(e) prochainement de la confirmation de votre rendez-vous par le médecin. Retrouvez les détails de vos rendez-vous sur votre page personnelle.';
				break;
			case 8: 
				$title = "Nouveau rendez-vous";
				$alertMessage = 'Un nouveau rendez-vous a été pris par M./Mme '.$_SESSION['user']->getProfile('nom').' '.$_SESSION['user']->getProfile('prénom').' pour le '.date("j-m à H:i", $Schedule->getDateStart()).' prochain.';
				break;
			case 9:
				$title = "Nouveau rendez-vous en attente de votre confirmation";
				$alertMessage = 'Un nouveau rendez-vous a été pris pas M./Mme '.$_SESSION['user']->getProfile('nom').' '.$_SESSION['user']->getProfile('prénom').' pour le '.date("j-m à H:i", $Schedule->getDateStart()).' prochain. N\'oubliez pas de confirmer le rendez-vous sur votre page dédiée.';
				break;
		}
		
		if(isset($alertMessage)){
			$alert->setMessage($alertMessage);
			$alert->setTitle($title);
			$alert->setIdMember($idMember);
			
			$this->add($alert);
		}
	}

}