<?php
/**
 * articles_categories_fixtures.php
 */

//Chargement des dépendances composer
require_once '../vendor/autoload.php';

//Connexion à la base de données
require_once '../connexion.php';

$bdd = connectBdd('root','', 'blog_db');

//Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

//Préparartion de la requete d'insertion d'utilisateur
$insertUser = $bdd->prepare("INSERT INTO articles_categories (article_id, category_id) VALUES(:article_id, :category_id)");

//Selectionne tous les articles
$query = $bdd->query("SELECT id FROM articles");
$articles = $query->fetchAll();

//Selectionne toutes les catégories
$query = $bdd->query("SELECT id FROM categories");
$categories = $query->fetchAll();


foreach ($articles as $article) {
    $iteration = rand(1,4);

    for ($j=0; $j < $iteration; $j++) { 
        $categorie = $faker->randomElement($categories);

        $insertUser->bindValue(':article_id', $article['id']);
        $insertUser->bindValue(':category_id', $categorie['id']);
        $insertUser->execute();
    }
}
?>