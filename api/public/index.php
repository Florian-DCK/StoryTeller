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

    $storycardData = [
        "title" => "Une première histoire",
        "author" => "John Doe",
        "content" => "Voici le contenu de l'histoire. Il peut être très long et contenir beaucoup de texte. Ce texte est là pour illustrer le rendu de l'histoire dans la carte. Ne vous inquiétez pas, il n'y a pas de limite de caractères ici. Il peut même y avoir des retours à la ligne et d'autres éléments de mise en forme.",
        "participationNumber" => 5,
        "likes" => 10,
    ];

    echo $mustache->render('storycard', $storycardData);

    require_once __DIR__ . '/../controllers/stories.php';
    require_once __DIR__ . '/../controllers/participations.php';
    require_once __DIR__ . '/../models/databaseService.php';

    $db = new DatabaseService();

    var_dump(getLimitStories($db, 5));
    ?>
</body>

</html>