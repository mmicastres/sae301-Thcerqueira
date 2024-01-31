<?php
/**
* définition de la classe projet
*/
class Projet {
	private int $_id_projet;
	private int $_id_utilisateur;    
	private string $_titre;
	private string $_description;
	private ?string $_image = null;
	private ?string $_imageType;
	private string $categorie;
	private string $_lien_demo;
	private string $_lien_source;
	private string $_semestre;
	private string $_identifiant;
    private string $_matiere;
	private string $_prenom;
    private string $_nom;
	private int $_validation;
	private int $_id_contexte;
	private int $_id_categorie;

	private int $_id_contexteList;
	private string $_identifiantList;
	private string $_matiereList;

	private int $_id_categorieList;
	private string $_categorieList;

	private int $_id_tag;
	private string $_tagList;
	private string $_tag = "";

	private string $_contributeur;


		
	// contructeur
	public function __construct(array $donnees) {
	// initialisation d'un produit à partir d'un tableau de données
		if (isset($donnees['id_projet']))       { $this->_id_projet =       $donnees['id_projet']; }
		if (isset($donnees['id_utilisateur']))       { $this->_id_utilisateur =       $donnees['id_utilisateur']; }
		if (isset($donnees['titre']))    { $this->_titre =    $donnees['titre']; }
		if (isset($donnees['description']))  { $this->_description =  $donnees['description']; }
		if (isset($donnees['image'])) { $this->_image = $donnees['image']; }
		if (isset($donnees['imageType'])) { $this->_imageType = $donnees['imageType']; }
		if (isset($donnees['lien_demo']))       { $this->_lien_demo =       $donnees['lien_demo']; }
		if (isset($donnees['lien_source']))       { $this->_lien_source =       $donnees['lien_source']; }
		if (isset($donnees['semestre']))       { $this->_semestre =       $donnees['semestre']; }
		if (isset($donnees['matiere']))       { $this->_matiere =       $donnees['matiere']; }
		if (isset($donnees['prenom']))       { $this->_prenom =       $donnees['prenom']; }
		if (isset($donnees['nom']))       { $this->_nom =       $donnees['nom']; }
		if (isset($donnees['validation']))       { $this->_validation =       $donnees['validation']; }
		if (isset($donnees['identifiant']))       { $this->_identifiant =       $donnees['identifiant']; }
		if (isset($donnees['id_categorieList']))       { $this->_id_categorieList =       $donnees['id_categorieList']; }
		if (isset($donnees['id_contexteList']))       { $this->_id_contexteList =       $donnees['id_contexteList']; }
		if (isset($donnees['id_contexte']))       { $this->_id_contexte =       $donnees['id_contexte']; }
		if (isset($donnees['id_categorie']))       { $this->_id_categorie =       $donnees['id_categorie']; }
		if (isset($donnees['categorie']))       { $this->_categorie =       $donnees['categorie']; }
		if (isset($donnees['tag']))       { $this->_tag =       $donnees['tag']; }

		if (isset($donnees['contributeur']))       { $this->_contributeur =       $donnees['contributeur']; }
	}           
	// GETTERS //
	public function idProjet()       { return $this->_id_projet;}
	public function id_utilisateur()       { return $this->_id_utilisateur;}
	public function titre()    { return $this->_titre;}
	public function description()  { return $this->_description;}
	public function image() { return $this->_image;}
	public function imageType() { return $this->_imageType;}
	public function categorie() { return $this->_categorie;}
	public function lien_demo()       { return $this->_lien_demo;}
	public function lien_source()    { return $this->_lien_source;}
	public function semestre() { return $this->_semestre; }
    public function matiere() { return $this->_matiere; }
	public function prenom() { return $this->_prenom; }
	public function nom() { return $this->_nom; }
	public function validation() { return $this->_validation; }
	public function identifiant() { return $this->_identifiant; }

	public function categorieList() { return $this->_categorieList; }
	public function id_categorieList() { return $this->_id_categorieList; }

	public function identifiantList() { return $this->_identifiantList; }
	public function id_contexte() { return $this->_id_contexte; }
	public function id_contexteList() { return $this->_id_contexteList; }
	public function id_categorie() { return $this->_id_categorie; }
	public function matiereList() { return $this->_matiereList; }

	public function id_tag() { return $this->_id_tag; }
	public function tag() { return $this->_tag; }

	public function contributeur() { return $this->_contributeur; }
		
	// SETTERS //
	public function setIdProjet(int $id_projet)             { $this->_id_projet = $id_projet; }
	public function setId_utilisateur(int $id_utilisateur)             { $this->_id_utilisateur = $id_utilisateur; }
	public function setTitre(srting $titre)       { $this->_titre = $titre; }
	public function setDescription(string $description)   { $this->_description= $description; }
	public function setImage(?string $image) { $this->_image = $image; }
	public function setImageType(string $imageType) { $this->_imageType = $imageType; }
	public function setCategorie(string $categorie) { $this->categorie = $categorie; }
	public function setLien_demo(string $lien_demo)             { $this->_lien_demo = $lien_demo; }
	public function setLien_source(string $lien_source)       { $this->_lien_source = $lien_source; }
	public function setSemestre(string $semestre) { $this->_semestre = $semestre; }
    public function setMatiere(string $matiere) { $this->_matiere = $matiere; }
	public function setPrenom(string $prenom) { $this->_prenom = $prenom; }
	public function setNom(string $nom) { $this->_nom = $nom; }
	public function setValidation(int $validation) { $this->_validation = $validation; }
	public function setIdentifiant(string $identifiant) { $this->_identifiant = $identifiant; }

	public function setId_categorieList(int $id_categorieList) { $this->_id_categorieList = $id_categorieList; }
	public function setCategorieList(string $categorieList) { $this->_categorieList = $categorieList; }

	public function setId_contexteList(int $id_contexteList) { $this->_id_contexteList = $id_contexteList; }
	public function setId_contexte(int $id_contexte) { $this->_id_contexte = $id_contexte; }
	public function setIdentifiantList(string $identifiantList) { $this->_identifiantList = $identifiantList; }
	public function setId_categorie(int $id_categorie) { $this->_id_categorie = $id_categorie; }
	public function setMatiereList(string $matiereList) { $this->_matiereList = $matiereList; }

	public function setId_tag(int $id_tag) { $this->_id_tag = $id_tag; }
	public function setTag(string $tag) { $this->_tag = $tag; }

	public function setContributeur(string $contributeur) { $this->_contributeur = $contributeur; }
}

