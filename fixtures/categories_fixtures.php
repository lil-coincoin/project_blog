<?php
/**
 * categories_fixtures.php
 */

//Chargement des dépendances composer
require_once '../vendor/autoload.php';

//Connexion à la base de données
require_once '../connexion.php';

$bdd = connectBdd('root','', 'blog_db');

//Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

//Préparartion de la requete d'insertion d'utilisateur
$insertUser = $bdd->prepare("INSERT INTO categories (name) VALUES(:name)");

//Générer 3 utilisateurs
for ($i=0; $i < 20; $i++) { 
    $insertUser->bindValue(':name', $faker->word);
    $insertUser->execute();
}
?>