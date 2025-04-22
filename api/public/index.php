<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../controllers/themes.php';
require __DIR__ . '/../models/databaseService.php';

$mustache = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
]);

$db = new DatabaseService();
$allThemes = getAllThemes($db);
// Extraire uniquement les noms des thèmes
$themeNames = array_column($allThemes, 'name');

$filterData = [
    "themes" => $themeNames
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>The StoryTeller</title>
    <script src="/../api/models/lazyLoadService.js"></script>
    <script>
        window.onload = function() {
            window.scrollTo(0, 0);
        }
    </script>
</head>
<body class="bg-background">
    <?php
    include __DIR__ . '/../views/navbar.php';
    echo $mustache->render('filter', $filterData);
    ?>
    <div id="stories-container">
        <?php  echo $mustache->render('storycardLoading');  ?> 
    </div>

    <script src="https://unpkg.com/mustache@latest"></script>
    <script>
        lazyLoadStories('/serve/stories');
    </script>
</body>
</html>