<?php
// utilisation des sessions
session_start();

include "moteurtemplate.php";
include "connect.php";

include "Controllers/projetsController.php";
include "Controllers/utilisateursController.php";
$proController = new ProjetController($bdd,$twig);
$utiController = new UtilisateurController($bdd,$twig);

if ($_SERVER['QUERY_STRING'] === '' && strpos($_SERVER['REQUEST_URI'], 'index.php') === false) {
  header("Location: ?action=accueil");
  exit();
}


// texte du message
$message = "";

$identite = "";

// ============================== connexion / deconnexion / gestion ==================

// si la variable de session n'existe pas, on la crée
if (!isset($_SESSION['acces'])) {
   $_SESSION['acces']="non";
}

if (!isset($_SESSION['admin'])) {
  $_SESSION['admin']="non";
}
// click sur le bouton connexion
if (isset($_POST["connexion"]))  {  
  $message = $utiController->utilisateurConnexion($_POST);  
}

// deconnexion : click sur le bouton deconnexion
if (isset($_GET["action"]) && $_GET['action']=="logout") { 
    $message = $utiController->utilisateurDeconnexion(); 
} 

// formulaire de connexion
if (isset($_GET["action"])  && $_GET["action"]=="login") {
  $utiController->utilisateurFormulaire(); 
}

// formulaire inscription
if (isset($_GET["action"]) && $_GET["action"]=="signup") {
  $utiController->utilisateurInscriptionFormulaire();
}

// click sur le bouton connexion
if (isset($_POST["inscription"]))  {  
  $message = $utiController->utilisateurInscription($_POST);  
}

// informations personnelles
if (isset($_GET["action"]) && $_GET["action"]=="infoperso") {
  $utiController->utilisateurInfoPerso();
}

// ============================== page d'accueil ==================

// cas par défaut = page d'accueil
if (!isset($_GET["action"]) && empty($_POST)) {
  echo $twig->render('index.html.twig',array('acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
}

// ============================== gestion des projets ==================

// liste des projets
//  https://.../index/php?action=projets
if (isset($_GET["action"]) && $_GET["action"]=="projets") {
  $proController->listeProjets();
}

// liste de mes projets
if (isset($_GET["action"]) && $_GET["action"]=="mesprojets") { 
  $proController->listeMesProjets($_SESSION['id_utilisateur']);
}

if (isset($_GET["action"]) && $_GET["action"]=="accueil") {
  echo $twig->render('accueil.html.twig',array('acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin']));
}

if (isset($_GET["action"]) && $_GET["action"] == "details" && isset($_GET["id"])) {
  $projetId = $_GET["id"];
  $proController->detailsProjet($projetId);
}

// recherche des projets
if (isset($_POST["valider_recher"])) { 
  $proController->rechercheProjet();
}


// ajout d'un projet
//  https://.../index/php?action=ajout
if (isset($_GET["action"]) && $_GET["action"]=="ajout") {
  $proController->formAjoutProjet();
}

// ajout d'un projet dans la base
// --> au clic sur le bouton "valider_ajout" du form précédent
if (isset($_POST["valider_ajout"])) {
  $proController->ajoutProjet();
}


// suppression d'un projet : choix du projet
//  https://.../index/php?action=suppr
if (isset($_GET["action"]) && $_GET["action"]=="suppr") { 
  $proController->choixSuppProjet($_SESSION['id_utilisateur']);
}

// supression d'un projet dans la base
// --> au clic sur le bouton "valider_supp" du form précédent
if (isset($_POST["valider_supp"])) { 
  $proController->suppProjet();
}

// modification d'un projet : choix du projet
if (isset($_GET["action"]) && $_GET["action"]=="modif") { 
  $proController->choixModProjet($_SESSION['id_utilisateur']);
}

// modification d'un projet : saisie des nouvelles valeurs
if (isset($_POST["saisie_modif"])) {   
  $proController->saisieModProjet();
}

//modification d'un projet : enregistrement dans la bd
if (isset($_POST["valider_modif"])) {
  $proController->modProjet();
}

if (isset($_POST["valider_commentaire"])) {
  $proController->ajoutCommentaire();
}

// ============================== Admin ==================

// modification d'un projet : choix du projet parmi tous les projets
if (isset($_GET["action"]) && $_GET["action"]=="modifall") { 
  $proController->choixModProjetAll();
}

// suppression d'un projet : choix du projet parmi tous les projets
if (isset($_GET["action"]) && $_GET["action"]=="supprall") { 
  $proController->choixSuppProjetAll();
}

// ajout d'un contexte form
if (isset($_GET["action"]) && $_GET["action"]=="ajoutcontexte") {
  $proController->formAjoutContexte();
}

// ajout d'un contexte dans la base
if (isset($_POST["valider_ajoutcontexte"])) {
  $proController->ajoutContexte();
}

// modification d'une contexte : choix de la categorie
if (isset($_GET["action"]) && $_GET["action"]=="modifcontexte") { 
  $proController->choixModContexte();
}

// modification d'une contexte : saisie des nouvelles valeurs
if (isset($_POST["saisie_modifcontexte"])) {   
  $proController->saisieModContexte();
}

//modification d'une contexte : enregistrement dans la bd
if (isset($_POST["valider_modifcontexte"])) {
  $proController->modContexte();
}

// suppression d'un contexte form
if (isset($_GET["action"]) && $_GET["action"]=="supprcontexte") { 
  $proController->choixSuppContexte();
}

// supression d'un contexte dans la base
if (isset($_POST["valider_suppcontexte"])) { 
  $proController->suppContexte();
}









// ajout d'une categorie form
if (isset($_GET["action"]) && $_GET["action"]=="ajoutcategorie") {
  $proController->formAjoutCategorie();
}

// ajout d'une categorie dans la base
if (isset($_POST["valider_ajoutcategorie"])) {
  $proController->ajoutCategorie();
}

// modification d'une categorie : choix de la categorie
if (isset($_GET["action"]) && $_GET["action"]=="modifcategorie") { 
  $proController->choixModCategorie();
}

// modification d'une ctaegorie : saisie des nouvelles valeurs
if (isset($_POST["saisie_modifcategorie"])) {   
  $proController->saisieModCategorie();
}

//modification d'une categorie : enregistrement dans la bd
if (isset($_POST["valider_modifcategorie"])) {
  $proController->modCategorie();
}

// suppression d'un ecategorie form
if (isset($_GET["action"]) && $_GET["action"]=="supprcategorie") { 
  $proController->choixSuppCategorie();
}

// supression d'une categorie dans la base
if (isset($_POST["valider_suppcategorie"])) { 
  $proController->suppCategorie();
}

?>

