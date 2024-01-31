<?php
/**
* définition de la classe contributeur
*/
class Contributeur {
	private int $_id_projet;
	private int $_id_utilisateur;    
	

	// contructeur
	public function __construct(array $donnees) {
	// initialisation d'un produit à partir d'un tableau de données
		if (isset($donnees['id_projet']))       { $this->_id_projet =       $donnees['id_projet']; }
		if (isset($donnees['id_utilisateur']))       { $this->_id_utilisateur =       $donnees['id_utilisateur']; }
	}           
	// GETTERS //
	public function idProjet()       { return $this->_id_projet;}
	public function id_utilisateur()       { return $this->_id_utilisateur;}
		
	// SETTERS //
	public function setIdProjet(int $id_projet)             { $this->_id_projet = $id_projet; }
	public function setId_utilisateur(int $id_utilisateur)             { $this->_id_utilisateur = $id_utilisateur; }


}

