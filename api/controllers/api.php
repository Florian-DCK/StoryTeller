<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

// Connexion à la base de données avec notre service personnalisé
require_once __DIR__ . '/../models/databaseService.php';
require_once __DIR__ . '/../models/authService.php';
require_once __DIR__ . '/stories.php';
require_once __DIR__ . '/participations.php';
require_once __DIR__ . '/users.php';
require_once __DIR__ . '/themes.php';

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
$search = isset($query_params['query']) ? $query_params['query'] : null;
$themes = isset($query_params['themes']) ? explode(",", $query_params['themes']) : null;
$sortBy = isset($query_params['sortBy']) ? $query_params['sortBy'] : null;

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
    case 'themes':
        handleThemesEndpoint($method, $id, $db);
        break;
    default:
        echo json_encode(['error' => 'Ressource non reconnue']);
        break;
}

function handleStoriesEndpoint($method, $id, $db) {
    global $limit, $search, $themes, $sortBy;
    switch ($method) {
        case 'GET':
            if ($id) {
                $result = getStory($db, $id);
                echo json_encode($result ?: ['error' => 'Histoire non trouvée']);
            } else {
                // Vérifier si une recherche est demandée
                if ($search || $themes || $sortBy) {
                    $result = searchStories($db, $search, $themes, $sortBy, $limit);
                } else if ($limit) {
                    $result = getLimitStories($db, $limit);
                } else {
                    $result = getAllStories($db);
                }
                echo json_encode($result);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $result = addStory($db, $data['title'], $data['author_id']);
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
    global $limit;
    switch ($method) {
        case 'GET':
            if ($id) {
                if ($limit) {
                    $result = getLimitParticipations($db, $id, $limit);
                } else {
                    $result = getParticipations($db, $id);
                }
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'ID de l\'histoire requis']);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            // Vérifier que les données nécessaires sont présentes
            if (!isset($data['story_id']) || !isset($data['user_id']) || !isset($data['content'])) {
                echo json_encode(['error' => 'Données incomplètes', 'status' => false]);
                break;
            }
            
            $result = addParticipation($db, $data['story_id'], $data['user_id'], $data['content']);
            
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
            break;
        default:
            echo json_encode(['error' => 'Méthode non supportée']);
            break;
    }
}

function handleUsersEndpoint($method, $id, $db) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $result = getUserInfosById($db, $id);
                // Retirer le mot de passe pour des raisons de sécurité
                unset($result['pass']);
                echo json_encode($result ?: ['error' => 'Utilisateur non trouvé']);
            } else {
                echo json_encode(['error' => 'ID utilisateur requis']);
            }
            break;
        default:
            echo json_encode(['error' => 'Méthode non supportée']);
            break;
    }
}

function handleThemesEndpoint($method, $id, $db) {
    switch ($method) {
        case 'GET':
            $result = getAllThemes($db);
            echo json_encode($result);
            break;
        default:
            echo json_encode(['error' => 'Méthode non supportée']);
            break;
    }
}