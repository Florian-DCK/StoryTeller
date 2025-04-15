<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <title>Nouvelle histoire</title>
</head>


<body class="bg-background h-screen flex flex-col items-center">
    <?php
    require __DIR__ . '/../../vendor/autoload.php';

    $mustache = new Mustache_Engine(
        [
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
        ]
    );

    /* Variables de contexte */

    $fmt = datefmt_create(
        'fr_FR',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'Europe/Paris',
        IntlDateFormatter::GREGORIAN,
        'dd MMMM yyyy'
    );
    $navbarData = [
        "date" => datefmt_format($fmt, time()),
        "isAuthRoute" => true,
        "isConnected" => isset($_SESSION['userId']),
        "username" => isset($_SESSION['username']) ? $_SESSION['username'] : null,
        "avatar" => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : null,
    ];

    echo $mustache->render('navbar', $navbarData);
    echo $mustache->render('newstory', []);
