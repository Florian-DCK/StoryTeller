<?php

require_once __DIR__ . '/../models/databaseService.php';

function getAllStories($db) {
    $query = "SELECT * FROM Stories ORDER BY creation_date DESC";
    $result = $db->Query($query);
    
    return $result;
}

function addLike($db, $storyId) {
    $query = "UPDATE Stories SET likes = likes + 1 WHERE id = ?";
    $result = $db->QueryParams($query, 's', $storyId);
    
    return $result;
}

function removeLike($db, $storyId) {
    $query = "UPDATE Stories SET likes = GREATEST(likes - 1, 0) WHERE id = ?";
    $result = $db->QueryParams($query, 's', $storyId);
    
    return $result;
}

function addStory($db, $title, $authorId, $description) {
    $query = "INSERT INTO Stories (title, author_id, description) VALUES (?, ?, ?)";
    $result = $db->QueryParams($query, 'sis', $title, $authorId, $description);
    
    return $result;
}

function getLimitStories($db, $limit) {
    $query = "SELECT * FROM Stories ORDER BY creation_date DESC LIMIT ?";
    $result = $db->QueryParams($query, 'i', $limit);
    
    return $result;
}

function getStory($db, $storyId) {
    $query = "SELECT * FROM Stories WHERE id = ?";
    $result = $db->QueryParams($query, 's', $storyId);
    
    return $result;
}

function getStoryByTitle($db, $title) {
    $query = "SELECT * FROM Stories WHERE title = ?";
    $result = $db->QueryParams($query, 's', $title);
    
    return $result;
}

function getStoryByAuthor($db, $author) {
    $query = "SELECT * FROM Stories WHERE author = ?";
    $result = $db->QueryParams($query, 's', $author);
    
    return $result;
}

function searchStories($db, $searchQuery, $themes, $sortBy, $limit = null) {
    $conditions = [];
    $params = [];
    $types = '';
    
    // Construction de la requête de base
    $sql = "SELECT DISTINCT Stories.* FROM Stories";
    
    // Ajouter les JOINs nécessaires pour les thèmes si demandé
    if ($themes && is_array($themes) && !empty($themes)) {
        $sql .= " JOIN StoriesThemes ON Stories.id = StoriesThemes.story_id 
                  JOIN Themes ON StoriesThemes.theme_id = Themes.id";
        
        $themeConditions = [];
        foreach ($themes as $theme) {
            $themeConditions[] = "Themes.name = ?";
            $params[] = $theme;
            $types .= 's';
        }
        
        if (!empty($themeConditions)) {
            $conditions[] = "(" . implode(" OR ", $themeConditions) . ")";
        }
    }
    
    // Ajouter JOIN pour Participations si recherche par texte
    if ($searchQuery) {
        $sql .= " LEFT JOIN Participations ON Stories.id = Participations.story_id";
    }

    // Ajouter WHERE initial
    $sql .= " WHERE 1=1";
    
    // Ajouter la condition de recherche par texte
    if ($searchQuery) {
        $conditions[] = "(Stories.title LIKE ? OR Participations.content LIKE ?)";
        $params[] = "%$searchQuery%";
        $params[] = "%$searchQuery%";
        $types .= 'ss';
    }
    
    // Ajouter toutes les conditions à la requête
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
    
    // Ajouter le tri
    switch ($sortBy) {
        case 'mostLikes':
            $sql .= " ORDER BY likes DESC";
            break;
        case 'mostRecent':
            $sql .= " ORDER BY creation_date DESC";
            break;
        case 'mostParticipations':
            // Si vous avez une façon de compter les participations
            $sql .= " ORDER BY (SELECT COUNT(*) FROM Participations WHERE Participations.story_id = Stories.id) DESC";
            break;
        default:
            $sql .= " ORDER BY creation_date DESC";
    }
    
    // Ajouter la limite
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
        $types .= 'i';
    }
    
    // Exécuter la requête
    $result = $db->QueryParams($sql, $types, ...$params);
    
    return $result;
}

