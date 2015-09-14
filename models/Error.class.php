<?php
defined("_nova_district_token_") or die('');

/**
* @class	Error	
* @brief	Représentation d'une erreur, qui avertit l'utilisateur d'un problème, ou d'une situation particulière
*/
class Error
{
	/**
	* @brief	Contenu du message d'erreur
	* @var		$message	
	*/
	private $message;
	
	/**
	* @brief	Spécifie le type de message, @a erreur ou @a info
	* @var		$type
	*/
	private $type;
	
	/**
	* @brief	Constructeur par défaut
	* @param	$msg	Message de l'erreur
	* @param	$type	Type d'erreur, par défaut cette valeur vaut @a error
	* @return	Void
	*/
	public function __construct($msg = "[Erreur non définie]", $type = 'error'){
		$this->message = $msg;
		$this->type = $type;
	}
	
	/**
	* @brief	Méthode qui retourne le texte HTML de l'erreur
	* @return	string		HTML
	*/
	public function getHTML(){
		return "<div class='error error-".$this->type."'>".$this->message."</div>";
	}
	
	//getters & setters
	public function getMessage(){
		return $this->message;
	}
	public function getType(){
		return $this->type;
	}
}