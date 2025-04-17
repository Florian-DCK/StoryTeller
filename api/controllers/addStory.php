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

if (addStory($db, $_POST['title'], $_SESSION['userId'])) {
    $story = getStoryByTitle($db, $_POST['title']);
    $storyId = $story[0]['id'];
    $participation = $_POST['participation'];
    
    addParticipation($db, $storyId, $_SESSION['userId'], $participation);
    
    echo json_encode(['success' => 'Story added successfully']);
} else {
    echo json_encode(['error' => 'Failed to add story']);
}