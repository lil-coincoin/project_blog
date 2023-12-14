<?php

//Demarrage de la session
//Doit etre placé au plus haut possible dans le code
session_start();
/**
 * login.php
 * Permet de vérifier si un utilisateur
 * peut accéder à l'administration
 */


 /**
  * 1 Verifier si le formulaire est complet -> sinon erreur
  * 
  */

/**
 * 1 Verifier si le formulaire est complet -> sinon erreur
 * 2 Nettoyer les données issues du formulaire
 * 3 Selectionner l'utilisateur en BDD via son email -> sinon erreur
 * 4 Vérifier si le mot de passe du formulaire correspond à celui en BDD -> sinon erreur
 * 5 Rediriger l'utilisateur vers la page "dashboard.php"
 */

require_once '../connexion.php';
$error = null;

if(!empty($_POST["email"]) && !empty($_POST["password"]) ){

    $email = htmlspecialchars(strip_tags($_POST["email"]));
    $mdp = htmlspecialchars(strip_tags($_POST["password"]));

    $bdd = connectBdd('root','', 'blog_db');
    $query = $bdd -> prepare("SELECT * FROM users WHERE email = :email");
    $query->bindValue(':email', $email);
    $query->execute();

    /**
     * fetch() retourner un tableau associatif contenant soit : 
     * - les informations d'un utilisateur
     * - false
     */

    $user = $query->fetch();
    if($user && password_verify($mdp, $user['password'])){
        header('Location: dashboard.php');
        exit;
    }else{
        $error = "Identifiants invalides";
    }
}
else{
    $error = 'Tous les champs sont obligatoires';
}

//Gestion de nos erreurs
if($error != null){
    //Declaration d'une session contenant l'erreur
    $_SESSION['error'] = $error;
    header('Location: index.php');
    exit;
}
?>