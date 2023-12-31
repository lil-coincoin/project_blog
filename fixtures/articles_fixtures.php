<?php
/**
 * articles_fixtures.php
 */

//Chargement des dépendances composer
require_once '../vendor/autoload.php';

//Connexion à la base de données
require_once '../connexion.php';

$bdd = connectBdd('root','', 'blog_db');

//Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

//Préparartion de la requete d'insertion d'utilisateur
$insertUser = $bdd->prepare("INSERT INTO articles (title, content, cover, publication_date, user_id) VALUES(:title, :content, :cover, :publication_date, :user_id)");

//Selectionne tous les utilisateurs
$query = $bdd->query("SELECT id FROM users");
$users = $query->fetchAll();

//Générer 3 utilisateurs
for ($i=0; $i < 50; $i++) { 
    //Selection un utilisateur random
    $user = $faker->randomElement($users);

    //Generer une date entre, il y'a deux ans et aujourd'hui
    $date = $faker->dateTimeBetween('-2 years')->format('Y-m-d H:i:s');

    $insertUser->bindValue(':title', $faker->sentence);
    $insertUser->bindValue(':content', $faker->paragraphs(6,true));
    $insertUser->bindValue(':cover', $faker->imageUrl);
    $insertUser->bindValue(':publication_date', $date);
    $insertUser->bindValue(':user_id', $user['id']);
    $insertUser->execute();
}
?>