<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/themes.php';
require_once __DIR__ . '/../models/databaseService.php';
$query = isset($_GET['query']) ? $_GET['query'] : null;
$themes = isset($_GET['themes']) ? explode(",", $_GET['themes']) : null;
$sort = isset($_GET['sortBy']) ? $_GET['sortBy'] : null;

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
    <script>
        window.onload = function() {
            window.scrollTo(0, 0);

            // Pré-remplir les champs de recherche avec les valeurs actuelles
            if ("<?php echo $query; ?>") {
                document.getElementById('search').value = "<?php echo $query; ?>";
            }

            <?php if ($themes): ?>
                <?php foreach ($themes as $theme): ?>
                    if (document.getElementById('<?php echo $theme; ?>')) {
                        document.getElementById('<?php echo $theme; ?>').checked = true;
                    }
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($sort): ?>
                if (document.getElementById('<?php echo $sort; ?>')) {
                    document.getElementById('<?php echo $sort; ?>').checked = true;
                }
            <?php endif; ?>
        }
    </script>
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
    $allThemes = getAllThemes($db);
    // Extraire uniquement les noms des thèmes
    $themeNames = array_column($allThemes, 'name');
    $filterData = [
        "themes" => $themeNames
    ];
    include __DIR__ . '/../views/navbar.php';
    echo $mustache->render('filter' , $filterData);
    ?>


    <h1 class="font-family-joan text-2xl mt-4 mb-4 text-center w-full">Résultats de recherche</h1>
    <div id="stories-container">
        <?php echo $mustache->render('storycardLoading'); ?>
    </div>

    <script src="https://unpkg.com/mustache@latest"></script>
    <script>
        // Construire l'URL de recherche avec les paramètres actuels
        function buildSearchUrl() {
            const params = new URLSearchParams();

            <?php if ($query): ?>
                params.append('query', '<?php echo $query; ?>');
            <?php endif; ?>

            <?php if ($themes): ?>
                params.append('themes', '<?php echo implode(",", $themes); ?>');
            <?php endif; ?>

            <?php if ($sort): ?>
                params.append('sortBy', '<?php echo $sort; ?>');
            <?php endif; ?>

            // Ajout d'une limite par défaut pour la pagination
            params.append('limit', '5');

            console.log('/serve/stories?' + params.toString());
            return '/serve/stories?' + params.toString();
        }

    lazyLoadStories(buildSearchUrl());
    </script>
</body>

</html>