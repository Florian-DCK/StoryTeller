<?php
require_once __DIR__ . '/../models/databaseService.php';

class AuthService {

    public function logUp($db, $username, $email, $password) {
        $query = "SELECT * FROM Users WHERE username = ?";
        $result = $db->QueryParams($query, 's', $username);

        if (count($result) > 0) {
            throw new Exception('User already exists'); // User already exists
        }

        $query = "INSERT INTO Users (username, email, pass) VALUES (?, ?, ?)";
        $result = $db->QueryParams($query, 'sss', $username, $email, $password);

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