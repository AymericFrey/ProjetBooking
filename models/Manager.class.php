<?php
defined("_nova_district_token_") or die('');

/**
* @class	Manager
* @brief	Gestionnaire qui référence une connexion à la base de données.
*/
abstract class Manager
{
	/** 
	* @brief	Connexion à la base de données
	* @var		$bdd
	*/
	protected $bdd = null;
	
	/** 
	* @brief	Constructeur de Manager. Récupère la connexion à la base de données
	* @return	Void
	* @note		Ne doit pas être overridé sinon la connexion à la base de données ne sera pas récupérée
	*/
	protected function __construct(){
		$this->bdd = Connection::instance()->getBdd();
	}

	/** 
	* @brief	Méthode abstraite qui ajoute un objet à la base de données
	* @param	$object		L'objet qui doit être ajouté
	*/
	abstract public function add($object);	
}