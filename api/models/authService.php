<?php
require_once __DIR__ . '/../models/databaseService.php';
require_once __DIR__ . '/../models/imageService.php';

class AuthService {

    public function logUp($db, $username, $email, $bio, $password) {
        $query = "SELECT * FROM Users WHERE username = ?";
        $result = $db->QueryParams($query, 's', $username);

        if (count($result) > 0) {
            throw new Exception('User already exists'); // User already exists
        }

        $query = "SELECT * FROM Users WHERE email = ?";
        $result = $db->QueryParams($query, 's', $email);

        if (count($result) > 0) {
            throw new Exception('Email already exists'); // Email already exists
        }

        $query = "INSERT INTO Users (username, email, pass, bio) VALUES (?, ?, ?, ? )";
        $result = $db->QueryParams($query, 'ssss', $username, $email, $password, $bio);

        return $result;
    }

    public function logIn($db, $username, $password) {
        $query = "SELECT * FROM Users WHERE username = ?";
        $result = $db->QueryParams($query, 's', $username);
        
        if (count($result) === 0) {
            throw new Exception('User not found'); // User not found
        }
        
        $user = $result[0];
        
        if (password_verify($password, $user['pass'])) {
            return true; // Password is correct
        } else {
            throw new Exception('Password incorrect'); // Password is incorrect
        }
    }
}