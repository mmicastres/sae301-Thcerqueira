<?php
/** 
* définition de la classe utilisateur
*/
class Utilisateur {
        private int $_id_utilisateur;
        private string $_nom;
        private string $_prenom;
		private string $_email;
		private string $_password;
		private string $_identifiant_iut;
		private int $_admin;
		
        // contructeur
        public function __construct(array $donnees) {
		// initialisation d'un produit à partir d'un tableau de données
			if (isset($donnees['id_utilisateur'])) { $this->_id_utilisateur = $donnees['id_utilisateur']; }
			if (isset($donnees['nom'])) { $this->_nom = $donnees['nom']; }
			if (isset($donnees['prenom'])) { $this->_prenom = $donnees['prenom']; }
			if (isset($donnees['email'])) { $this->_email = $donnees['email']; }
			if (isset($donnees['password'])) { $this->_password = $donnees['password']; }
			if (isset($donnees['identifiant_iut'])) { $this->_identifiant_iut = $donnees['identifiant_iut']; }
			if (isset($donnees['admin'])) { $this->_admin = $donnees['admin']; }
        }           
        // GETTERS //
		public function id_utilisateur() { return $this->_id_utilisateur;}
		public function nom() { return $this->_nom;}
		public function prenom() { return $this->_prenom;}
		public function email() { return $this->_email;}
		public function password() { return $this->_password;}
		public function identifiant_iut() { return $this->_identifiant_iut;}
		public function admin() { return $this->_admin;}
		
		// SETTERS //
		public function setId_utilisateur(int $id_utilisateur) { $this->_id_utilisateur = $id_utilisateur; }
        public function setNom(string $nom) { $this->_nom= $nom; }
		public function setPrenom(string $prenom) { $this->_prenom = $prenom; }
		public function setEmail(string $email) { $this->_email = $email; }
		public function setPassword(string $password) { $this->_password = $password; }
		public function setIdentifiant_iut(string $identifiant_iut) { $this->_identifiant_iut = $identifiant_iut; }
		public function setAdmin(int $admin) { $this->_admin = $admin; }		

    }

?>