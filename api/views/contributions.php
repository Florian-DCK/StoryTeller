
<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/themes.php';
require_once __DIR__ . '/../models/databaseService.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <script src="/../api/models/lazyLoadService.js"></script>
    <script src="/api/models/toggleLike.js"></script>
    <title>Recherche</title>
    <script src="/api/models/toggleLike.js"></script>
</head>

<body class="bg-background flex flex-col">
    <?php
    $mustache = new Mustache_Engine(
        [
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
        ]
    );
    $db = new DatabaseService();
    include __DIR__ . '/../views/navbar.php';
    ?>


    <h1 class="font-family-joan text-2xl mt-4 mb-4 text-center w-full">Vos contributions</h1>
    <div id="stories-container">
        <?php echo $mustache->render('storycardLoading'); ?>
    </div>

    <script src="https://unpkg.com/mustache@latest"></script>
    <script>
        // Construire l'URL de recherche avec les paramètres actuels
    lazyLoadStories("/serve/stories?author=<?php echo $_SESSION['userId']; ?>");
    </script>
</body>

</html>