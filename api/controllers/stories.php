<?php

require_once __DIR__ . '/../models/databaseService.php';

function getAllStories($db) {
    $query = "SELECT * FROM Stories ORDER BY creation_date DESC";
    $result = $db->Query($query);
    
    // Récupérer les thèmes pour chaque histoire
    foreach ($result as &$story) {
        $queryThemes = "SELECT Themes.id, Themes.name FROM Themes 
                        JOIN StoriesThemes ON Themes.id = StoriesThemes.theme_id 
                        WHERE StoriesThemes.story_id = ?";
        $themesResult = $db->QueryParams($queryThemes, 's', $story['id']);
        $story['themes'] = $themesResult;
    }
    
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

function addStory($db, $title, $authorId) {
    $query = "INSERT INTO Stories (title, author) VALUES (?, ?)";
    $result = $db->QueryParams($query, 'ss', $title, $authorId);
    
    return $result;
}

function getLimitStories($db, $limit) {
    $query = "SELECT * FROM Stories ORDER BY creation_date DESC LIMIT ?";
    $result = $db->QueryParams($query, 'i', $limit);
    
    // Récupérer les thèmes pour chaque histoire
    foreach ($result as &$story) {
        $queryThemes = "SELECT Themes.id, Themes.name FROM Themes 
                        JOIN StoriesThemes ON Themes.id = StoriesThemes.theme_id 
                        WHERE StoriesThemes.story_id = ?";
        $themesResult = $db->QueryParams($queryThemes, 's', $story['id']);
        $story['themes'] = $themesResult;
    }
    
    return $result;
}

function getStory($db, $storyId) {
    // Récupérer d'abord les informations de base de l'histoire
    $queryStory = "SELECT * FROM Stories WHERE id = ?";
    $storyResult = $db->QueryParams($queryStory, 's', $storyId);
    
    if (empty($storyResult)) {
        return null;
    }
    
    $story = $storyResult[0];
    
    // Récupérer les thèmes associés
    $queryThemes = "SELECT Themes.id, Themes.name FROM Themes 
                    JOIN StoriesThemes ON Themes.id = StoriesThemes.theme_id 
                    WHERE StoriesThemes.story_id = ?";
    $themesResult = $db->QueryParams($queryThemes, 's', $storyId);
    
    // Ajouter les thèmes à l'histoire
    $story['themes'] = $themesResult;
    
    return $story;
}

function getStoryByTitle($db, $title) {
    // Récupérer d'abord les informations de base de l'histoire
    $queryStory = "SELECT * FROM Stories WHERE title = ?";
    $storyResult = $db->QueryParams($queryStory, 's', $title);
    
    if (empty($storyResult)) {
        return null;
    }
    
    $story = $storyResult[0];
    
    // Récupérer les thèmes associés
    $queryThemes = "SELECT Themes.id, Themes.name FROM Themes 
                    JOIN StoriesThemes ON Themes.id = StoriesThemes.theme_id 
                    WHERE StoriesThemes.story_id = ?";
    $themesResult = $db->QueryParams($queryThemes, 's', $story['id']);
    
    // Ajouter les thèmes à l'histoire
    $story['themes'] = $themesResult;
    
    return $story;
}

function getStoryByAuthor($db, $author) {
    $query = "SELECT * FROM Stories WHERE author = ?";
    $result = $db->QueryParams($query, 's', $author);
    
    // Récupérer les thèmes pour chaque histoire
    foreach ($result as &$story) {
        $queryThemes = "SELECT Themes.id, Themes.name FROM Themes 
                        JOIN StoriesThemes ON Themes.id = StoriesThemes.theme_id 
                        WHERE StoriesThemes.story_id = ?";
        $themesResult = $db->QueryParams($queryThemes, 's', $story['id']);
        $story['themes'] = $themesResult;
    }
    
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
        $sql .= "   JOIN StoriesThemes ON Stories.id = StoriesThemes.story_id 
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
    
    // Récupérer les thèmes pour chaque histoire
    foreach ($result as &$story) {
        $queryThemes = "SELECT Themes.id, Themes.name FROM Themes 
                        JOIN StoriesThemes ON Themes.id = StoriesThemes.theme_id 
                        WHERE StoriesThemes.story_id = ?";
        $themesResult = $db->QueryParams($queryThemes, 's', $story['id']);
        $story['themes'] = $themesResult;
    }
    
    return $result;
}

