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

//Charger les dépendances PHP
require_once '../vendor/autoload.php';

//Verifier si l'utilisateur peut accéder à cette page
if(!isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    require_once '../connexion.php';
    $bdd = connectBdd('root','', 'blog_db');

    $titre = htmlspecialchars(strip_tags($_POST["titre"]));
    $contenu = htmlspecialchars(strip_tags($_POST["contenu"]));
    $id_article = htmlspecialchars(strip_tags($_GET["id"]));

    //Applique une fonction sur les valeurs d'un tableau
    $categories = array_map('strip_tags',$_POST["categorie"]);

    if(!empty($titre) && !empty($contenu)){
        //Selectionner le nom de l'image actuellement en BDD
        $selectCoverQuery = $bdd->prepare("SELECT cover FROM articles WHERE id = :id");
        $selectCoverQuery->bindValue(':id', $id_article);
        $selectCoverQuery->execute();

        //Recuperation de la valeur de la colonne et stockage de l'info dans une variable
        $cover= $selectCoverQuery->fetchColumn();

        //Verifie si un upload doit etre fait
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {

            $typeExt = [
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
            ];

            $sizeMax = 1 * 1024 * 1024;
            $extension = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));

            // Vérifier si le fichier est bien une image autorisée
            if (array_key_exists($extension, $typeExt) && in_array($_FILES['cover']['type'], $typeExt)) {

                // Vérifie si le poids de l'image ne dépasse pas la limite fixée
                if ($_FILES['cover']['size'] <= $sizeMax) {

                    // Supprime l'ancienne image
                    if (file_exists("../public/uploads/$cover")) {
                        // Supprime l'image à l'endroit indiqué
                        unlink("../public/uploads/$cover");
                    }

                    // Renomme le nom de l'image
                    $slugify = new \Cocur\Slugify\Slugify();
                    $newName = $slugify->slugify("$title-$id_article");
                    $cover = "$newName.$extension";

                    // Télécharge la nouvelle image sous le nouveau nom
                    move_uploaded_file(
                        $_FILES['cover']['tmp_name'],
                        "../public/uploads/$cover"
                    );

                } else {
                    $_SESSION['error'] = "L'image ne doit pas dépasser les 1Mo";
                    header("Location: edit.php?id=$$id_article");
                    exit;
                }
            } else {
                $_SESSION['error'] = "Le fichier n'est pas une image conforme";
                header("Location: edit.php?id=$id_article");
                exit;
            }
        }

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