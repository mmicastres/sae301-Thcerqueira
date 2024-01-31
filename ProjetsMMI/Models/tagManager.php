<?php
/**
*/
class TagManager {
    
	private $_db; // Instance de PDO - objet de connexion au SGBD
        
	/**
	* Constructeur = initialisation de la connexion vers le SGBD
	*/
	public function __construct($db) {
		$this->_db=$db;
	}

    public function add(Tag $tag) {
		$req = "INSERT INTO Classe (id_projet,id_tag) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($tag->idProjet(), $tag->id_tag()));
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}


		
		return $res;
	}

	public function modSupprTag($id_projet) {
		$req = "DELETE FROM Classe WHERE id_projet = ?";
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