<?php
defined("_nova_district_token_") or die('');
require_once "Config.inc";

/**
* @class	Connection
* @brief	Singleton qui permet de se connecter à la base de données
*/
class Connection {
	/** 
	* @brief	Instance de Connection
	* @var		$m_instance
	*/
	private static $m_instance = null;
	
	/** 
	* @brief	Connexion à la base de données
	* @var		$m_bdd
	*/
	private $m_bdd = null;
	
	/** 
	* @brief	Constructeur par défaut qui initialise la connexion à la base de données
	* @return	Void
	*/
	private function __construct(){
		try {
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
			$this->m_bdd = new PDO('mysql:host='.SQL_HOST.'; dbname='.SQL_DBNAME, SQL_USER, SQL_PASS, $pdo_options);
		}
		catch (Exception $e){
			$this->m_bdd = null;
			die("Impossible d'établir une connexion à la base de donnée.");
		}
	}
	
	/** 
	* @brief	Méthode qui permet de récupérer l'instance unique de Connection
	* @return	Connection	Instance unique
	*/
	public static function instance(){
		if(self::$m_instance == null){
			self::$m_instance = new Connection();
		}
		return self::$m_instance;
	}
	
	/** 
	* @brief	Retourne la connexion à la base de données
	*/
	public function getBdd(){
		return $this->m_bdd;
	}	
}
?>