<?php

require_once __DIR__ . '/../models/databaseService.php';

function getParticipations($db, $storyId) {
    $query = 'SELECT * FROM Participations WHERE story_id = ? ORDER BY creationDate DESC';
    $result = $db->QueryParams($query, 's', $storyId);
    
    return $result;
}
