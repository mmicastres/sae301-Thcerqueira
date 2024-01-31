<?php
/**
* définition de la classe tag
*/
class Tag {
	private int $_id_projet;
	private int $_id_tag;    
	

	// contructeur
	public function __construct(array $donnees) {
	// initialisation d'un produit à partir d'un tableau de données
		if (isset($donnees['id_projet']))       { $this->_id_projet =       $donnees['id_projet']; }
		if (isset($donnees['id_tag']))       { $this->_id_tag =       $donnees['id_tag']; }
	}           
	// GETTERS //
	public function idProjet()       { return $this->_id_projet;}
	public function id_tag()       { return $this->_id_tag;}
		
	// SETTERS //
	public function setIdProjet(int $id_projet)             { $this->_id_projet = $id_projet; }
	public function setId_tag(int $id_tag)             { $this->_id_tag = $id_tag; }


}