<?php
include "Modules/utilisateur.php";
include "Models/utilisateurManager.php";

/**
* Définition d'une classe permettant de gérer les utilisateurs
*   en relation avec la base de données	
*/
class UtilisateurController {
    private $utilisateurManager; // instance du manager
	private $proManager; 
    private $twig;

	/**
	* Constructeur = initialisation de la connexion vers le SGBD
	*/
	public function __construct($db, $twig) {
		$this->utilisateurManager = new UtilisateurManager($db);
		$this->proManager = new ProjetManager($db);
		$this->twig = $twig;
	}
        
	/**
	* connexion
	* @param aucun
	* @return rien
	*/
	function utilisateurConnexion($data) {
		$utilisateur = $this->utilisateurManager->verif_identification($_POST['login'], $_POST['passwd']);
	
		if ($utilisateur != false) {
			$_SESSION['acces'] = "oui";
			$_SESSION['id_utilisateur'] = $utilisateur->id_utilisateur();
			$_SESSION['identite'] = $utilisateur->prenom()." ".$utilisateur->nom();
			$message = "Bonjour ".$utilisateur->prenom()." ".$utilisateur->nom()."!";
			$identite = $utilisateur->prenom() . " " . $utilisateur->nom();
			define('IDENTITE_CONSTANTE', $identite);
	
			$admin = $this->utilisateurManager->verif_administrateur($utilisateur->id_utilisateur());

			if ($admin->admin() == 1) {
				$_SESSION['admin'] = "oui";
			} else {
				$_SESSION['admin'] = "non";
			}
	
			echo $this->twig->render('accueil.html.twig',array('admin'=> $_SESSION['admin'], 'acces'=> $_SESSION['acces'],'message'=>$message, 'identite'=>$identite)); 
		} else {
			$message = "Identification incorrecte";
			$_SESSION['acces'] = "non";
			echo $this->twig->render('index.html.twig',array('acces'=> $_SESSION['acces'],'message'=>$message)); 
		}
	}
	

	

	/**
	* deconnexion
	* @param aucun
	* @return rien
	*/
	function utilisateurDeconnexion() {
		$_SESSION['acces'] = "non"; // acces non autorisé
		$message = "vous êtes déconnecté";
		echo $this->twig->render('index.html.twig',array('acces'=> $_SESSION['acces'],'message'=>$message)); 
	 
	}

	/**
	* Informations personnelles
	* @param aucun
	* @return rien
	*/
	public function utilisateurInfoPerso() {
		$id_utilisateur = $_SESSION['id_utilisateur'];
		$utilisateur = $this->utilisateurManager->getUtilisateurInfoPerso($id_utilisateur);
		$projets = $this->proManager->getListUtilisateur($id_utilisateur);
		echo $this->twig->render('utilisateur_infoperso.html.twig',array('utis'=>$utilisateur, 'pros'=>$projets, 'acces'=> $_SESSION['acces'], 'id_utilisateur' => $_SESSION['id_utilisateur'])); 
	}

	/**
	* formulaire de connexion
	* @param aucun
	* @return rien
	*/
	function utilisateurFormulaire() {
		echo $this->twig->render('utilisateur_connexion.html.twig',array('acces'=> $_SESSION['acces'])); 
	}

	/**
	* formulaire inscription
	* @param aucun
	* @return rien
	*/
	function utilisateurInscriptionFormulaire() {
		echo $this->twig->render('utilisateur_inscription.html.twig'); 
	}

	/**
	* ajout dans la BD d'un utilisateur
	* @param aucun
	* @return rien
	*/
	public function utilisateurInscription() {
		// création d'un utilisateur
		$passwordV = false;
		$nomV = false;
		$prenomV = false;
		$emailV = false;
		$identifiant_iutV = false;

		if (isset($_POST['password'])) {
			$password = $_POST['password'];

			if (strlen($password) >= 6) {
				$passwordV = true;
			} else {
				echo "<br>Le mot de passe doit comporter au moins 6 caractères.";
				$passwordV = false;
			}
		} else {
			echo "Le champ 'password' n'a pas été trouvé dans la requête POST.";
		}

		if (isset($_POST['nom'])) {
			$nom = $_POST['nom'];
		
			if (strlen($nom) >= 2) {
				if (ctype_alpha($nom) && $nom === strtolower($nom) || $nom === strtoupper($nom)) {
					$nomV = true;
				} else {
					$nomV = false;
					echo "<br>Le nom doit comporter uniquement des lettres minuscules ou majuscules.";
				}
			} else {
				echo "<br>Le nom doit comporter au moins 2 caractères.";
			}
		} else {
			echo "Le champ 'nom' n'a pas été trouvé dans la requête POST.";
		}

		if (isset($_POST['prenom'])) {
			$prenom = $_POST['prenom'];
		
			if (strlen($prenom) >= 2) {
				if (ctype_alpha($prenom) && $prenom === strtolower($prenom) || $prenom === strtoupper($prenom)) {
					$prenomV = true;
				} else {
					$prenomV = false;
					echo "<br>Le prenom doit comporter uniquement des lettres minuscules ou majuscules.";
				}
			} else {
				echo "<br>Le prenom doit comporter au moins 2 caractères.";
			}
		} else {
			echo "Le champ 'prenom' n'a pas été trouvé dans la requête POST.";
		}
		
		if (isset($_POST['email'])) {
			$email = $_POST['email'];
		
			$email = strtolower($email);
		
			if (substr($email, -17) === '@etu.iut-tlse3.fr') {
				$emailV = true;
			} else {
				$emailV = false;
				echo "<br>L'adresse email doit se terminer par '@etu.iut-tlse3.fr'.";
			}
		} else {
			echo "Le champ 'email' n'a pas été trouvé dans la requête POST.";
		}

		if (isset($_POST['identifiant_iut'])) {
			$identifiant_iut = $_POST['identifiant_iut'];
		
			$identifiant_iut = strtolower($identifiant_iut);
		
			if (preg_match('/^[a-z]{3}[0-9]{4}[a-z]{1}$/', $identifiant_iut)) {
				$identifiant_iutV = true;
			} else {
				$identifiant_iutV = false;
				echo "<br>L'identifiant doit être composé de 3 lettres suivies de 4 chiffres et d'une lettre, le tout en minuscules.";
			}
		} else {
			echo "Le champ 'identifiant' n'a pas été trouvé dans la requête POST.";
		}

		if ($passwordV == true && $nomV == true && $prenomV == true && $emailV == true && $identifiant_iutV == true){
			// stockage dans la BD
			$utilisateur = new Utilisateur($_POST);
			$ok = $this->utilisateurManager->addUtilisateur($utilisateur);
			header("Location: index.php?action=login");
			exit();
		} else {
			echo "<br>Erreur dans l'ajout des données";
		}
	}

}