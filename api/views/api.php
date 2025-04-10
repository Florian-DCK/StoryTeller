<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

// Connexion à la base de données avec notre service personnalisé
require_once __DIR__ . '/../models/databaseService.php';
require_once __DIR__ . '/../models/authService.php';
require_once __DIR__ . '/../controllers/stories.php';
require_once __DIR__ . '/../controllers/participations.php';
require_once __DIR__ . '/../controllers/users.php';

// Initialiser la connexion à la base de données
$db = new DatabaseService();

// Récupérer la méthode HTTP et l'URL demandée
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$resource = $request[0] ?? '';
$id = $request[1] ?? null;

// Ligne ~15, ajoutez ce code après la récupération de $id
$query_params = $_GET;
$limit = isset($query_params['limit']) ? intval($query_params['limit']) : null;

switch ($resource) {
    case 'stories':
        handleStoriesEndpoint($method, $id, $db);
        break;
    case 'participations':
        handleParticipationsEndpoint($method, $id, $db);
        break;
    case 'users':
        handleUsersEndpoint($method, $id, $db);
        break;
    default:
        echo json_encode(['error' => 'Ressource non reconnue']);
        break;
}

function handleStoriesEndpoint($method, $id, $db) {
    global $limit;
    switch ($method) {
        case 'GET':
            if ($id) {
                $result = getStory($db, $id);
                echo json_encode($result ?: ['error' => 'Histoire non trouvée']);
            } else {
                if ($limit) {
                    $result = getLimitStories($db, $limit);
                } else {
                    $result = getAllStories($db);
                }
                echo json_encode($result);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $result = addStory($db, $data['title'], $data['author_id'], $data['description']);
            echo json_encode([
                'success' => $result,
                'message' => 'Histoire créée avec succès'
            ]);
            break;
        case 'PUT':
            if ($id) {
                // Implémentez la mise à jour d'une histoire si nécessaire
                echo json_encode(['error' => 'Non implémenté']);
            } else {
                echo json_encode(['error' => 'ID requis']);
            }
            break;
        case 'DELETE':
            if ($id) {
                // Implémentez la suppression d'une histoire si nécessaire
                echo json_encode(['error' => 'Non implémenté']);
            } else {
                echo json_encode(['error' => 'ID requis']);
            }
            break;
    }
}

function handleParticipationsEndpoint($method, $id, $db) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $result = getParticipations($db, $id);
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'ID de l\'histoire requis']);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            // Implémentez l'ajout d'une participation
            echo json_encode(['error' => 'Non implémenté']);
            break;
        default:
            echo json_encode(['error' => 'Méthode non supportée']);
            break;
    }
}

function handleUsersEndpoint($method, $username, $db) {
    switch ($method) {
        case 'GET':
            if ($username) {
                $result = getUserInfosByUsername($db, $username);
                // Retirer le mot de passe pour des raisons de sécurité
                unset($result['pass']);
                echo json_encode($result ?: ['error' => 'Utilisateur non trouvé']);
            } else {
                echo json_encode(['error' => 'nom utilisateur requis']);
            }
            break;
        default:
            echo json_encode(['error' => 'Méthode non supportée']);
            break;
    }
}