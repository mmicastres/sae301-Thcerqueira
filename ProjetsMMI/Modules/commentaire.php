<?php
/**
* définition de la classe commentaire
*/
class Commentaire {
    private int $_id_commentaire;
	private int $_id_projet;
	private int $_id_utilisateur;
    private int $_note;
    private string $_commentaire;
    private string $_nom;
    private string $_prenom;
    private string $_date;
	

	// contructeur
	public function __construct(array $donnees) {
	// initialisation d'un commentaire à partir d'un tableau de données
		if (isset($donnees['id_commentaire']))       { $this->_id_commentaire =       $donnees['id_commentaire']; }
        if (isset($donnees['id_projet']))       { $this->_id_projet =       $donnees['id_projet']; }
        if (isset($donnees['id_utilisateur']))       { $this->_id_utilisateur =       $donnees['id_utilisateur']; }
        if (isset($donnees['commentaire']))       { $this->_commentaire =       $donnees['commentaire']; }
        if (isset($donnees['nom']))       { $this->_nom =       $donnees['nom']; }
        if (isset($donnees['prenom']))       { $this->_prenom =       $donnees['prenom']; }
        if (isset($donnees['note']))       { $this->_note =       $donnees['note']; }
        if (isset($donnees['date']))       { $this->_date =       $donnees['date']; }
	}           
	// GETTERS //
	public function id_commentaire()       { return $this->_id_commentaire;}
    public function id_projet()       { return $this->_id_projet;}
    public function id_utilisateur()       { return $this->_id_utilisateur;}
    public function commentaire()       { return $this->_commentaire;}
    public function nom()       { return $this->_nom;}
    public function prenom()       { return $this->_prenom;}
    public function note()       { return $this->_note;}
    public function date()       { return $this->_date;}
		
	// SETTERS //
	public function setId_commentaire(int $id_commentaire)             { $this->_id_commentaire = $id_commentaire; }
    public function setId_projet(int $id_projet)             { $this->_id_projet = $id_projet; }
    public function setId_utilisateur(int $id_utilisateur)             { $this->_id_utilisateur = $id_utilisateur; }
    public function setCommentaire(string $commentaire)             { $this->_commentaire = $commentaire; }
    public function setNote(int $note)             { $this->_note = $note; }
    public function setNom(string $nom)             { $this->_nom= $nom; }
    public function setPrenom(string $prenom)             { $this->_prenom = $prenom; }
}