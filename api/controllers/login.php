<?php
session_start();
require_once __DIR__ . '/../models/databaseService.php';
require_once __DIR__ . '/../models/authService.php';
require_once __DIR__ . '/../controllers/users.php';

$authService = new AuthService();
$db = new DatabaseService();

$username = isset($_POST['username']) ? $_POST['username'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

$result = $authService->logIn($db, $username, $password); 

if ($result) {
    $userInfos = getUserInfosByUsername($db, $username);
    $_SESSION['userId'] = $userInfos['id'];
    $_SESSION['username'] = $userInfos['username'];
    $_SESSION['email'] = $userInfos['email'];
    
    #rediriger vers la page d'accueil
    header('Location: /');
} else {
    throw new Exception('Login failed'); // Login failed
}

$db = new DatabaseService();
$authService = new AuthService();

