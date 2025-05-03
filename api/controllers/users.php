<?php

require_once __DIR__ . '/../models/databaseService.php';

function getUsername($db, $userId) {
    $query = "SELECT username FROM Users WHERE id = ?";
    $result = $db->QueryParams($query, 's', $userId);
    
    return !empty($result) ? $result[0]['username'] : null;
}

function getUserId($db, $username) {
    $query = "SELECT id FROM Users WHERE username = ?";
    $result = $db->QueryParams($query, 's', $username);
    
    return !empty($result) ? $result[0]['id'] : null;
}

function getUserInfosById($db, $userId) {
    $query = "SELECT * FROM Users WHERE id = ?";
    $result = $db->QueryParams($query, 's', $userId);
    
    return !empty($result) ? $result[0] : null;
}

function getUserInfosByUsername($db, $username) {
    $query = "SELECT * FROM Users WHERE username = ?";
    $result = $db->QueryParams($query, 's', $username);
    
    return !empty($result) ? $result[0] : null;
}

function getUserAvatar($db, $userId) {
    $query = "SELECT avatar FROM Users WHERE id = ?";
    $result = $db->QueryParams($query, 's', $userId);
    
    return !empty($result) ? $result[0]['avatar'] : null;
}

function changeAvatar($db, $userId, $avatarPath) {
    $query = "UPDATE Users SET avatar = ? WHERE id = ?";
    $result = $db->QueryParams($query, 'ss', $avatarPath, $userId);
    
    return $result;
}

function hasUserLikedStory($db, $userId, $storyId) {
    $query = "SELECT id FROM UserLikes WHERE user_id = ? AND story_id = ?";
    $result = $db->QueryParams($query, 'ss', $userId, $storyId);
    
    return !empty($result);
}

function addUserLike($db, $userId, $storyId) {
    // Vérifier si l'utilisateur a déjà liké
    if (hasUserLikedStory($db, $userId, $storyId)) {
        return false;
    }
    
    // Ajouter le like dans la table de jonction
    $query = "INSERT INTO UserLikes (user_id, story_id) VALUES (?, ?)";
    $result = $db->QueryParams($query, 'ss', $userId, $storyId);
    
    // Incrémenter le compteur de likes dans la table Stories
    if ($result) {
        $queryUpdate = "UPDATE Stories SET likes = likes + 1 WHERE id = ?";
        $db->QueryParams($queryUpdate, 's', $storyId);
    }
    
    return $result;
}

function removeUserLike($db, $userId, $storyId) {
    // Vérifier si l'utilisateur a liké
    if (!hasUserLikedStory($db, $userId, $storyId)) {
        return false;
    }
    
    // Supprimer le like de la table de jonction
    $query = "DELETE FROM UserLikes WHERE user_id = ? AND story_id = ?";
    $result = $db->QueryParams($query, 'ss', $userId, $storyId);
    
    // Décrémenter le compteur de likes dans la table Stories
    if ($result) {
        $queryUpdate = "UPDATE Stories SET likes = GREATEST(likes - 1, 0) WHERE id = ?";
        $db->QueryParams($queryUpdate, 's', $storyId);
    }
    
    return $result;
}

function getUserLikedStories($db, $userId) {
    $query = "SELECT s.* FROM Stories s 
              JOIN UserLikes ul ON s.id = ul.story_id 
              WHERE ul.user_id = ?";
    $result = $db->QueryParams($query, 's', $userId);
    
    return $result;
}