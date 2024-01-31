<?php
include "Modules/projets.php";
include "Models/projetsManager.php";

include "Modules/contributeur.php";
include "Models/contributeurManager.php";

include "Modules/tag.php";
include "Models/tagManager.php";

include "Modules/commentaire.php";
include "Models/commentaireManager.php";

/**
*   en relation avec la base de données	
*/
class ProjetController {
    
	private $tagManager;
	private $conManager;
	private $commManager;
	private $proManager; // instance du manager
	private $twig;
        
	/**
	* Constructeur = initialisation de la connexion vers le SGBD
	*/
	public function __construct($db, $twig) {
		$this->proManager = new ProjetManager($db);
		$this->conManager = new ContributeurManager($db);
		$this->tagManager = new TagManager($db);
		$this->commManager = new CommentaireManager($db);
		$this->twig = $twig;
	}
        
	/**
	* liste de tous les projets
	* @param aucun
	* @return rien
	*/
	public function listeProjets() {
		$projets = $this->proManager->getList();
		echo $this->twig->render('projet_liste.html.twig', ['pros' => $projets, 'acces' => $_SESSION['acces'], 'admin'=> $_SESSION['admin']]);
	}

	/**
	* liste de mes projets
	* @param aucun
	* @return rien
	*/
	public function listeMesProjets($id_utilisateur) {
		$projets = $this->proManager->getListUtilisateur($id_utilisateur);
		echo $this->twig->render('projet_liste.html.twig',array('pros'=>$projets,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
	}



	public function detailsProjet($projetId) {
		if ($projetId) {
			$projetDetails = $this->proManager->getProjetDetails($projetId);
			$projetCommentaires = $this->commManager->getListCommentaire($projetId);

			$id_utilisateur = $_SESSION['acces'] === 'oui' ? $_SESSION['id_utilisateur'] : null;

			echo $this->twig->render('projet_details.html.twig', ['projetDetails' => $projetDetails, 'projetCommentaires' => $projetCommentaires, 'acces' => $_SESSION['acces'], 'id_utilisateur' => $id_utilisateur, 'admin'=> $_SESSION['admin']]);
		} else {
			echo "ID du projet non spécifié";
		}
	}


	/**
	* recherche dans la BD d'un projet à partir des données du form précédent
	* @param aucun
	* @return rien
	*/
	public function rechercheProjet() {
		$projets = $this->proManager->search($_POST["titre"]);
		echo $this->twig->render('projet_liste.html.twig',array('pros'=>$projets,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
	}


	/**
	* formulaire ajout
	* @param aucun
	* @return rien
	*/
	public function formAjoutProjet() {
		$contextes = $this->proManager->getContexte();
		$categories = $this->proManager->getCategorie();
		$contributeurs = $this->proManager->getContributeur();
		$tags = $this->proManager->getTag();
		
		echo $this->twig->render('projet_ajout.html.twig', array('contexteList' => $contextes, 'categorieList' => $categories, 'contributeurList' => $contributeurs, 'tagList' => $tags,'acces' => $_SESSION['acces'], 'admin'=> $_SESSION['admin'], 'id_utilisateur' => $_SESSION['id_utilisateur'])); 
	}

	/**
	* ajout dans la BD d'un projet à partir du form
	* @param aucun
	* @return rien
	*/
	public function ajoutProjet() {
		$_POST['image']= file_get_contents($_FILES['image']['tmp_name']);
		$pro = new Projet($_POST);
		$idProjet = $this->proManager->add($pro);

		if (isset($_POST["contributeurs"]) && !empty($_POST["contributeurs"])) {
			foreach ($_POST["contributeurs"] as $contributeur){
				
				$con = new Contributeur(['id_projet' => $idProjet, 'id_utilisateur' => $contributeur]);
				$ok = $this->conManager->add($con);
				
			}
		} else {
			unset($_POST["contributeurs"]);
		}

		if (isset($_POST["tags"]) && !empty($_POST["tags"])) {
			foreach ($_POST["tags"] as $tag){
				
				$tag = new Tag(['id_projet' => $idProjet, 'id_tag' => $tag]);
				$ok = $this->tagManager->add($tag);
				
			}
		} else {
			unset($_POST["tags"]);
		}

		$message = $ok ? "Projet ajouté" : "probleme lors de l'ajout";

		echo $this->twig->render('index.html.twig',array('message'=>$message, 'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 

	}

	/**
	* form de choix du projet à supprimer crée par l'utilisateur
	* @param aucun
	* @return rien
	*/
	public function choixSuppProjet($id_utilisateur) {
		$projets = $this->proManager->getListUtilisateur($id_utilisateur);
		echo $this->twig->render('projet_choix_suppression.html.twig',array('pros'=>$projets,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
	}

	/**
	* form de choix de n'importe quel projet à supprimer (admin)
	* @param aucun
	* @return rien
	*/
	public function choixSuppProjetAll() {
		if ($_SESSION['admin'] === 'oui'){
			$projets = $this->proManager->getList();
			echo $this->twig->render('projet_choix_suppression.html.twig',array('pros'=>$projets,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin']));
		} else {
			echo('Compte administrateur nécessaire');
		}
	}

	/**
	* suppression dans la BD d'un projet à partir de l'id choisi dans le form précédent
	* @param aucun
	* @return rien
	*/
	public function suppProjet() {
		$idProjet = $_POST['id_projet'];
        $pro = new Projet(['id_projet' => $idProjet]);
		$modSupprContributeur = $this->conManager->modSupprContributeur($idProjet);
		$modSupprTag = $this->tagManager->modSupprTag($idProjet);
		$ok = $this->proManager->delete($pro);
		$message = $ok ?  "projet supprimé" : "probleme lors de la supression";
		echo $this->twig->render('index.html.twig',array('message'=>$message,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
	}

	/**
	* ajouter un commentaire
	* @param aucun
	* @return rien
	*/
	public function ajoutCommentaire() {
		if ($_SESSION['acces'] === 'oui') {
			$_POST['id_utilisateur'] = $_POST['id_utilisateur'];
		
			$comm = new Commentaire($_POST);
			$ok = $this->commManager->add($comm);

			$message = $ok ? "Commentaire posté" : "probleme lors de l'ajout du commentaire";
		} else {
			$message = "Connectez vous pour poster un commentaire";
		}
		
		echo $this->twig->render('index.html.twig',array('message'=>$message, 'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin']));
	}


	/**
	* form de choix du projet crée par l'utilisateur à modifier
	* @param aucun
	* @return rien
	*/
	public function choixModProjet($id_utilisateur) {
		$projets = $this->proManager->getListUtilisateur($id_utilisateur);
		echo $this->twig->render('projet_choix_modifier.html.twig',array('pros'=>$projets,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
	}

	/**
	* form de choix de n'importe quel projet à modifier (admin)
	* @param aucun
	* @return rien
	*/
	public function choixModProjetAll() {
		if ($_SESSION['admin'] === 'oui'){
			$projets = $this->proManager->getList();
			echo $this->twig->render('projet_choix_modifier.html.twig',array('pros'=>$projets,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
		} else {
			echo('Compte administrateur nécessaire');
		}
	}

	/**
	* form de saisi des nouvelles valeurs du projet à modifier
	* @param aucun
	* @return rien
	*/
	public function saisieModProjet() {
		$projetId = $_POST["id_projet"];

		$contextes = $this->proManager->getContexte();
		$categories = $this->proManager->getCategorie();
		$contributeurs = $this->proManager->getContributeur();
		$tags = $this->proManager->getTag();
		$pro =  $this->proManager->get($projetId);

		echo $this->twig->render('projet_modification.html.twig',array('categorieList' => $categories, 'contexteList' => $contextes, 'contributeurList' => $contributeurs, 'tagList' => $tags, 'pro'=>$pro,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'], 'id_utilisateur' => $_SESSION['id_utilisateur'])); 
	}

	public function modProjet() {
		if (!empty($_FILES['image']['tmp_name'])) {
			$_POST['image'] = file_get_contents($_FILES['image']['tmp_name']);
		} else {
			unset($_POST['image']);
		}
		$pro =  new Projet($_POST);
		$idProjet = $_POST['id_projet'];
		$ok = $this->proManager->update($pro);

		if (isset($_POST["contributeurs"]) && !empty($_POST["contributeurs"])) {
			$modSupprContributeur = $this->conManager->modSupprContributeur($idProjet);
			foreach ($_POST["contributeurs"] as $contributeur){
				$con = new Contributeur(['id_projet' => $idProjet, 'id_utilisateur' => $contributeur]);
				$ok = $this->conManager->add($con);
			}	
		} else {
			unset($_POST["contributeurs"]);
		}

		if (isset($_POST["tags"]) && !empty($_POST["tags"])) {
			$modSupprTag = $this->tagManager->modSupprTag($idProjet);
			foreach ($_POST["tags"] as $tag){
				$tag = new Tag(['id_projet' => $idProjet, 'id_tag' => $tag]);
				$ok = $this->tagManager->add($tag);
			}
		} else {
			unset($_POST["tags"]);
		}

		$message = $ok ? "Projet modifié" : $message = "probleme lors de la modification";
		echo $this->twig->render('index.html.twig',array('message'=>$message,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
	}













	// contexte

	/**
	* formulaire ajout
	* @param aucun
	* @return rien
	*/
	public function formAjoutContexte() {
		if ($_SESSION['admin'] === 'oui'){
			echo $this->twig->render('contexte_ajout.html.twig', array('acces' => $_SESSION['acces'], 'admin'=> $_SESSION['admin']));
		} else {
			echo('Compte administrateur nécessaire');
		}
	}

	/**
	* ajout dans la BD d'une contexte à partir du form
	* @param aucun
	* @return rien
	*/
	public function ajoutContexte() {
		$cont = new Projet($_POST);
		$ok = $this->proManager->addContexte($cont);
		$message = $ok ? "Contexte ajouté" : "probleme lors de l'ajout";

		echo $this->twig->render('index.html.twig',array('message'=>$message, 'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 

	}

	public function choixModContexte() {
        if ($_SESSION['admin'] === 'oui') {
            $contextes = $this->proManager->getContexte();
            echo $this->twig->render('contexte_choix_modifier.html.twig', array('contexteList' => $contextes, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
        } else {
            echo('Compte administrateur nécessaire');
        }
    }

    /**
     * Affiche le formulaire de saisie des nouvelles valeurs de la contexte à modifier
     */
    public function saisieModContexte() {
        if (isset($_POST["id_contexte"])) {
            $id_contexte = $_POST["id_contexte"];
            $contexte = $this->proManager->getContexteId($id_contexte);
            echo $this->twig->render('contexte_modification.html.twig', array('cont' => $contexte, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'id_utilisateur' => $_SESSION['id_utilisateur']));
        } else {
            echo('Identifiant de catégorie non spécifié');
        }
    }

    /**
     * Met à jour la contexte dans la base de données
     */
    public function modContexte() {
        if (isset($_POST['id_contexte'])) {
            $id_contexte = $_POST['id_contexte'];
            $nouvelle_semestre = $_POST['semestre'];
			$nouvelle_matiere = $_POST['matiere'];
			$nouvelle_identifiant = $_POST['identifiant'];
            
            $cont = new Projet(['id_contexte' => $id_contexte, 'semestre' => $nouvelle_semestre, 'matiere' => $nouvelle_matiere, 'identifiant' => $nouvelle_identifiant]);
            $ok = $this->proManager->updateContexte($cont);

            $message = $ok ? "Contexte modifiée" : "Problème lors de la modification de la catégorie";
            echo $this->twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
        } else {
            echo('Identifiant de catégorie non spécifié');
        }
    }


	/**
	* form de choix pour supprimer un contexte (admin)
	* @param aucun
	* @return rien
	*/
	public function choixSuppContexte() {
		if ($_SESSION['admin'] === 'oui'){
			$contextes = $this->proManager->getContexte();
			echo $this->twig->render('contexte_choix_suppression.html.twig',array('contexteList' => $contextes,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin']));
		} else {
			echo('Compte administrateur nécessaire');
		}
	}

	/**
	* suppression dans la BD d'un contexte à partir du form précédent
	* @param aucun
	* @return rien
	*/
	public function suppContexte() {
		$id_contexte = $_POST['id_contexte'];
        $contexte = new Projet(['id_contexte' => $id_contexte]);
		$updateContexteProjet = $this->proManager->updateContexteProjet($contexte);
		$ok = $this->proManager->deleteContexte($contexte);
		$message = $ok ?  "contexte supprimé" : "probleme lors de la supression";
		echo $this->twig->render('index.html.twig',array('message'=>$message,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
	}













	// categorie

	/**
	* formulaire ajout
	* @param aucun
	* @return rien
	*/
	public function formAjoutCategorie() {
		if ($_SESSION['admin'] === 'oui'){
			echo $this->twig->render('categorie_ajout.html.twig', array('acces' => $_SESSION['acces'], 'admin'=> $_SESSION['admin']));
		} else {
			echo('Compte administrateur nécessaire');
		}
	}

	/**
	* ajout dans la BD d'une catégorie à partir du form
	* @param aucun
	* @return rien
	*/
	public function ajoutCategorie() {
		$cat = new Projet($_POST);
		$ok = $this->proManager->addCategorie($cat);
		$message = $ok ? "Catégorie ajouté" : "probleme lors de l'ajout";

		echo $this->twig->render('index.html.twig',array('message'=>$message, 'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin']));
	}


	public function choixModCategorie() {
        if ($_SESSION['admin'] === 'oui') {
            $categories = $this->proManager->getCategorie();
            echo $this->twig->render('categorie_choix_modifier.html.twig', array('categorieList' => $categories, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
        } else {
            echo('Compte administrateur nécessaire');
        }
    }

    /**
     * Affiche le formulaire de saisie des nouvelles valeurs de la catégorie à modifier
     */
    public function saisieModCategorie() {
        if (isset($_POST["id_categorie"])) {
            $id_categorie = $_POST["id_categorie"];
            $categorie = $this->proManager->getCategorieId($id_categorie);
            echo $this->twig->render('categorie_modification.html.twig', array('cat' => $categorie, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'id_utilisateur' => $_SESSION['id_utilisateur']));
        } else {
            echo('Identifiant de catégorie non spécifié');
        }
    }

    /**
     * Met à jour la catégorie dans la base de données
     */
    public function modCategorie() {
        if (isset($_POST['id_categorie'])) {
            $id_categorie = $_POST['id_categorie'];
            $nouvelle_categorie = $_POST['categorie'];
            
            $cat = new Projet(['id_categorie' => $id_categorie, 'categorie' => $nouvelle_categorie]);
            $ok = $this->proManager->updateCategorie($cat);

            $message = $ok ? "Catégorie modifiée" : "Problème lors de la modification de la catégorie";
            echo $this->twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
        } else {
            echo('Identifiant de catégorie non spécifié');
        }
    }

	/**
	* form de choix pour supprimer une categorie (admin)
	* @param aucun
	* @return rien
	*/
	public function choixSuppCategorie() {
		if ($_SESSION['admin'] === 'oui'){
			$categories = $this->proManager->getCategorieMod();
			echo $this->twig->render('categorie_choix_suppression.html.twig',array('categorieList' => $categories,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin']));
		} else {
			echo('Compte administrateur nécessaire');
		}
	}

	/**
	* suppression dans la BD d'un categorie à partir du form précédent
	* @param aucun
	* @return rien
	*/
	public function suppCategorie() {
		$id_categorie = $_POST['id_categorie'];
        $categorie = new Projet(['id_categorie' => $id_categorie]);
		$updateCategorieProjet = $this->proManager->updateCategorieProjet($categorie);
		$ok = $this->proManager->deleteCategorie($categorie);
		$message = $ok ?  "categorie supprimé" : "probleme lors de la supression";
		echo $this->twig->render('index.html.twig',array('message'=>$message,'acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
	}
	
}