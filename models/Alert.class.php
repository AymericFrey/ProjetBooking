<?php
defined("_nova_district_token_") or die('');

/**
* @class	Alert
* @brief	Classe qui représente une alerte
*/
class Alert extends Model {
	private $idAlert;
	private $idMember;
	private $message;
	private $title;
	
	/**
	* @brief	Représente l'état lu ou non lu d'une alerte. @b 1 signifie non lu
	* @var	$state
	*/
	private $state = 1;
	private $dateAlert;

	//getters & setters
	public function setIdAlert($id)
	{
		$this->idAlert = $id;
	}

	public function getIdAlert()
	{
		return $this->idAlert;
	}

	public function setIdMember($id)
	{
		$this->idMember = $id;
	}

	public function getIdMember()
	{
		return $this->idMember;
	}
	
	public function setMessage($datas)
	{
		$this->message = $datas;
	}

	public function getMessage()
	{
		return $this->message;
	}
	
	public function setTitle($datas)
	{
		$this->title = $datas;	
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setState($datas)
	{
		$this->state = $datas;	
	}

	public function getState()
	{
		return $this->state;
	}
	
	public function setDateAlert($datas)
	{
		$this->dateAlert = $datas;	
	}

	public function getDateAlert()
	{
		return $this->dateAlert;
	}
}