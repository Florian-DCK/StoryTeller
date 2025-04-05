<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <title>The StoryTeller</title>
</head>
<body>
    <?php
    require __DIR__ . '/../../vendor/autoload.php';

    $mustache = new Mustache_Engine([
        'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
        'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
        ]
    );
    
    echo $mustache->render('test');

    ?>
    <h1 class="text-red-500 text-9xl">HOME</h1>
</body>
</html>