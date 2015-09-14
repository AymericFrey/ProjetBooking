<?php
defined("_nova_district_token_") or die('');

/**
* @class	User
* @brief	Classe abstraite qui représente un utilisateur
*/
abstract class User extends Model{
	protected $idMember;
	protected $email;
	protected $password;
	protected $session;
	protected $level; //je ne commente pas ces attributs car ils sont assez explicites, idem les getters/setters SAUF s'ils sont plus compliqué que juste retourner une valeur
	
	/** 
	* @brief	Tableau[] indexé par le nom des champs du profil.
	* @var 		$profile
	*/
	protected $profile;
	
	/** 
	* @brief	ID du médecin généraliste lié à l'utilisateur
	* @var 		$idGeneralist
	*/
	protected $idGeneralist = null;
	

	/** 
	* @brief	Ajoute au profil une ou plusieurs valeurs
	* @param	Array	$data	Liste des champs à modifier ou à ajouter au profil
	* @return	Void
	*/
	public function addToProfile(array $data){
		foreach($data as $key => $value){
			$this->profile[$key] = $value;
		}
	}
	
	//getters & setters
	public function setProfile($datas)
	{
		$this->profile = $datas;
	}
	
	/** 
	* @brief	Récupère une ou plusieurs valeurs du profil
	* @param	String	$key	Index particulier (optionnel) à rechercher dans le tableau profile
	* @retval	String			Si non trouvé à l'index demandé : @a Non @a renseigné
	* @retval	String			Si l'index existe dans profil, la valeur à cet index
	* @retval	String[]		Si @a $key non renseigné, le profil complet
	*/
	public function getProfile($key = null)
	{
		if($key == null)
			return $this->profile; //array
			
		if(isset($this->profile[$key]))
			return $this->profile[$key];
		return "Non renseigné";
	}
	public function getIdMember()
	{
		return $this->idMember;
	}
	public function setIdMember($value)
	{
		$this->idMember = $value;
	}
	public function getEmail()
	{
		return $this->email;
	}
	public function setEmail($value)
	{
		$this->email = $value;
	}
	public function getSession()
	{
		return $this->session;
	}
	public function setSession($value)
	{
		$this->session = $value;
	}
	public function getStatus()
	{
		return $this->idStatus;
	}
	public function setIdStatus($value)
	{
		$this->idStatus = $value;
	}
	public function getPassword()
	{
		return $this->password;
	}
	public function setPassword($value)
	{
		$this->password = $value;
	}
	public function getLevel()
	{
		return $this->level;
	}
	public function setLevel($value)
	{
		$this->level = $value;
	}
	public function getIdGeneralist()
	{
		return $this->idGeneralist;
	}
	public function setIdGeneralist($value)
	{
		$this->idGeneralist = $value;
	}
}