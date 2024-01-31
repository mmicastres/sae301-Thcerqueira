<?php
/**
*/
class CommentaireManager {
    
	private $_db; // Instance de PDO - objet de connexion au SGBD
        
	/**
	* Constructeur = initialisation de la connexion vers le SGBD
	*/
	public function __construct($db) {
		$this->_db=$db;
	}

    /**
	* retourne l'ensemble des commentaires pour un projet
	* @param int id_utilisateur
	* @return Commentaires[]
	*/
	public function getListCommentaire($projetId) {
		$comms = array();
		$req = "SELECT commentaire.id_commentaire, commentaire.commentaire, commentaire.note, commentaire.date, commentaire.id_projet, commentaire.id_utilisateur, utilisateur.nom, utilisateur.prenom
        FROM commentaire
        JOIN utilisateur ON commentaire.id_utilisateur = utilisateur.id_utilisateur
        WHERE commentaire.id_projet = ?
		ORDER BY commentaire.date DESC";
		$stmt = $this->_db->prepare($req);
		$stmt->execute([$projetId]);
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}

        while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $donnees = array_map('utf8_encode', $donnees);
            $commentaire = new Commentaire($donnees);
            $comms[] = $commentaire;
        }
		return $comms;
	}


	/**
	* ajout d'un commentaire pour un projet
	* @param commentaire à ajouter
	* @return int true si l'ajout a bien eu lieu, false sinon
	*/
	public function add(Commentaire $comm) {
		// calcul d'un nouveau code de commentaire non déja utilisé = Maximum + 1
		$stmt = $this->_db->prepare("SELECT max(id_commentaire) AS maximum FROM commentaire");
		$stmt->execute();
		$comm->setId_commentaire($stmt->fetchColumn()+1);
		
		// requete d'ajout dans la BD
		$req = "INSERT INTO commentaire (id_commentaire,commentaire,note,id_projet,id_utilisateur) VALUES (?,?,?,?,?)";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($comm->id_commentaire(), $comm->commentaire(), $comm->note(), $comm->id_projet(), $comm->id_utilisateur()));

		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}
}
	
?>