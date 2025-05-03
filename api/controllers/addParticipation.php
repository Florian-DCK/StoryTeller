<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/themes.php';
require_once __DIR__ . '/../controllers/participations.php';
require_once __DIR__ . '/../controllers/users.php';
require_once __DIR__ . '/../controllers/stories.php';
require_once __DIR__ . '/../models/databaseService.php';

$db = new DatabaseService();

$storyId = $_GET['storyId'] ?? null;
$userId = $_SESSION['userId'] ?? null;
$content = $_POST['participation'] ?? null;

if (!$storyId || !$userId || !$content) {
    echo json_encode([
        'success' => false,
        'message' => 'Données incomplètes'
    ]);
    exit;
}

$result = addParticipation($db, $storyId, $userId, $content);

if ($result) {
    Header('Location: /story/' . $storyId);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'ajout de la participation'
    ]);
}