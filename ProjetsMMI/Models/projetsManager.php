<?php
/**
*/
class ProjetManager {
    
	private $_db; // Instance de PDO - objet de connexion au SGBD
        
	/**
	* Constructeur = initialisation de la connexion vers le SGBD
	*/
	public function __construct($db) {
		$this->_db=$db;
	}
        		
		
	/**
	* @return Projet[]
	*/
	public function getList() {
		$pros = array();
		$req = "SELECT 
            projet.id_projet, 
            projet.titre, 
            projet.description, 
            projet.lien_demo, 
            projet.image,
            contexte.matiere, 
            contexte.semestre, 
            contexte.identifiant,
            GROUP_CONCAT(tag.tag SEPARATOR ' / ') AS tag
        FROM projet
        LEFT JOIN contexte ON projet.id_contexte = contexte.id_contexte
        LEFT JOIN Classe ON projet.id_projet = Classe.id_projet
        LEFT JOIN tag ON Classe.id_tag = tag.id_tag
        GROUP BY projet.id_projet";


		$stmt = $this->_db->prepare($req);
		$stmt->execute();
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// recupération des données en ne convertissant pas l'image en utf8
		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($donnees !== null) {
				$encoded = array_map(function ($value) use ($donnees) {
					return ($value !== $donnees['image']) ? utf8_encode($value) : $value;
				}, $donnees);
		
				$projet = new Projet($encoded);
				$projet->setImage(base64_encode($donnees['image']));
				$pros[] = $projet;
			}
		}
		return $pros;
	}

	/**
	* retourne l'ensemble des projets pour un utilisateur
	* @param int id_utilisateur
	* @return Projets[]
	*/
	public function getListUtilisateur(int $id_utilisateur) {
		$pros = array();
		$req = "SELECT projet.id_projet, projet.titre, projet.description, projet.lien_demo, projet.image,
            contexte.matiere, contexte.semestre, contexte.identifiant,
            GROUP_CONCAT(tag.tag SEPARATOR ' / ') AS tag
			FROM projet
			LEFT JOIN contexte ON projet.id_contexte = contexte.id_contexte
			LEFT JOIN Classe ON projet.id_projet = Classe.id_projet
			LEFT JOIN tag ON Classe.id_tag = tag.id_tag
			WHERE projet.id_utilisateur = ?
			GROUP BY projet.id_projet";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($id_utilisateur));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// recupération des données en ne convertissant pas l'image en utf8
		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($donnees !== null) {
				$encoded = array_map(function ($value) use ($donnees) {
					return ($value !== $donnees['image']) ? utf8_encode($value) : $value;
				}, $donnees);
		
				$projet = new Projet($encoded);
				$projet->setImage(base64_encode($donnees['image']));
				$pros[] = $projet;
			}
		}
		return $pros;
	}


	public function get(int $projetId) : Projet {	
		$req = 'SELECT 
		projet.id_projet, 
		projet.titre, 
		projet.description, 
		projet.lien_source, 
		projet.lien_demo, 
		projet.image,
		utilisateur.prenom, 
		utilisateur.nom,
		contexte.id_contexte, 
		contexte.matiere, 
		contexte.identifiant, 
		contexte.semestre,
		categorie.id_categorie, 
		categorie.categorie
		FROM projet
		JOIN utilisateur ON projet.id_utilisateur = utilisateur.id_utilisateur
		JOIN contexte ON projet.id_contexte = contexte.id_contexte
		JOIN categorie ON projet.id_categorie = categorie.id_categorie
		LEFT JOIN Cree ON projet.id_projet = Cree.id_projet
		LEFT JOIN utilisateur AS utilisateur_contributeur ON Cree.id_utilisateur = utilisateur_contributeur.id_utilisateur
		WHERE projet.id_projet = ?';
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($projetId));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}

		$pro = new Projet($stmt->fetch());
		return $pro;
	}	


	public function getProjetDetails($projetId) {
		$details = array();
		$req = "SELECT 
		projet.id_projet, 
		projet.titre, 
		projet.description, 
		projet.lien_source, 
		projet.lien_demo, 
		projet.image,
		utilisateur.prenom, 
		utilisateur.nom,
		contexte.matiere, 
		contexte.identifiant, 
		contexte.semestre,
		categorie.categorie,
		GROUP_CONCAT(utilisateur_contributeur.prenom, '   ', utilisateur_contributeur.nom) AS contributeur
		FROM projet
		JOIN utilisateur ON projet.id_utilisateur = utilisateur.id_utilisateur
		JOIN contexte ON projet.id_contexte = contexte.id_contexte
		JOIN categorie ON projet.id_categorie = categorie.id_categorie
		LEFT JOIN Cree ON projet.id_projet = Cree.id_projet
		LEFT JOIN utilisateur AS utilisateur_contributeur ON Cree.id_utilisateur = utilisateur_contributeur.id_utilisateur
		WHERE projet.id_projet = ?";
		// requête réalisé avec l'aide de ChatGPT
		$stmt = $this->_db->prepare($req);
		$stmt->execute([$projetId]);

		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// recupération des données en ne convertissant pas l'image en utf8
		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($donnees !== null) {
				$encoded = array_map(function ($value) use ($donnees) {
					return ($value !== $donnees['image']) ? utf8_encode($value) : $value;
				}, $donnees);
		
				$projet = new Projet($encoded);
				$projet->setImage(base64_encode($donnees['image']));
				$details[] = $projet;
			}
		}
    	return $details;
	}



	/**
	* 
	* @param string $titre
	* @param string
	* @param string
	* @return Projet[]
	*/
	public function search(string $titre) {
		$projets = array();
		$req = "SELECT projet.id_projet, projet.titre, projet.image,
            contexte.matiere, contexte.semestre, contexte.identifiant
        FROM projet
        LEFT JOIN contexte ON projet.id_contexte = contexte.id_contexte";
		$cond = '';
	
		if ($titre <> "") {
			$cond = $cond . " titre like '%" . $titre . "%'";
		}
		if ($cond <> "") {
			$req .= " WHERE " . $cond;
		}
	
		$stmt = $this->_db->prepare($req);
		$stmt->execute();
	
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// recupération des données en ne convertissant pas l'image en utf8
		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($donnees !== null) {
				$encoded = array_map(function ($value) use ($donnees) {
					return ($value !== $donnees['image']) ? utf8_encode($value) : $value;
				}, $donnees);
		
				$projet = new Projet($encoded);
				$projet->setImage(base64_encode($donnees['image']));
				$projets[] = $projet;
			}
		}
	
		return $projets;
	}
	
	
	// CONTEXTE

	public function getContexte() {
		$contexteList = array();
		$req = "SELECT id_contexte, identifiant, matiere FROM contexte";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();

		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}

		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {
		
				$projet = new Projet($donnees);
				$projet->setId_contexteList(utf8_encode($donnees['id_contexte']));
    			$projet->setIdentifiantList(utf8_encode($donnees['identifiant']));
    			$projet->setMatiereList(utf8_encode($donnees['matiere']));
				$contexteList[] = $projet;
			}
		return $contexteList;
	}

	public function getContexteId($id_contexte) {
		$cont = array();
		$req = "SELECT id_contexte, semestre, matiere, identifiant FROM contexte WHERE id_contexte = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($id_contexte));
	
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
	
		$cont = $stmt->fetch(PDO::FETCH_ASSOC);
		return $cont;
	}

	public function getCategorie() {
		$categorieList = array();
		$req = "SELECT id_categorie, categorie FROM categorie";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();

		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}

		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {	
				$projet = new Projet($donnees);
				$projet->setId_categorieList(utf8_encode($donnees['id_categorie']));
    			$projet->setCategorieList(utf8_encode($donnees['categorie']));
				$categorieList[] = $projet;
			}
		return $categorieList;
	}

	public function getCategorieId($id_categorie) {
		$cat = array();
		$req = "SELECT id_categorie, categorie FROM categorie WHERE id_categorie = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($id_categorie));
	
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
	
		$cat = $stmt->fetch(PDO::FETCH_ASSOC);
		return $cat;
	}
	

	public function getContributeur() {
		$contributeurList = array();
		$req = "SELECT id_utilisateur, nom, prenom FROM utilisateur;";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();

		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}

		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {	
				$projet = new Projet($donnees);
				$projet->setId_utilisateur(utf8_encode($donnees['id_utilisateur']));
				$projet->setNom(utf8_encode($donnees['nom']));
    			$projet->setPrenom(utf8_encode($donnees['prenom']));
				$contributeurList[] = $projet;
		}
		return $contributeurList;
	}

	public function getTag() {
		$tagList = array();
		$req = "SELECT id_tag, tag FROM tag;";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();

		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		
		while ($donnees = $stmt->fetch(PDO::FETCH_ASSOC)) {	
				$projet = new Projet($donnees);
				$projet->setId_tag(utf8_encode($donnees['id_tag']));
				$projet->setTag(utf8_encode($donnees['tag']));
				$tagList[] = $projet;
			}
		return $tagList;
	}

	

	/**
	* ajout d'un projet dans la BD
	* @param projet à ajouter
	* @return int true si l'ajout a bien eu lieu, false sinon
	*/
	public function add(Projet $pro) {
		$stmt = $this->_db->prepare("SELECT max(id_projet) AS maximum FROM projet");
		$stmt->execute();
		$idProjet = $stmt->fetchColumn()+1;
		$pro->setIdProjet($idProjet);
		
		// requete d'ajout dans la BD
		$req = "INSERT INTO projet (id_projet,id_utilisateur,titre, description, image, lien_demo,lien_source,id_contexte, id_categorie, validation) VALUES (?,?,?,?,?,?,?,?,?,?)";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($pro->idProjet(), $pro->id_utilisateur(), $pro->titre(), $pro->description(), $pro->image(), $pro->lien_demo(), $pro->lien_source(), $pro->id_contexteList(), $pro->id_categorieList(), $pro->validation()));
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}


		
		return $idProjet;
	}


	public function update(Projet $pro): bool {
		$req = "UPDATE projet SET titre = :titre, "
			. "description = :description, "
			. "lien_demo  = :lien_demo, "
			. "lien_source = :lien_source, "
			. "id_contexte = :id_contexte, "
			. "id_categorie = :id_categorie ";
	
		if ($pro->image() !== null) {
			$req .= ", image = :image ";
		}
	
		$req .= "WHERE id_projet = :id_projet";
	
		$stmt = $this->_db->prepare($req);
	
		$params = array(
			":titre" => $pro->titre(),
			":description" => $pro->description(),
			":lien_demo" => $pro->lien_demo(),
			":lien_source" => $pro->lien_source(),
			":id_contexte" => $pro->id_contexte(),
			":id_categorie" => $pro->id_categorie(),
			":id_projet" => $pro->idProjet()
		);

		if ($pro->image() !== null) {
			$params[":image"] = $pro->image();
		}
	
		$stmt->execute($params);
		return $stmt->rowCount();
	}
	

	/**
	* suppression d'un projet dans la base de données
	* @param Projet 
	* @return boolean true si suppression, false sinon
	*/
	public function delete(Projet $pro) : bool {
		$req = "DELETE FROM projet WHERE id_projet = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($pro->idProjet()));
	}

	/**
	* ajout d'une contexte dans la BD
	* @param projet à ajouter
	* @return int true si l'ajout a bien eu lieu, false sinon
	*/
	public function addContexte(Projet $cont) {
		$stmt = $this->_db->prepare("SELECT max(id_contexte) AS maximum FROM contexte");
		$stmt->execute();
		$cont->setId_contexte($stmt->fetchColumn()+1);
		
		// requete d'ajout dans la BD
		$req = "INSERT INTO contexte (id_contexte, semestre, identifiant, matiere) VALUES (?,?,?,?)";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($cont->id_contexte(), $cont->semestre(), $cont->identifiant(), $cont->matiere()));		
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	public function updateContexte(Projet $cont) : bool {
		$req = "UPDATE contexte SET semestre = :semestre, matiere = :matiere, identifiant = :identifiant WHERE id_contexte = :id_contexte";

	
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array(
			":semestre" => $cont->semestre(),
			":matiere" => $cont->matiere(),
			":identifiant" => $cont->identifiant(),
			":id_contexte" => $cont->id_contexte()
		));
		// Retourner true si la mise à jour a réussi, sinon false
		return $stmt->rowCount() > 0;
	}

	/**
	* update contexte dans les projets
	* @param Projet 
	* @return boolean true si suppression, false sinon
	*/
	public function updateContexteProjet(Projet $contexte) : bool {
		$req = "UPDATE projet SET id_contexte = 0 WHERE id_contexte = ?;";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($contexte->id_contexte()));
	}

	/**
	* suppression d'un contexte dans la base de données
	* @param Projet 
	* @return boolean true si suppression, false sinon
	*/
	public function deleteContexte(Projet $contexte) : bool {
		$req = "DELETE FROM contexte WHERE id_contexte = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($contexte->id_contexte()));
	}

	/**
	* ajout d'une categorie dans la BD
	* @param projet à ajouter
	* @return int true si l'ajout a bien eu lieu, false sinon
	*/
	public function addCategorie(Projet $cat) {
		$stmt = $this->_db->prepare("SELECT max(id_categorie) AS maximum FROM categorie");
		$stmt->execute();
		$cat->setId_categorie($stmt->fetchColumn()+1);
		
		// requete d'ajout dans la BD
		$req = "INSERT INTO categorie (id_categorie,categorie) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($cat->id_categorie(), $cat->categorie()));		
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	public function updateCategorie(Projet $cat) : bool {
		$req = "UPDATE categorie SET categorie = :categorie WHERE id_categorie = :id_categorie";
	
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array(":categorie" => $cat->categorie(),
							 ":id_categorie" => $cat->id_categorie()));
	
		// Retourner true si la mise à jour a réussi, sinon false
		return $stmt->rowCount() > 0;
	}




	/**
	* update categorie dans les projets
	* @param Projet 
	* @return boolean true si suppression, false sinon
	*/
	public function updateCategorieProjet(Projet $categorie) : bool {
		$req = "UPDATE projet SET id_categorie = 0 WHERE id_categorie = ?;";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($categorie->id_categorie()));
	}

	/**
	* suppression d'un categorie dans la base de données
	* @param Projet 
	* @return boolean true si suppression, false sinon
	*/
	public function deleteCategorie(Projet $categorie) : bool {
		$req = "DELETE FROM categorie WHERE id_categorie = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($categorie->id_categorie()));
	}



}
	
?>