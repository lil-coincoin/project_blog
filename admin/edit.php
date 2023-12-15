<?php
//Demarrer une session
session_start();
// Verifier si l'utilisateur peut accéder à cette page
if(!isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}

//Verifie si le parametre "id" est présent et/ou non vide
if(empty($_GET['id'])){
    header('Location: http://php.test/projet_blog/admin/dashboard.php');
    exit;
}

$id_article = htmlspecialchars(strip_tags($_GET['id']));

//Connexion à la BDD
require_once '../connexion.php';
$bdd = connectBdd('root','', 'blog_db');

$query = $bdd -> prepare("SELECT * FROM articles WHERE articles.id = :id_article;");

$query->bindValue(':id_article', $id_article);
$query->execute();

//Retourne tous les résultats trouvés par la requete SQL ci-dessus
$articles = $query->fetch();

//Si aucun article n'existe avec cet ID, redirection vers le dashboard.php
if(!$articles || $articles['user_id'] !== $_SESSION['user']['id']){
    header('Location: http://php.test/projet_blog/admin/dashboard.php');
    exit;
}

$query = $bdd -> query("SELECT * FROM categories;");
$categories = $query->fetchAll();

//Selectionne toutes les catégories liées à l'articles
$query = $bdd->prepare("SELECT category_id FROM articles_categories WHERE article_id = :id_article");
$query->bindValue(':id_article', $id_article);
$query->execute();

/**
 * PDO::FETCH_COLUMN
 * Retourne un tableau indexé contenant les valeurs extraites de la requete SQL pour une seule colonne
 */
$articlesCategories = $query->fetchAll(PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <div class="container p-3">
        <a href="http://php.test/projet_blog/admin/dashboard.php" class="btn btn-primary mb-3">Retour</a>
        <h1>Editer "<?php echo $articles['title']; ?>"</h1>

        <?php
            if(isset($_SESSION['succes'])):
        ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['succes']; ?>
        </div>
        <?php
            unset($_SESSION['succes']);
            endif;
        ?>

        <?php
            if(isset($_SESSION['error'])):
        ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; ?>
        </div>
        <?php
            unset($_SESSION['error']);
            endif;
        ?>
       
        <form action="http://php.test/projet_blog/admin/update_article.php?id=<?php echo $articles['id']; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $articles['title']; ?>">
            </div>
            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu</label>
                <textarea class="form-control" id="contenu" rows="8" name="contenu"><?php echo $articles['content']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="couverture" class="form-label">Couverture</label>
                <input class="form-control" type="file" id="couverture" name="couverture">
            </div>
            <div class="mb-3">
                <label for="categorie" class="form-label">Catégories</label>
                <select multiple class="form-control" aria-label="Default select example" id="categorie" name="categorie[]">
                    <?php foreach($categories as $categorie): ?>
                        <option value="<?php echo $categorie['id']; ?>" <?php echo in_array($categorie['id'], $articlesCategories) ? 'selected' : '' ?>><?php echo $categorie['name'];?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>