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

