<?php
defined("_nova_district_token_") or die('');

/**
* @class	JSLoader
* @brief	Autoloader de fichiers Javascript
*/
class JSLoader 
{
	/**
	* @brief	Détermine si l'application est en développement ou en production
	* @var		$production	
	*/
	private static $production = false;
	
	/**
	* @brief	Dossier racine des fichiers Javascript
	* @var		$production		
	*/
	private static $base = "js/";
	
	/**
	* @brief	Nom par défaut du fichier Javascript compressé qui est utilisé pour la production
	* @var		$prodJS		
	*/
	private static $prodJS = "minify-booking.js";
	
	
	/**
	* @brief	Charge les fichiers Javascript selon le nom de la page et selon l'état de l'application (production ou développement)
	* @param	String		$controlName	Nom de la page qui demande à inclure le Javascript
	* @return 	String		Code HTML nécessaire à l'inclusion du/des codes Javascript
	* @see 		JSLoader::$production
	*/
	public static function loader($controlName)
	{
		$result = "";
		if(self::$production == false) {
			$file = array();
			if(file_exists(self::$base."common.js"))
				$file[] = self::$base.'common.js';
			if(file_exists(self::$base.$controlName.".js"))
				$file[] = self::$base.$controlName.".js";
				
			
			foreach($file as $f)
				$result .= self::getHTML($f);
		}
		else 
			$result = getHTML(self::$base.self::$prodJS);
		
		return $result;
	}
	
	/**
	* @brief	Retourne le code HTML nécessaire à l'inclusion de fichier Javascript externes
	* @param	String		$url	Url du fichier javascript à inclure
	* @return 	String		Code HTML
	*/
	public static function getHTML($url)
	{
		return "<script type='text/javascript' src='".$url."'></script>
		";
	}
}


?>