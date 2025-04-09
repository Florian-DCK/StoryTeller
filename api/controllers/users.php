<?php

require_once __DIR__ . '/../models/databaseService.php';

function getUsername($db, $userId) {
    $query = "SELECT username FROM Users WHERE id = ?";
    $result = $db->QueryParams($query, 's', $userId);
    
    return $result[0]['username'];
}