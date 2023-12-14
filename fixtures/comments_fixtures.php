<?php
/**
 * comments_fixtures.php
 */

//Chargement des dépendances composer
require_once '../vendor/autoload.php';

//Connexion à la base de données
require_once '../connexion.php';

$bdd = connectBdd('root','', 'blog_db');

//Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

//Préparartion de la requete d'insertion d'utilisateur
$insertUser = $bdd->prepare("INSERT INTO comments (content, comment_date, user_id, article_id) VALUES(:content, :comment_date, :user_id, :article_id)");

//Selectionne tous les utilisateurs
$query = $bdd->query("SELECT id FROM users");
$users = $query->fetchAll();

//Selectionne tous les articles
$query = $bdd->query("SELECT id, publication_date FROM articles");
$articles = $query->fetchAll();

//Générer 3 utilisateurs
for ($i=0; $i < 200; $i++) { 

    //Selection un utilisateur random
    $user = $faker->randomElement($users);

    //Selection un article random
    $article = $faker->randomElement($articles);

    //Generer une date entre, il y'a deux ans et aujourd'hui
    $date = $faker->dateTimeBetween($article['publication_date'])->format('Y-m-d H:i:s');

    $insertUser->bindValue(':content', $faker->text);
    $insertUser->bindValue(':comment_date', $date);
    $insertUser->bindValue(':user_id', $user['id']);
    $insertUser->bindValue(':article_id', $article['id']);
    $insertUser->execute();
}
?>