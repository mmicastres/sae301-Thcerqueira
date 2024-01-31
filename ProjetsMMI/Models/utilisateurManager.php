<?php

/**
* Définition d'une classe permettant de gérer les utilisateurs
* en relation avec la base de données
*
*/

class UtilisateurManager
    {
        private $_db; // Instance de PDO - objet de connexion au SGBD
        
		/** 
		* Constructeur = initialisation de la connexion vers le SGBD
		*/
        public function __construct($db) {
            $this->_db=$db;
        }
		
		/**
		* verification de l'identité d'un utilisateur (Login/password)
		* @param string $login
		* @param string $password
		* @return utilisateur si authentification ok, false sinon
		*/
		public function verif_identification($login, $password) {
		//echo $login." : ".$password;
			$req = "SELECT id_utilisateur, nom, prenom FROM utilisateur WHERE email=:login and password=:password ";
			$stmt = $this->_db->prepare($req);
			$stmt->execute(array(":login" => $login, ":password" => $password));
			if ($data=$stmt->fetch()) { 
				$utilisateur = new Utilisateur($data);
				return $utilisateur;
				}
			else return false;
		}

		public function verif_administrateur($id_utilisateur) {
				$req = "SELECT admin FROM utilisateur WHERE id_utilisateur = ?";
				$stmt = $this->_db->prepare($req);
				$stmt->execute(array($id_utilisateur));
				$data =$stmt->fetch();
				$admin = new Utilisateur($data);
			return $admin;
		}


	/**
	* informations personelles 
	* @param int id_utilisateur
	* @return Utilisateur[]
	*/
	public function getUtilisateurInfoPerso(int $id_utilisateur) {
		$utis = array();
		$req = "SELECT nom, prenom, identifiant_iut, email FROM utilisateur WHERE id_utilisateur = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($id_utilisateur));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}

		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$utilisateur = new Utilisateur($donnees);
			$utis[] = $utilisateur;
		}
		return $utis;
	}

	/**
	* ajout d'un utilisateur dans la BD
	* @param utilisateur à ajouter
	* @return int true si l'ajout a bien eu lieu, false sinon
	*/
	public function addUtilisateur(Utilisateur $utilisateur) {
		$stmt = $this->_db->prepare("SELECT max(id_utilisateur) AS maximum FROM utilisateur");
		$stmt->execute();
		$utilisateur->setId_utilisateur($stmt->fetchColumn()+1);
			
		// requete d'ajout dans la BD
		$req = 'INSERT INTO utilisateur (`id_utilisateur`, `nom`, `prenom`, `email`, `password`, `identifiant_iut`, `admin`) VALUES (?,?,?,?,?,?,?)';
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($utilisateur->id_utilisateur(), $utilisateur->nom(), $utilisateur->prenom(), $utilisateur->email(), $utilisateur->password(), $utilisateur->identifiant_iut(), $utilisateur->admin()));		
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	/**
	* liste utilisateur
	* @param rien
	* @return int true si l'ajout a bien eu lieu, false sinon
	*/
	public function getUtilisateur() {
		$utis = array();
		// requete d'ajout dans la BD
		$req = 'SELECT id_utilisateur, nom, prenom FROM utilisateur';
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute();		
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}

		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$utilisateur = new Utilisateur($donnees);
			$utis[] = $utilisateur;
		}
		return $utis;
	}
}
?>