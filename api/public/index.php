<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../controllers/themes.php';
require __DIR__ . '/../models/databaseService.php';

$mustache = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
]);

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

$db = new DatabaseService();
$allThemes = getAllThemes($db);
// Extraire uniquement les noms des thèmes
$themeNames = array_column($allThemes, 'name');
$filterData = [
    "themes" => $themeNames
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../api/public/global.css">
    <title>The StoryTeller</title>
    <script>
        window.onload = function() {
            window.scrollTo(0, 0);
        }
    </script>
</head>
<body class="bg-background">
    <?php
    echo $mustache->render('navbar', $navbarData);
    echo $mustache->render('filter', $filterData);
    ?>
    <div id="stories-container">
        <?php  echo $mustache->render('storycardLoading');  ?> 
    </div>

    <script src="https://unpkg.com/mustache@latest"></script>
    <script>
fetch('/serve/stories?limit=5')
    .then(response => response.json())
    .then(stories => {
        const container = document.getElementById('stories-container');
        
        Promise.all(stories.map(story =>
            Promise.all([
                fetch(`/serve/users/${story.user_id}`).then(res => res.json()).then(data => data.username || 'Inconnu'),
                fetch(`/serve/participations/${story.id}`).then(res => res.json())
                    .then(participations => Promise.all(
                        participations.map(p => 
                            fetch(`/serve/users/${p.user_id}`)
                                .then(res => res.json())
                                .then(userData => {
                                    console.log('userData pour participation:', p.user_id, userData);
                                    return {
                                        content: p.content,
                                        author: userData.userName || 'Inconnu'
                                    };
                                })
                        )
                    ))
            ]).then(([author, participations]) => ({
                id: story.id,
                title: story.title,
                author,
                participationNumber: participations.length,
                likes: story.likes,
                participations: participations
            }))
        ))
        .then(formattedStories => {
            // console.log('Stories avec participations:', formattedStories);
            // Charger à la fois le template principal et le partial
            Promise.all([
                fetch('/api/templates/storycard.mustache').then(response => response.text()),
                fetch('/api/templates/partials/participation.mustache').then(response => response.text())
            ])
            .then(([templateText, participationTemplate]) => {
                // Enregistrer le partial avant de rendre le template principal
                Mustache.parse(participationTemplate);
                const partials = { 'participation': participationTemplate };
                container.innerHTML = '';
                container.classList = '';
                
                formattedStories.forEach(story => {
                    // console.log('Story:', story);
                    const rendu = Mustache.render(templateText, story, partials);
                    container.innerHTML += rendu;
                });
            });
        });
    })
    .catch(error => {
        console.error('Erreur:', error);
        document.getElementById('stories-container').innerHTML = '<p>Erreur lors du chargement.</p>';
    });
    </script>
</body>
</html>