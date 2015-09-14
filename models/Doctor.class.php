<?php
defined("_nova_district_token_") or die('');

/**
* @class	Doctor
* @brief	Représentation d'un praticien
*/
class Doctor extends User
 {
	/**
	* @brief	Nom de la spécialité du praticien
	* @var		$medicineName
	*/
	private $medicineName;
	
	/**
	* @brief	Heure de début de la journée du praticien
	* @var		$startHour
	*/
	private $startHour = array("h" => 8, "m" => 0, "s" => 0);
	
	/**
	* @brief	Heure de fin de la journée du praticien
	* @var		$endHour
	*/
	private $endHour = array("h" => 8, "m" => 0, "s" => 0);
	
	/**
	* @brief	Définit si oui ou non les rendez-vous pris avec ce praticien doivent être confirmés
	* @var		$rdvConfirm
	*/
	private $rdvConfirm;
	
	/**
	* @brief	Durée de base d'un rendez-vous
	* @var		$rdvDuration
	*/
	private $rdvDuration;
	
	/**
	* @brief	Message informatif sur le praticien
	* @var		$infoPro
	*/
	private $infoPro;
	
	
	/**
	* @brief	Constructeur par défaut
	* @param	$datas			Tableau de données indexés par nom de champ SQL qui permet d'hydrater le Doctor
	* @return	Void
	*/
	public function __construct($datas = null)
	{
		parent::__construct($datas);
	}
	
	
	/**
	* @brief	Méthode de calcul du temps de travail journalier, en minutes
	* @return	int		Nombre de minutes travaillées dans la journée
	*/
	public function getWorkTime()
	{
		return ($this->endHour['h'] * 60 + $this->endHour['m']) - ($this->startHour['h'] * 60 + $this->startHour['m']);
	}
	
	public function setMedicineName($value)
	{
		$this->medicineName = $value;
	}
	public function getMedicineName()
	{
		return $this->medicineName;
	}
	
	/**
	* @brief	Définit l'heure de début de la journée de travail
	* @param	$value		Heure de type int, ou tableau indexé par [h] [m] et [s]
	*/
	public function setStartHour($value)
	{
		if(!is_int($value)){
			$temp = explode(":", $value);
			if(isset($temp[0]))
				$this->startHour['h'] = intval($temp[0]);
			if(isset($temp[1]))
				$this->startHour['m'] = intval($temp[1]);
			if(isset($temp[2]))
				$this->startHour['s'] = intval($temp[2]);
		}
		else
			$this->startHour['h'] = $value;
	}
	
	/**
	* @brief	Retourne l'heure de début de la journée de travail
	* @param	String		$custom		Ce paramètre peut prendre la valeur de h, m ou s, et détermine l'élément de temps retourné(heures, minutes ou secondes)
	* @retval	Array		Tableau indexé par h,m et s
	* @retval	int			Valeur unique entière si @a custom est définit
	*/
	public function getStartHour($custom = null)
	{
		if($custom == null)
			return $this->startHour;
		else {
			if(isset($this->startHour[$custom]))
				return $this->startHour[$custom];
		}
	}
	
	/**
	* @brief	Définit l'heure de fin de la journée de travail
	* @param	$value		Heure de type int, ou au format (h:m:s)
	*/
	public function setEndHour($value)
	{
		if(!is_int($value)){
			$temp = explode(":", $value);
			if(isset($temp[0]))
				$this->endHour['h'] = intval($temp[0]);
			if(isset($temp[1]))
				$this->endHour['m'] = intval($temp[1]);
			if(isset($temp[2]))
				$this->endHour['s'] = intval($temp[2]);
		}
		else
			$this->endHour['h'] = $value;
	}
	
	/**
	* @brief	Retourne l'heure de fin de la journée de travail
	* @param	$custom		String		Non obligatoire, ce paramètre peut prendre la valeur de h, m ou s, et détermine l'élément de temps retourné(heure, minutes ou secondes)
	* @retval	Array		Tableau indexé par h,m et s
	* @retval	int			Valeur unique entière si @a custom est définit
	*/
	public function getEndHour($custom = null)
	{
		if($custom == null)
			return $this->endHour;
		else {
			if(isset($this->endHour[$custom]))
				return $this->endHour[$custom];
		}
	}
	public function setRdvConfirm($value)
	{
		$this->rdvConfirm = $value;
	}
	public function getRdvConfirm()
	{
		return $this->rdvConfirm;
	}
	public function setRdvDuration($value)
	{
		$this->rdvDuration = $value;
	}
	public function getRdvDuration()
	{
		return $this->rdvDuration;
	}
	public function setInfoPro($value)
	{
		$this->infoPro = $value;
	}
	public function getInfoPro()
	{
		return $this->infoPro;
	}
}