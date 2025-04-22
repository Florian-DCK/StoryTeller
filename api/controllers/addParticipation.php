<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/themes.php';
require_once __DIR__ . '/../controllers/participations.php';
require_once __DIR__ . '/../controllers/users.php';
require_once __DIR__ . '/../controllers/stories.php';
require_once __DIR__ . '/../models/databaseService.php';

$db = new DatabaseService();

// Récupération des données
$storyId = $_GET['storyId'] ?? null;
$userId = $_SESSION['userId'] ?? null;
$content = $_POST['participation'] ?? null;

// Vérification des données
if (!$storyId || !$userId || !$content) {
    echo json_encode([
        'success' => false,
        'message' => 'Données incomplètes'
    ]);
    exit;
}

// Ajout de la participation
$result = addParticipation($db, $storyId, $userId, $content);

// Retourner le résultat
if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Participation ajoutée avec succès',
        'participation_id' => $result
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'ajout de la participation'
    ]);
}