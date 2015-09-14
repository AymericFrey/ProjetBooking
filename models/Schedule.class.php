<?php
defined("_nova_district_token_") or die('');

/**
* @class	Schedule
* @brief	Représente un rendez-vous ou une plage horaire bloquée
*/
class Schedule extends Model
{
	private $idSchedule;
	private $idDoctor;
	
	/** 
	* @brief	ID du membre ayant demandé le rendez-vous. Optionnel dans le cas ou le Schedule est un bloquage de la part du praticien
	* @var		$idMember	
	*/
	private $idMember = null;
	
	/** 
	* @brief	Date de début du rendez-vous en @a Timestamp @a Unix
	* @var		$dateStart	
	*/
	private $dateStart;
	
	/** 
	* @brief	Date de fin du rendez-vous en @a Timestamp @a Unix
	* @var		$dateStop	
	*/
	private $dateStop;
	
	/** 
	* @brief	Etat du rendez-vous : @b 0 pour validé et @b 1 pour non validé
	* @var		$validate
	*/
	private $validate = 0;
	private $note = "";
	private $recursion = 0;
	
	
	/** 
	* @brief	Constructeur par défaut
	* @param	Array	$datas	Tableau pouvant contenir une ou plusieurs valeurs par défaut
	* @return	Void
	*/
	public function __construct($datas = null){
		if(isset($datas) AND $datas != null)
			$this->hydrate($datas);
	}
	
	/** 
	* @brief	Calcule la durée du rendez-vous
	* @return	int		Durée en seconde
	*/
	public function getLength()
	{
		return date_diff($dateStart - $dateStop);
	}
	
	
	//setters & getters
	public function setIdSchedule($value){
		$this->idSchedule = $value;
	}
	public function getIdSchedule(){
		return $this->idSchedule;
	}
	public function setIdDoctor($value){
		$this->idDoctor = $value;
	}
	public function getIdDoctor(){
		return $this->idDoctor;
	}
	public function setIdMember($value){
		$this->idMember = $value;
	}
	public function getIdMember(){
		return $this->idMember;
	}
	public function setDateStart($value){
		$this->dateStart = $value;
	}
	public function getDateStart(){
		return $this->dateStart;
	}
	public function setDateStop($value){
		$this->dateStop = $value;
	}
	public function getDateStop(){
		return $this->dateStop;
	}
	public function setValidate($value){
		$this->validate = $value;
	}
	public function getValidate(){
		return $this->validate;
	}

	public function getNote(){
		return $this->note;
	}

	public function setNote($value){
		$this->note = $value;
	}

	public function setRecursion($value){
		$this->recursion = $value;
	}

	public function getRecursion(){
		return $this->recursion;
	}

}