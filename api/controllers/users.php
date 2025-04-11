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