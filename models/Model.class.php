<?php
defined("_nova_district_token_") or die('');

/**
* @class	Model
* @brief	Classe abstraite qui permet d'hydrater les objets avec des valeurs provenant de la base de données.
*/
abstract class Model {
	/**
	* @brief	Constructeur par défaut
	* @param	array	$datas		Tableau optionnel de valeurs à utiliser pour la création de l'objet
	* @return 	Void
	*/
	public function __construct($datas = null)
	{
		if(isset($datas) AND $datas != null)
			$this->hydrate($datas);
	}

	/**
	* @brief	Hydrate l'objet en assignant les valeurs, du tableau @a $datas renseigné, aux attributs.
	*			Cela ne fonctionne que si les setters existent pour ces valeurs.
	* @param	array	$datas	Tableau de valeurs indexé par nom d'attribut
	* @return 	Void
	*/
	protected function hydrate(array $datas){
		foreach ($datas as $key => $value){
			$temp = explode("_", $key);
			$name = "";
			foreach($temp as $val) {
				$name .= ucfirst($val);
			}

			$method = "set".ucfirst($name);
			if (method_exists($this, $method))
				$this->$method($value);
		}
	}
}