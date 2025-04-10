<?php

require_once __DIR__ . '/../models/databaseService.php';

function getUsername($db, $userId) {
    $query = "SELECT username FROM Users WHERE id = ?";
    $result = $db->QueryParams($query, 's', $userId);
    
    return $result[0]['username'];
}

function getUserId($db, $username) {
    $query = "SELECT id FROM Users WHERE username = ?";
    $result = $db->QueryParams($query, 's', $username);
    
    return $result[0]['id'];
}

function getUserInfosById($db, $userId) {
    $query = "SELECT * FROM Users WHERE id = ?";
    $result = $db->QueryParams($query, 's', $userId);
    
    return $result[0];
}

function getUserInfosByUsername($db, $username) {
    $query = "SELECT * FROM Users WHERE username = ?";
    $result = $db->QueryParams($query, 's', $username);
    
    return $result[0];
}