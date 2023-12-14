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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Administration</title>
</head>
<body>
    <nav class="navbar bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Administration</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container p-3">

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
                <td><?php echo DateTime::createFromFormat('Y-m-d H:i:s', $data['publication_date'])->format('d-m-Y');?></td>
                <td>
                    <a href="#" class="btn btn-light btn-sm">Editer</a>
                    <a href="#" class="btn btn-danger btn-sm">Supprimer</a>
                </td>
                </tr>
                <?php
                    endforeach;
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>