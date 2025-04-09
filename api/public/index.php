<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <title>The StoryTeller</title>
</head>

<body class=" bg-background">
    <?php
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
    $navbarData = [
        "date" => datefmt_format($fmt, time())
    ];
    echo $mustache->render('navbar', $navbarData);


    require_once __DIR__ . '/../../api/models/databaseService.php';
    require_once __DIR__ . '/../../api/controllers/stories.php';
    require_once __DIR__ . '/../../api/controllers/participations.php';
    require_once __DIR__ . '/../../api/controllers/users.php';
    $db = new DatabaseService();
    $stories = getAllStories($db);
    

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