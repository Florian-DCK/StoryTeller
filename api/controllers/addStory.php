<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';

include_once __DIR__ . '/../models/databaseService.php';
include_once __DIR__ . '/../controllers/themes.php';
include_once __DIR__ . '/../controllers/participations.php';
include_once __DIR__ . '/../controllers/users.php';
include_once __DIR__ . '/../controllers/stories.php';

$db = new DatabaseService();

if (!isset($_SESSION['userId']) || empty($_SESSION['userId']) || !isset($_POST['title']) || empty($_POST['title']) || !isset($_POST['participation']) || empty($_POST['participation'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$allThemes = getAllThemes($db);
$themeIds = [];

if (isset($_POST['genres']) && is_array($_POST['genres'])) {
    foreach ($_POST['genres'] as $themeName) {
        $found = false;
        foreach ($allThemes as $theme) {
            if ($theme['name'] == $themeName) {
                $themeIds[] = $theme['id'];
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo json_encode(['error' => 'Invalid theme: ' . $themeName]);
            exit;
        }
    }
}

if (addStory($db, $_POST['title'], $_SESSION['userId'])) {
    $story = getStoryByTitle($db, $_POST['title']);
    if (!$story || !isset($story['id'])) {
        echo json_encode(['error' => 'Failed to retrieve story']);
        exit;
    }
    
    $storyId = $story['id'];
    $participation = $_POST['participation'];
    
    addParticipation($db, $storyId, $_SESSION['userId'], $participation);
    
    if (!empty($themeIds)) {
        linkThemes($db, $storyId, $themeIds);
    }
    

    Header('Location: /story/' . $storyId);
    exit; 
} else {
    echo json_encode(['error' => 'Failed to add story']);
}