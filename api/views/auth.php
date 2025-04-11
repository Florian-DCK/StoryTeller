<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <title>Connexion</title>
</head>


<body class="bg-background flex flex-col items-center h-screen">
    <?php
    require __DIR__ . '/../../vendor/autoload.php';

    $mustache = new Mustache_Engine(
        [
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
        ]
    );

    /* Variables de contexte */

    $url = $_SERVER['REQUEST_URI'];
    $isAuthRoute = preg_match('/\/auth/', $url) ? false : true;

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
        "isAuthRoute" => $isAuthRoute,
    ];

    $isLogin = true;

    function toggleAuthForm($isLogin)
    {
        return !$isLogin;
    }

    $authData = [
        "isLogin" => isset($_GET['login']) ? null : $isLogin,
        "success" => isset($_GET['success']) ? $_GET['success'] : null
    ];


    echo $mustache->render('navbar', $navbarData);
    echo $mustache->render('auth', $authData);
