<?php
/**
*/
class ContributeurManager {
    
	private $_db; // Instance de PDO - objet de connexion au SGBD
        
	/**
	* Constructeur = initialisation de la connexion vers le SGBD
	*/
	public function __construct($db) {
		$this->_db=$db;
	}

    public function add(Contributeur $con) {

		$req = "INSERT INTO Cree (id_projet,id_utilisateur) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($con->idProjet(), $con->id_utilisateur()));
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}


		
		return $res;
	}

	public function modSupprContributeur($id_projet) {
		$req = "DELETE FROM Cree WHERE id_projet = ?";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($id_projet));
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}

		return $res;
	}
        		
	



}
	
?>