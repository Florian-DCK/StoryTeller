<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/themes.php';
require_once __DIR__ . '/../controllers/participations.php';
require_once __DIR__ . '/../controllers/users.php';
require_once __DIR__ . '/../controllers/stories.php';
require_once __DIR__ . '/../models/databaseService.php';    

$db = new DatabaseService();

$storyId = $_GET['id'] ?? null;
$story = getStory($db, $storyId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $story['title'].' - StoryTeller' ?></title>
    <link rel="stylesheet" href="/api/public/global.css">
    <script src="/api/models/lazyLoadService.js"></script>
    <script src="/api/models/toggleLike.js"></script>
</head>
<body class="bg-background">
    <?php
    $mustache = new Mustache_Engine([
        'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
        'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
    ]);

    include_once __DIR__ . '/navbar.php';



    if (!$story) {
        echo "<p class='text-red-500'>Aucune histoire trouvée.</p>";
        exit;
    }

    ?>
    <div id="story-container">
        <?php echo $mustache->render('storycardLoading'); ?> 
    </div>
</body>
<script src="https://unpkg.com/mustache@latest"></script>
<script>
    lazyLoadStory('<?php echo $storyId; ?>');
</script>
</html>