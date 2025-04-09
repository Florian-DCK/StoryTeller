<?php

require_once __DIR__ . '/../models/databaseService.php';

function getAllStories($db) {
    $query = "SELECT * FROM Stories ORDER BY creation_date DESC";
    $result = $db->Query($query);
    
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

