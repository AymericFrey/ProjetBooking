<?php
defined("_nova_district_token_") or die('');

/**
* @class	SchedulesManager
* @brief	Singleton qui gère les rendez-vous et les blocages d'horaires
*/
final class SchedulesManager extends Manager
{
	/** 
	* @brief	Instance unique de SchedulesManager
	* @var		$instance
	*/
	private static $instance = null;
	
	
	/** 
	* @brief	Retourne l'instance de la classe et permet d'instancier un SchedulesManager si c'est le premier appel.
	* @return	SchedulesManager		Retourne l'instance de la classe
	*/
	public static function instance()
	{
		if(self::$instance == null)
			self::$instance = new SchedulesManager();
		return self::$instance;
	}
	
	/** 
	* @brief	Temps maximum en heure pour annuler un rendez-vous pris auprès d'un praticien
	* @var		$maxTimeToCancel
	*/
	private $maxTimeToCancel = 24;
	
	/** 
	* @brief	Constructeur par défaut. Appelle le constructeur de Manager
	* @return	Void
	*/
	protected function __construct()
	{
		parent::__construct();
	}
	
	/** 
	* @brief	Ajoute un rendez-vous à la base de données
	* @param 	Schedule	$sc		Rendez-vous à enregistrer dans la base de données
	* @retval	Void		Succès
	* @retval	Error		Erreur
	*/
	public function add($sc)
	{
		//vérif dans la base que le rdv est possible 
		$req = $this->bdd->prepare ('SELECT id_schedule FROM block_schedule 
		WHERE id_doctor = :id_doctor AND date_start BETWEEN :datestart AND :datestop') ;
		$req->bindValue (':id_doctor', $sc->getIdDoctor(), PDO::PARAM_INT);
		$req->bindValue (':datestart', $sc->getDateStart(), PDO::PARAM_INT);//PARAM_DATE ???
		$req->bindValue (':datestop', $sc->getDateStop(), PDO::PARAM_INT);	
		$req->execute();
		
		$rep_checkBlock = $req->fetch();
		$req->closeCursor();

		if (!isset($rep_checkBlock['id_schedule']))
		{
			//vérif que le rdv n'est pas déjà pris
			$req = $this->bdd->prepare ('SELECT id_schedule FROM schedule 
			WHERE id_doctor = :id_doctor AND (
			(date_start > :datestart AND date_start < :datestop) OR
			(date_stop > :datestart AND date_stop < :datestop)
			)');
			$req->bindValue (':id_doctor', $sc->getIdDoctor(), PDO::PARAM_INT);
			$req->bindValue (':datestart', $sc->getDateStart(), PDO::PARAM_INT);
			$req->bindValue (':datestop', $sc->getDateStop(), PDO::PARAM_INT);	
			$req->execute();
			$rep_available = $req->fetch();
			$req->closeCursor();
		
			if (!isset($rep_available['id_schedule']))
			{
				//insertion du schedule dans la bdd
				$req_sc = $this->bdd->prepare('INSERT INTO schedule (id_doctor, id_member, date_start, date_stop, note)
				VALUES (:id_doctor, :id_member, :datestart, :datestop, :note)');
				$req_sc->bindValue (':id_doctor', $sc->getIdDoctor(), PDO::PARAM_INT);
				$req_sc->bindValue ('id_member', $sc->getIdMember(), PDO::PARAM_INT);
				$req_sc->bindValue ('note', $sc->getNote(), PDO::PARAM_STR);
				$req_sc->bindValue (':datestart', $sc->getDateStart(), PDO::PARAM_INT);
				$req_sc->bindValue (':datestop', $sc->getDateStop(), PDO::PARAM_INT);	
				$req_sc->execute();
			}
			else 
				return new Error("Votre médecin ne sera pas disponible sur cette période");
		}	
		else 
			return  new Error("les informations de prise de rendez-vous ne sont pas complètes");
	}

	/** 
	* @brief	Ajoute un blocage à la base de données
	* @param 	Schedule	$block		Bloquage à enregistrer dans la base de données
	* @return	Error		Retourne une information avec le message de succès
	*/
	public function addBlock($block)
	{
		$req = $this->bdd->prepare('INSERT INTO block_schedule (id_doctor, date_start, date_stop, note, recursion) VALUES (:idDoctor , :dstart, :dstop, :note, :recursion) ');
		$req->bindValue(':dstart', $block->getDateStart(), PDO::PARAM_STR);
		$req->bindValue(':dstop', $block->getDateStop(), PDO::PARAM_STR);
		$req->bindValue(':note',$block->getNote(),PDO::PARAM_STR);
		$req->bindValue (':idDoctor', $block->getIdDoctor(), PDO::PARAM_INT);
		$req->bindValue (':recursion', $block->getRecursion(), PDO::PARAM_INT);			
		$req->execute();	

		return new Error("Le blocage a été ajouté", "info");	
	}

	/** 
	* @brief	Récupère l'heure de fin du dernier blocage ou rendez-vous de l'emploi du temps jusqu'au début de la journée (entre $dayStartHour et $scStart)
	* @param 	int		$idDoctor		ID du praticien de l'emploi du temps concerné
	* @param 	int		$dayStartHour	Heure de début de la journée dans laquelle la recherche doit s'effectuer
	* @param 	int		$scStart		Date de début du rendez-vous de référence (on cherche avant cette date)
	* @return	int		Retourne la date en timestamp du dernier rendez-vous ou blocage. A défaut d'enregistrement, c'est la date du début de la journée recherchée qui est retournée
	* @see		SchedulesManager::getPreviousSchedule()
	*/
	public function getLastBlockedBlockHour($idDoctor, $dayStartHour, $scStart){
		$req = $this->bdd->prepare('SELECT date_stop FROM schedule 
		WHERE date_start >= :dayStartHour AND date_start < :scStart AND id_doctor = :id_doctor
		ORDER BY date_stop DESC
		LIMIT 1');
		$req->bindParam(':dayStartHour', $dayStartHour, PDO::PARAM_INT);
		$req->bindParam(':scStart', $scStart, PDO::PARAM_INT);
		$req->bindParam(':id_doctor', $idDoctor, PDO::PARAM_INT);
		$req->execute();
		$rep = $req->fetch();
		
		$req = $this->bdd->prepare('SELECT date_stop FROM block_schedule 
		WHERE date_start >= :dayStartHour AND date_start < :scStart AND id_doctor = :id_doctor
		ORDER BY date_stop DESC
		LIMIT 1');
		$req->bindParam(':dayStartHour', $dayStartHour, PDO::PARAM_INT);
		$req->bindParam(':scStart', $scStart, PDO::PARAM_INT);
		$req->bindParam(':id_doctor', $idDoctor, PDO::PARAM_INT);
		$req->execute();
		$rep2 = $req->fetch();
		
		if(isset($rep2['date_stop']) AND isset($rep['date_stop'])){
			if($rep['date_stop'] >= $rep2['date_stop'])
				return $rep['date_stop'];
			return $rep2['date_stop'];
		}
		else if(isset($rep2['date_stop']))
			return $rep2['date_stop'];
		else if(isset($rep['date_stop']))
			return $rep['date_stop'];
		
		return $dayStartHour;
	}

	/** 
	* @brief	Supprime une date bloquée par le praticien.
	* @param 	int		$idDoctor		ID du praticien de l'emploi du temps concerné
	* @param 	int		$idSchedule		ID du blocage à supprimer de la base de données
	* @return	Void	
	*/
	public function deleteBlock($idDoctor, $idSchedule)
	{
		$req = $this->bdd->prepare('DELETE FROM block_schedule WHERE id_schedule = :id_schedule AND id_doctor = :id_doctor');
		$req->bindParam(':id_schedule', $idSchedule, PDO::PARAM_INT);
		$req->bindParam(':id_doctor', $idDoctor, PDO::PARAM_INT);
		$req->execute();
	}

	/** 
	* @brief	Récupère les rendez-vous compris entre $fromDate et $toDate
	* @param 	$doctor				ID du praticien de l'emploi du temps concerné @b ou objet Doctor
	* @param 	int		$fromDate	@a Timestamp @a Unix de la date de départ pour la recherche
	* @param 	int		$toDate		@a Timestamp @a Unix de la date de fin pour la recherche
	* @return	Void	
	*/
	public function getSchedule($doctor, $fromDate, $toDate) //$doctor = id ou objet
	{
		return $this->getScheduleFrom("schedule", $doctor, $fromDate, $toDate);
	}
	
	/** 
	* @brief	Récupère les blocages compris entre $fromDate et $toDate
	* @param 	$doctor				ID du praticien de l'emploi du temps concerné @b ou objet Doctor
	* @param 	int		$fromDate	@a Timestamp @a Unix de la date de départ pour la recherche
	* @param 	int		$toDate		@a Timestamp @a Unix de la date de fin pour la recherche
	* @return	Void	
	*/
	public function getBlockSchedule($doctor, $fromDate, $toDate)
	{
		return $this->getScheduleFrom("block_schedule", $doctor, $fromDate, $toDate);
	}
	
	/** 
	* @brief	Récupère les blocages compris entre $fromDate et $toDate
	* @param 	String	$table		Table de la base de données ou il faut effectuer la rechercher (blocage ou rendez-vous)
	* @param 	int		$doctor		ID du praticien de l'emploi du temps concerné @b ou objet Doctor
	* @param 	int		$fromDate	@a Timestamp @a Unix de la date de départ pour la recherche
	* @param 	int		$toDate		@a Timestamp @a Unix de la date de fin pour la recherche
	* @return	Void
	*/
	private function getScheduleFrom($table, $doctor, $fromDate, $toDate)
	{
		if(Tools::getClass($doctor) == "Doctor")
			$idDoc = $doctor->getIdMember();
		else
			$idDoc = $doctor;
	
		$req = $this->bdd->prepare("SELECT * FROM ".$table." 
		WHERE id_doctor = :id_doctor 
		AND (date_start BETWEEN :date_start AND :date_stop OR date_stop BETWEEN :date_start AND :date_stop) ");
		$req->bindValue('id_doctor', $idDoc, PDO::PARAM_INT);
		$req->bindParam('date_start', $fromDate, PDO::PARAM_STR);
		$req->bindParam('date_stop', $toDate, PDO::PARAM_STR);
		$req->execute();
		
		$rdv = array();
		while($rep = $req->fetch()){
			$rdv[] = new Schedule($rep);
		}
		return $rdv;
	}
	
	/** 
	* @brief	Récupère la liste des blocages effectués par le praticien
	* @param 	int		$idDoctor	ID du praticien
	* @param 	int		$recursion	Filtre sur la récursivité du blocage en jours : @b 7 ou @b 0
	* @return	Array[Schedule]		Retourne un tableau des rendez-vous trouvés
	*/
	public function getRecursiveSchedule($idDoctor, $recursion = 7)
	{
		$req= $this->bdd->prepare("SELECT * FROM block_schedule 
		WHERE id_doctor = :idDoctor AND recursion = :recursion");
		$req->bindValue(':idDoctor', $idDoctor, PDO::PARAM_INT);
		$req->bindValue(':recursion', $recursion, PDO::PARAM_INT);
		$req->execute();

		$result = array();
		while($rep = $req->fetch())
			$result[] = new Schedule($rep);
		
		return $result;
	}
	
	/** 
	* @brief	Récupère la liste des rendez-vous ainsi que des informations sur le membre qui les ont pris
	* @param 	int		$doctor		ID du praticien
	* @param 	int		$fromDate	@a Timestamp @a Unix de la date de départ pour la recherche
	* @param 	int		$toDate		@a Timestamp @a Unix de la date de fin pour la recherche
	* @return	Array	Liste des rendez-vous
	*/
	public function getScheduleList($doctor, $fromDate, $toDate)
	{	
		$result = array();
		$req = $this->bdd->prepare('SELECT * FROM schedule 
		WHERE id_doctor = :id_doctor AND date_start >= :date_start AND date_start <= :date_end
		ORDER BY date_start');
		$req->bindPARAM(':id_doctor', $doctor, PDO::PARAM_INT);
		$req->bindPARAM(':date_start', $fromDate, PDO::PARAM_INT);
		$req->bindPARAM(':date_end', $toDate, PDO::PARAM_INT);
		$req->execute();
		$result = $req->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($result as &$item)
		{
			$req2 = $this->bdd->prepare("SELECT profile_key.key_name, profile.value FROM profile
			NATURAL JOIN profile_key
			WHERE id_member = :id_member");
			$req2->bindValue(':id_member', $item['id_member'], PDO::PARAM_INT);
			$req2->execute();
		
			$temp = array();
			while($rep2 = $req2->fetch())
				$temp[$rep2['key_name']] = $rep2['value'];
				
			$item = array_merge($item, $temp);
			$req2->closeCursor();
		}
		return $result;
	}
	
	/** 
	* @brief	Récupère les rendez-vous à venir d'un membre ainsi que des informations sur les praticiens
	* @param 	int		$idMember	ID du membre
	* @return	Array	Liste des rendez-vous à venir et information sur les praticiens
	*/
	public function getFutureSchedule($idMember)
	{
		$result = array();
	
		$time = time();
		$req = $this->bdd->prepare("SELECT * FROM schedule
		LEFT JOIN doctor ON doctor.id_member = schedule.id_doctor
		NATURAL JOIN medicine
		WHERE schedule.id_member = :id_member AND schedule.date_start >= :datestart");
		$req->bindValue(':id_member', $idMember, PDO::PARAM_INT);
		$req->bindParam(':datestart', $time, PDO::PARAM_INT);
		$req->execute();
		$result = $req->fetchAll(PDO::FETCH_ASSOC);
	
		foreach($result as &$item)
		{
			$req2 = $this->bdd->prepare("SELECT profile_key.key_name, profile.value  FROM profile
			NATURAL JOIN profile_key
			WHERE id_member = :id_member");
			$req2->bindValue(':id_member', $item['id_doctor'], PDO::PARAM_INT);
			$req2->execute();
			
			$temp = array();
			while($rep2 = $req2->fetch())
				$temp[$rep2['key_name']] = $rep2['value'];
				
			$item = array_merge($item, $temp);
			$req2->closeCursor();
		}
		
		return $result;
	}
	
	/** 
	* @brief	Récupère le dernier rendez-vous ou le blocage précédent @a $dateStart, avec pour limite de recherche @a $dayStart. On recherche dans le passé.
	* @param 	int			$doctor			ID du praticien de l'emploi du temps concerné
	* @param 	int			$dayStart		@a Timestamp @a Unix de la date de recherche minimale
	* @param 	int			$dateStart		@a Timestamp @a Unix de départ de la recherche
	* @retval	Schedule	Rendez-vous ou blocage trouvé
	* @retval	Error		Erreur car aucun rendez-vous ou blocage trouvé dans la période demandée
	* @see		SchedulesManager::getLastBlockedBlockHour()
	*/
	public function getPreviousSchedule($doctor, $dayStart, $dateStart) //obj, ts, ts
	{
		$req = $this->bdd->prepare("
		SELECT * FROM 
			((SELECT id_schedule, date_start, date_stop FROM schedule
			WHERE id_doctor = :id_doctor AND date_start < :date_start AND date_start >= :day_start
			ORDER BY date_start DESC LIMIT 1)
			UNION ALL
			(SELECT id_schedule, date_start, date_stop FROM block_schedule
			WHERE id_doctor = :id_doctor AND date_start < :date_start AND date_start >= :day_start
			ORDER BY date_start DESC LIMIT 1)) as resultats
		ORDER BY date_start DESC
		LIMIT 1
		");
		$req->bindValue('id_doctor', $doctor->getIdMember(), PDO::PARAM_INT);
		$req->bindParam('date_start', $dateStart, PDO::PARAM_INT);
		$req->bindParam('day_start', $dayStart, PDO::PARAM_INT);
		$req->execute();
		
		$rep = $req->fetch();
		if(isset($rep['id_schedule'])){
			return new Schedule($rep);
		}
		else
			return new Error("Impossible de déterminer un horaire de calibrage !");
	}
	
	/** 
	* @brief	Détermine si un rendez-vous est annulable selon la date maximum d'annulation déterminé par SchedulesManager::$maxTimeToCancel
	* @param 	int		$dateStart		Date de début du rendez-vous
	* @return	boolean					
	*/
	public function scheduleIsCancelable($dateStart)
	{
		return ($dateStart - time() - $this->maxTimeToCancel * 3600 >= 0);
	}
	
	/** 
	* @brief	Annulation d'un rendez-vous par le patient ou le praticien
	* @param 	int			$idMember		ID du membre ou du praticien faisant la demande d'annulation
	* @param 	int			$idSchedule		ID du rendez-vous
	* @retval	Schedule	Si succès, un Schedule est retourné contenant les infos du rendez-vous supprimé
	* @retval	Error		Erreur
	*/
	public function cancelSchedule($idMember, $idSchedule)
	{	
		$req1 = $this->bdd->prepare('SELECT * FROM schedule 
		WHERE id_schedule = :id_schedule AND (id_doctor = :id_member OR id_member = :id_member)');
		$req1->bindParam(':id_schedule', $idSchedule, PDO::PARAM_INT);
		$req1->bindParam(':id_member', $idMember, PDO::PARAM_INT);
		$req1->execute();
		$rep1 = $req1->fetch();
		
		if(!isset($rep1['id_schedule']))
			return new Error('');
		
		if($idMember == $rep1['id_member'] AND !$this->scheduleIsCancelable($rep1['date_start']) AND $rep1['id_doctor'] != $idMember)
			return new Error('Moins de 24h ! Veuillez contactez votre praticien par téléphone !');
		
		$req2 = $this->bdd->prepare('DELETE FROM schedule WHERE id_schedule = :id_schedule');
		$req2->bindParam(':id_schedule', $idSchedule, PDO::PARAM_INT);
		$req2->execute();
		
		return new Schedule($rep1);
	}	

	/** 
	* @brief	Confirmation d'un rendez-vous par le praticien
	* @param 	int			$idDoctor		ID du praticien demandant la confirmation
	* @param 	int			$idSchedule		ID du rendez-vous
	* @retval	Schedule	Si succès, un Schedule est retourné contenant les infos du rendez-vous mis à jour
	* @retval	Error		Erreur
	*/
	public function confirmSchedule($idDoctor, $idSchedule)
	{
		$req1 = $this->bdd->prepare('SELECT * FROM schedule 
		WHERE id_schedule = :id_schedule AND id_doctor = :id_doctor');
		$req1->bindParam(':id_schedule', $idSchedule, PDO::PARAM_INT);
		$req1->bindParam(':id_doctor', $idDoctor, PDO::PARAM_INT);
		$req1->execute();
		$rep1 = $req1->fetch();
		
		if(!isset($rep1['id_schedule']))
			return new Error('');
		
		$req2 = $this->bdd->prepare('UPDATE schedule SET validate = 1 WHERE id_schedule = :id_schedule');
		$req2->bindParam(':id_schedule', $idSchedule, PDO::PARAM_INT);
		$req2->execute();
		
		return new Schedule($rep1);
	}
	
	/** 
	* @brief	Retourne le temps maximal pour annuler un rendez-vous
	* @return	int		Nombre d'heures
	*/
	public function getMaxTimeToCancel(){
		return $this->maxTimeToCancel;
	}

}