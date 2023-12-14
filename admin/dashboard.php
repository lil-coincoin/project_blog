<?php
//Demarrer une session
session_start();
//Verifier si l'utilisateur peut accéder à cette page
if(!isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}

//Connexion à la BDD
require_once '../connexion.php';
$bdd = connectBdd('root','', 'blog_db');

//Selectionne tous les articles et leurs catégories
$query = $bdd -> query("SELECT articles.id, articles.title, articles.publication_date, GROUP_CONCAT(categories.name, ' ') AS categories FROM articles
LEFT JOIN articles_categories ON articles_categories.article_id = articles.id
LEFT JOIN categories ON categories.id = articles_categories.category_id
GROUP BY id;");

//Retourne tous les résultats trouvés par la requete SQL ci-dessus
$results = $query->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Administration</title>
</head>
<body>
    <div class="container p-3">
        <h1>Administration</h1>
        <a href="logout.php">Déconnexion</a>

        <p>Ici afficher un tableau contenant tous les articles avec les données suivants :
        ID, Titre, nom de categorie ,Date de publication et une colonne "actions"

        La colonne "actions" contiendra 2 liens : Editer et supprimer</p>

        <table class="table">
            <thead>
                <tr>
                <th scope="col">ID</th>
                <th scope="col">Titre</th>
                <th scope="col">Catégories</th>
                <th scope="col">Date de publications</th>
                <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($results as $data) :
                ?>
                <tr>
                <th scope="row"><?php echo $data['id']; ?></th>
                <td><?php echo $data['title']; ?></td>
                <td><?php echo $data['categories']; ?></td>
                <td><?php echo $data['publication_date']; ?></td>
                <td><a href="">Editer</a> | <a href="">Supprimer</a></td>
                </tr>
                <?php
                    endforeach;
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>