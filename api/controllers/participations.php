<?php

require_once __DIR__ . '/../models/databaseService.php';

function getParticipations($db, $storyId) {
    $query = 'SELECT * FROM Participations WHERE story_id = ? ORDER BY creationDate ';
    $result = $db->QueryParams($query, 's', $storyId);
    
    return $result;
}

function getLimitParticipations($db, $storyId, $limit) {
    $query = 'SELECT * FROM Participations WHERE story_id = ? ORDER BY creationDate LIMIT ?';
    $result = $db->QueryParams($query, 'si', $storyId, $limit);
    
    return $result;
}

function addParticipation($db, $storyId, $userId, $content) {
    $query = 'INSERT INTO Participations (story_id, user_id, content) VALUES (?, ?, ?)';
    $result = $db->QueryParams($query, 'sss', $storyId, $userId, $content);
    
    return $result;
}

function getNumberOfParticipations($db, $userId) {
    $query = 'SELECT COUNT(*) as count FROM Participations WHERE user_id = ?';
    $result = $db->QueryParams($query, 's', $userId);
    
    return $result[0]['count'];
}
