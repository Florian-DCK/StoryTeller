<?php
session_start();

require __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . '/../../api/controllers/users.php';
include_once __DIR__ . '/../../api/models/databaseService.php';
include_once __DIR__ . '/../../api/controllers/participations.php';
include_once __DIR__ . '/../../api/controllers/stories.php';

$db = new DatabaseService();
$userData = getUserInfosById($db, $_GET['id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <title><?php echo $userData['userName'] . " - StoryTeller" ;?></title>
    <script src="/api/models/toggleLike.js"></script>
</head>


<body class="bg-background h-screen flex flex-col ">
    <?php

    $mustache = new Mustache_Engine(
        [
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
        ]
    );

    $numberOfParticipations = getNumberOfParticipations($db, $_GET['id']);
    $numberOfStories = getNumberOfStories($db, $_GET['id']);
    $favoriteThemes = getNumberOfStoriesByTheme($db, $_GET['id']);


    $userPageData = [
        'followers' => 17,
        'stories' => $numberOfStories,
        'participations' => $numberOfParticipations,
        'user' => $userData,
        'favoriteThemes' => $favoriteThemes,
        'hasNoStories' => $numberOfStories == 0
    ];


    include_once __DIR__ . '/../views/navbar.php';

    echo $mustache->render('user', $userPageData);

?>
</body>
<script src="https://unpkg.com/mustache@latest"></script>
<script src="/../api/models/lazyLoadService.js"></script>
<script>
    <?php if ($numberOfStories > 0) { ?>
        lazyLoadStories('/serve/stories?author=<?php echo $_GET['id'] ?>' );
    <?php } ?>
</script>
