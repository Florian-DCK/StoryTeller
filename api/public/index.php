<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';

$mustache = new Mustache_Engine(
    [
        'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
        'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
    ]
);

$fmt = datefmt_create(
    'fr_FR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::FULL,
    'Europe/Paris',
    IntlDateFormatter::GREGORIAN,
    'dd MMMM yyyy'
);

$url = $_SERVER['REQUEST_URI'];
$isAuthRoute = preg_match('/\/auth/', $url) ? false : true;
$navbarData = [
    "date" => datefmt_format($fmt, time()),
    "isAuthRoute" => $isAuthRoute,
    "isConnected" => isset($_SESSION['userId']),
    "username" => isset($_SESSION['username']) ? $_SESSION['username'] : null,
    "avatar" => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : null,
];

require_once __DIR__ . '/../../api/models/databaseService.php';
require_once __DIR__ . '/../../api/controllers/stories.php';
require_once __DIR__ . '/../../api/controllers/participations.php';
require_once __DIR__ . '/../../api/controllers/users.php';
$db = new DatabaseService();
$stories = getAllStories($db);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <title>The StoryTeller</title>
</head>

<body class="bg-background">
    <?php
    echo $mustache->render('navbar', $navbarData);

    echo $mustache->render('filter');
    foreach ($stories as $story) {
        $participations = getParticipations($db, $story['id']);
        $storycardData = [
            "title" => $story['title'],
            "author" => getUsername($db, $story['author_id']),
            "participations" => $participations,
            "participationNumber" => count(getParticipations($db, $story['id'])),
            "likes" => $story['likes'],
        ];
        echo $mustache->render('storycard', $storycardData);
    }
    ?>
</body>

</html>