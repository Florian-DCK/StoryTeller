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
    
    $story['themes'] = $themesResult;
    
    return $story;
}

function getStoryByTitle($db, $title) {
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
    
    $story['themes'] = $themesResult;
    
    return $story;
}

function getStoriesByAuthor($db, $author) {
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
    
    $sql = "SELECT DISTINCT Stories.* FROM Stories";
    
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
    
    if ($searchQuery) {
        $sql .= " LEFT JOIN Participations ON Stories.id = Participations.story_id";
    }

    $sql .= " WHERE 1=1";
    
    if ($searchQuery) {
        $conditions[] = "(Stories.title LIKE ? OR Participations.content LIKE ?)";
        $params[] = "%$searchQuery%";
        $params[] = "%$searchQuery%";
        $types .= 'ss';
    }
    
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }
    
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
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
        $types .= 'i';
    }
    
    $result = $db->QueryParams($sql, $types, ...$params);
    
    foreach ($result as &$story) {
        $queryThemes = "SELECT Themes.id, Themes.name FROM Themes 
                        JOIN StoriesThemes ON Themes.id = StoriesThemes.theme_id 
                        WHERE StoriesThemes.story_id = ?";
        $themesResult = $db->QueryParams($queryThemes, 's', $story['id']);
        $story['themes'] = $themesResult;
    }
    
    return $result;
}

function linkThemes($db, $storyId, $themes) {
    foreach ($themes as $theme) {
        $query = "INSERT INTO StoriesThemes (story_id, theme_id) VALUES (?, ?)";
        $db->QueryParams($query, 'ss', $storyId, $theme);
    }
}

function getNumberOfStories($db, $userId) {
    $query = 'SELECT COUNT(*) as count FROM Stories WHERE author = ?';
    $result = $db->QueryParams($query, 's', $userId);
    
    return $result[0]['count'];
}
