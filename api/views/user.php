<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <title>Admin</title>
</head>


<body class="bg-background h-screen flex flex-col ">
    <?php
    require __DIR__ . '/../../vendor/autoload.php';

    $mustache = new Mustache_Engine(
        [
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
        ]
    );

    $userPageData = [
        'followers' => 17,
        'stories' => 5,

    ];

    include_once __DIR__ . '/../views/navbar.php';
    echo $mustache->render('user', $userPageData);
