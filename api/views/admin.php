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
    require_once __DIR__ . '/../controllers/adminData.php';

    $mustache = new Mustache_Engine(
        [
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
        ]
    );

    $timeFormat =
        IntlDateFormatter::create(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            'Europe/Paris',
            IntlDateFormatter::GREGORIAN,
            "dd MMMM 'à' HH:mm"
        );

    $dashboardData = [
        'avgUsers' => fetchAvgUsers($db),
        'totUsers' => fetchTotalUsers($db),
        'totPosts' => fetchTotalPosts($db),
        'pendingTickets' => 2,
        'tickets' => [
            [
                'id' => 1,
                'title' => 'Ticket 1',
                'content' => 'This is the content of ticket 1',
                'status' => 'open',
                'created_at' => datefmt_format($timeFormat, time()),
            ],
            [
                'id' => 2,
                'title' => 'Ticket 2',
                'content' => 'This is the content of ticket 2',
                'status' => 'open',
                'created_at' => datefmt_format($timeFormat, time()),

            ]
        ],
    ];


    include __DIR__ . '/../views/navbar.php';
    echo $mustache->render('dashboard', $dashboardData);
