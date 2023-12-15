<?php
/**
 * update_article.php
 * Mise à jour d'un article en BDD
 */

/**
 * 1- Seul une personne connectée peut y accéder
 * 2- Vérifier si la méthode du formulaire reçue est bien "POST"
 * 3- Connexion à la base de données
 * 4- Récupérer et nettoyer les données
 * 5- Mise à jour du titre et du contenu de l'article dans la table "articles"
 * 6- Redirection vers le formulaire d'édition avec un message de succès
 */
//Demarrer une session
session_start();
//Verifier si l'utilisateur peut accéder à cette page
if(!isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    echo 'tes';
    require_once '../connexion.php';
    $bdd = connectBdd('root','', 'blog_db');

    $titre = htmlspecialchars(strip_tags($_POST["titre"]));
    $contenu = htmlspecialchars(strip_tags($_POST["contenu"]));
    $id_article = htmlspecialchars(strip_tags($_GET["id"]));
    $categories = array_map('strip_tags',$_POST["categorie"]);

    if(!empty($titre) && !empty($contenu)){
        // Mise à jour du titre et du contenu de l'article dans la table "articles"
        $query = $bdd -> prepare("UPDATE articles SET title = :titre, content = :contenu WHERE id = :id");

        $query->bindValue(':titre', $titre);
        $query->bindValue(':contenu', $contenu);
        $query->bindValue(':id', $id_article);
        $query->execute();

        //Mise à jour des catégories liées à l'article
        $deleteQuery = $bdd->prepare("DELETE FROM articles_categories WHERE article_id = :id");
        $deleteQuery->bindValue(':id', $id_article);
        $deleteQuery->execute();


        $insertCategoryQuery = $bdd->prepare("INSERT INTO articles_categories(article_id, category_id) VALUES (:article_id, :category_id)");

        foreach ($categories as $category) {
            $insertCategoryQuery->bindValue(':article_id', $id_article);
            $insertCategoryQuery->bindValue(':category_id', $category);
            $insertCategoryQuery->execute();
        }

        // Message de succès
        $_SESSION['succes'] = "Mise à jour faite";
    }else{
        // Message d'erreur
        $_SESSION['error'] = 'Le titre et le contenu est obligatoire';
    }
    // Redirection vers le formulaire d'édition
    header("Location: edit.php/?id=$id_article");
    exit;
}else{
    header("Location: dashboard.php");
    exit;
}
?>