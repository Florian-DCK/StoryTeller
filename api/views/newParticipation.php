<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <title>Nouvelle participation</title>
</head>


<body class="bg-background h-screen flex flex-col items-center">
    <?php
    require __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/../controllers/themes.php';
    require_once __DIR__ . '/../controllers/participations.php';
    require_once __DIR__ . '/../controllers/users.php';
    require_once __DIR__ . '/../controllers/stories.php';
    require_once __DIR__ . '/../models/databaseService.php';


    $db = new DatabaseService();

    $mustache = new Mustache_Engine(
        [
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
        ]
    );
    $story = getStoryByTitle($db, $_GET['story']);
    $storyThemes = $story['themes'];
    $themeNames = array_column($storyThemes, 'name');

    $Data = [
        "themes" => $themeNames,
        "story" => $story,
        "username" => $_SESSION['username'],
        "avatar" => $_SESSION['avatar']
    ];

    /* Variables de contexte */

    $fmt = datefmt_create(
        'fr_FR',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'Europe/Paris',
        IntlDateFormatter::GREGORIAN,
        'dd MMMM yyyy'
    );

    include __DIR__ . '/navbar.php';

    echo $mustache->render('newParticipation', $Data);

