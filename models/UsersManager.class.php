<?php
defined("_nova_district_token_") or die('');

/**
* @class	UsersManager
* @brief	Singleton qui gère les utilisateurs
*/
final class UsersManager extends Manager
{
	/** 
	* @brief	Instance unique de UsersManager
	* @var		$instance
	*/
	private static $instance = null;
	
	
	/** 
	* @brief	Retourne l'instance de la classe et permet d'instancier un UsersManager si c'est le premier appel.
	* @return	UsersManager		Retourne l'instance de la classe
	*/
	public static function instance()
	{
		if(self::$instance == null)
			self::$instance = new UsersManager();
		return self::$instance;
	}
	
	/** 
	* @brief	Connecte un utilisateur
	* @param	String		$email		Email de connexion
	* @param	String		$pwd		Mot de passe
	* @retval	User		Retourne un nouvel utilisateur
	* @retval	Error		Erreur à la connexion
	* @note		Selon le type d'utilisateur on retourne un Member ou un Doctor
	* @see		UsersManager::createUser()
	*/
	public function connect($email, $pwd) 
	{
		$req = $this->bdd->prepare('SELECT medicine.medicine_name, doctor.*, member.* FROM member 
		LEFT JOIN doctor ON doctor.id_member = member.id_member
		LEFT JOIN medicine ON medicine.id_medicine = doctor.id_medicine
		WHERE email = :email AND password = :password');
		$req->bindParam(':email', $email, PDO::PARAM_STR);
		$req->bindParam(':password', $pwd, PDO::PARAM_STR);
		$req->execute();
		
		$rep = $req->fetch();
		$req->closeCursor();
		
		if(isset($rep['id_member'])){
			//on récupère le profil
			$req = $this->bdd->prepare('SELECT value, key_name FROM profile
			NATURAL JOIN profile_key
			WHERE id_member = :id_member');
			$req->bindValue(':id_member', $rep['id_member'], PDO::PARAM_INT);
			$req->execute();
			
			while($rep2 = $req->fetch())
				$rep['profile'][$rep2['key_name']] = $rep2['value'];
		
			return $this->createUser($rep);
		}
		else
			return new Error("Ce membre n'existe pas !");
	}
	
	/** 
	* @brief	Déconnecte un utilisateur et le redirige
	* @return	Void
	*/
	public function disconnect()
	{
		session_unset();
		session_destroy();
		Tools::redirect("home");
	}
	
	/** 
	* @brief	Regarde si un utilisateur existe dans la base de données selon certains critères de recherche.
	* @param	Array		$values		Filtres sous forme de tableau de données indexé par le nom du champ SQL. La recherche s'effectue sur ces données.
	* @param	String		$type		Méthode de recherche sur les filtres : @b AND ou @b OR
	* @return	boolean		@b TRUE si l'utilisateur existe
	*/
	public function exists($values, $type = "AND") //création d'une fonction indépendante de vérification de l'existence du mail dans la bdd
	{
		if(count($values) <= 0 OR ($type != 'AND' AND $type != "OR"))
			return false;
		
		$i = 0;
		$sql = "SELECT * FROM member WHERE ";
		foreach($values as $field => $value){
			if($i > 0)
				$sql .= " ".$type;
			$sql .= " ".$field." = :".$field;
			$i++;
		}

		$reqCheck = $this->bdd->prepare($sql);
		$reqCheck->execute($values);
		$repCheck = $reqCheck->fetch();
		
		if ($repCheck)
			return true;
		return false;
	}
	
	/** 
	* @brief	Crée un utilisateur selon son rôle (praticien ou utilisateur). Fait office de @a BUILDER
	* @param	array		$datas		Données de l'utilisateur récupérées depuis la base de données
	* @retval	Member		
	* @retval	Doctor
	*/
	public function createUser($datas){
		if(isset($datas['id_medicine']) AND $datas['id_medicine'] > 0)
			return new Doctor($datas);
		return new Member($datas);
	}
	
	/** 
	* @brief	Inscription d'un utilisateur
	* @param	User		$member		Membre à inscrire
	* @retval	User		Si succès, retourne le membre inscrit
	* @retval	Error		Raison de l'échec
	*/
	public function add($member)
	{
		if ($this->exists(array("email" => $member->getEmail())) === false) //si l'adresse mail n'existe pas on inscrit
		{
			$session = Tools::generateSession($member->getEmail());
			
			$req_subscribe = $this->bdd->prepare ('INSERT INTO member (email, password, session) 
			VALUES (:email, :pwd, :session)');
			$req_subscribe->bindValue(':email', $member->getEmail(), PDO::PARAM_STR);
			$req_subscribe->bindValue(':pwd', $member->getPassword(), PDO::PARAM_STR); //sha1
			$req_subscribe->bindParam(':session', $session, PDO::PARAM_STR);
			$req_subscribe->execute();
			
			//on tente d'ouvrir la session automatiquement
			$member = $this->connect($member->getEmail(), $member->getPassword());
			
			if(Tools::getParentClass($member) == 'User'){
				return $member;
			}
			else
				return new Error("Inscription réussi mais problème à la connexion automatique, merci de vous connecter !");
		}
		else
			return new Error("Cet email est déjà enregistré sur la plateforme. Essayez de vous connecter !");
	}
	
	/** 
	* @brief	Suppression d'un utilisateur
	* @param	int		$id_member		ID du membre à supprimer
	* @return	Error	Retourne le résultat de l'opération sous forme d'information
	*/
	public function delete($id_member)
	{
		$req = $this->bdd->prepare('DELETE * FROM member WHERE id_member = :id_member');
		$req->bindParam(':id_member', $id_member, PDO::PARAM_INT);
		$req->execute();
	
		return new Error ("le membre à bien été supprimé");
	}
	
	/** 
	* @brief	Sauvegarde le profil de l'utilisateur dans la base de données
	* @param	User		$user		ID du membre qui doit sauvegarder son profil
	* @return	boolean		@b TRUE si la sauvegarde s'est correctement déroulée
	*/
	public function saveProfile($user)
	{
		try 
		{
			$this->bdd->beginTransaction();
			
			$req = $this->bdd->prepare("SELECT * FROM profile 
			NATURAL JOIN profile_key
			WHERE id_member = :id_member");
			$req->bindValue(':id_member', $user->getIdMember(), PDO::PARAM_INT);
			$req->execute();
			
			$temp = array();
			while($rep = $req->fetch(PDO::FETCH_ASSOC)){
				$temp[$rep['key_name']]['value'] = $rep['value'];
				$temp[$rep['key_name']]['key'] = $rep['id_key'];
			}
			
			foreach($user->getProfile() as $key => $value)
			{
				if(isset($temp[$key]['value'])){
					if($temp[$key] != $value){ //différence entre BDD et User Session ?
						$req = $this->bdd->prepare("UPDATE profile SET value = :value
						WHERE id_member = :id_member AND id_key = :id_key");
						$req->bindValue(':id_member', $user->getIdMember(), PDO::PARAM_INT);
						$req->bindValue(':id_key', $temp[$key]['key'], PDO::PARAM_INT);
						$req->bindValue(':value', $value, PDO::PARAM_STR);
						$req->execute();
					}
				}
				else {
					$req = $this->bdd->prepare("INSERT INTO profile(id_member, id_key, value) 
					VALUES (:id_member, (SELECT id_key FROM profile_key WHERE key_name = :key_name), :value)");
					$req->bindValue(':id_member', $user->getIdMember(), PDO::PARAM_INT);
					$req->bindValue(':key_name', $key, PDO::PARAM_INT);
					$req->bindValue(':value', $value, PDO::PARAM_STR);
					$req->execute();
				}
			}
			
			$this->bdd->commit();
			return true;
		}
		catch(Exception $e)
		{
			$this->bdd->rollBack();
			return false;
		}
	}
	
	/** 
	* @brief	Met à jour le médecin généraliste
	* @param	User		$idMember		ID du membre qui met à jour son médecin généraliste
	* @param	int			$idGeneralist	ID du médecin généraliste
	* @return	boolean		@b TRUE si la mise à jour s'est correctement déroulée
	*/
	public function setGeneralist($idMember, $idGeneralist)
	{
		$req = $this->bdd->prepare("SELECT id_member FROM doctor
		WHERE id_member = :id_member AND id_medicine = (SELECT id_medicine FROM medicine WHERE medicine_name = 'médecin généraliste' LIMIT 1)");
		$req->bindValue(':id_member', $idGeneralist, PDO::PARAM_INT);
		$req->execute();
		
		$rep = $req->fetch();
		if(isset($rep['id_member']))
		{
			$req = $this->bdd->prepare("UPDATE member SET id_generalist = :id_generalist
			WHERE id_member = :id_member");
			$req->bindValue(':id_member', $idMember, PDO::PARAM_INT);
			$req->bindValue(':id_generalist', $idGeneralist, PDO::PARAM_INT);
			$req->execute();
			
			return true;
		}
		return false;
	}
	
	/** 
	* @brief	Ajoute un praticien aux favoris d'un utilisateur
	* @param	int			$idMember	ID du membre qui ajoute le praticien
	* @param	int			$idDoctor	ID du praticien à ajouter
	* @return	Error		Retourne une erreur ou une information sur le résultat de la fonction
	*/
	public function addDoctorToFavorites($idMember, $idDoctor)
	{
		$req = $this->bdd->prepare("SELECT favorite_doctor.*, mb.id_member AS mb_id, doc.id_member AS doc_id FROM favorite_doctor
		LEFT JOIN member as doc ON doc.id_member = :id_doctor
		LEFT JOIN member as mb ON mb.id_member = :id_member
		WHERE favorite_doctor.id_member = :id_member AND id_doctor = :id_doctor");
		$req->bindValue(':id_doctor', $idDoctor, PDO::PARAM_INT);
		$req->bindValue(':id_member', $idMember, PDO::PARAM_INT);
		$req->execute();
		
		$rep = $req->fetch();
		if(isset($rep['id_member']) AND isset($rep['mb_id']) AND isset($rep['doc_id'])) //si la relation existe
			return new Error("Vous avez déjà enregistré ce praticien dans vos favoris");
		else if(isset($rep['id_member']) AND !isset($rep['doc_id']))
			return new Error("Ce praticien n'existe pas !");
		else {
			$req = $this->bdd->prepare("INSERT INTO favorite_doctor (id_member, id_doctor) 
			VALUES (:id_member, :id_doctor)");
			$req->bindValue(':id_member', $idMember, PDO::PARAM_INT);
			$req->bindValue(':id_doctor', $idDoctor, PDO::PARAM_INT);
			$req->execute();
			
			return new Error("Le praticien a été ajouté à vos favoris", 'info');
		}
	}
	
	/** 
	* @brief	Retire un praticien des favoris d'un utilisateur
	* @param	int			$idMember	ID du membre qui retire le praticien
	* @param	int			$idDoctor	ID du praticien à retirer
	* @return	Error		Retourne une information
	*/
	public function removeDoctorFromFavorites($idMember, $idDoctor)
	{
		$req = $this->bdd->prepare("DELETE FROM favorite_doctor
		WHERE id_member = :id_member AND id_doctor = :id_doctor");
		$req->bindValue(':id_member', $idMember, PDO::PARAM_INT);
		$req->bindValue(':id_doctor', $idDoctor, PDO::PARAM_INT);
		$req->execute();
		
		//on l'enlève le généraliste par défaut si c'est le même
		$req = $this->bdd->prepare("UPDATE member SET id_generalist = null
		WHERE id_member = :id_member AND id_generalist = :id_doctor");
		$req->bindValue(':id_member', $idMember, PDO::PARAM_INT);
		$req->bindValue(':id_doctor', $idDoctor, PDO::PARAM_INT);
		$req->execute();
		
		return new Error("Le praticien a été retiré de vos favoris", 'info');
	}
	
	/** 
	* @brief	Récupère les favoris d'un utilisateur
	* @param	int			$idMember	ID du membre
	* @return	Array		Liste des praticiens favoris de l'utilisateur sous forme de tableau
	*/
	public function getFavoritesDoc($idMember)
	{	
		$req = $this->bdd->prepare('SELECT key_name, value, medicine_name, favorite_doctor.id_doctor as id_member FROM favorite_doctor 
			LEFT JOIN doctor ON favorite_doctor.id_doctor = doctor.id_member 
			LEFT JOIN medicine ON doctor.id_medicine = medicine.id_medicine 
			LEFT JOIN profile ON profile.id_member = favorite_doctor.id_doctor 
			LEFT JOIN profile_key ON profile_key.id_key = profile.id_key
			WHERE favorite_doctor.id_member = :id_member');
		$req->bindParam(':id_member', $idMember, PDO::PARAM_INT);
		$req->execute();
		
		$result = array();
		while($rep = $req->fetch())
		{
			$result[$rep['id_member']][$rep['key_name']] = $rep['value'];
			$result[$rep['id_member']]['medicine_name'] = $rep['medicine_name'];
		}
		return $result;
	}
	
	/** 
	* @brief	Vérifie si un praticien est dans les favoris d'un utilisateur
	* @param	int			$idMember	ID du membre
	* @param	int			$idDoctor	ID du praticien
	* @return	boolean		@b TRUE si le praticien appartient aux favoris
	*/
	public function checkFavoritesDoc($idMember, $idDoctor)
	{
		$req = $this->bdd->prepare("SELECT * FROM favorite_doctor
		WHERE id_member = :id_member AND id_doctor = :id_doctor");
		$req->bindValue(':id_doctor', $idDoctor, PDO::PARAM_INT);
		$req->bindValue(':id_member', $idMember, PDO::PARAM_INT);
		$req->execute();
		
		$rep = $req->fetch();
		if(isset($rep['id_doctor']))
			return true;
		return false;
	}
	
	/** 
	* @brief	Enregistre une demande d'un utilisateur pour devenir praticien
	* @param	int		$idMember		ID du membre
	* @param	int		$idMedicine		ID de la médecine pratiquée par le membre
	* @return	Void
	*/
	public function askToBecomePratician($idMember, $idMedicine)
	{
		$req = $this->bdd->prepare("INSERT INTO waiting_doctor(id_member, id_medicine, date_waiting)
		VALUES (:id_member, :id_medicine, CURRENT_DATE)");
		$req->bindValue(':id_member', $idMember, PDO::PARAM_INT);
		$req->bindValue(':id_medicine', $idMedicine, PDO::PARAM_INT);
		$req->execute();
	}
	
	/** 
	* @brief	Compte les utilisateurs inscrits
	* @return	int		Le nombre d'utilisateurs
	* @note		Cette fonction compte également les praticiens car il sont aussi des membres.
	*/
	public function countMembers()
	{
		$req = $this->bdd->query('SELECT COUNT(id_member) as nbr FROM member');
		$rep = $req->fetch();
		return $rep['nbr'];
	}
	
	/** 
	* @brief	Détermine si un utilisateur est connecté
	* @return	boolean		@b TRUE si l'utilisateur est connecté
	*/
	public function isConnected(){
		if(isset($_SESSION['user']) AND Tools::getParentClass($_SESSION['user']) == "User" AND $_SESSION['user']->getIdMember() > 0)
			return true;
		return false;
	}
}