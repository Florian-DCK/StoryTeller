<?php
require_once __DIR__ . '/../models/databaseService.php';
require_once __DIR__ . '/../models/authService.php';

$authService = new AuthService();
$db = new DatabaseService();

$username = isset($_POST['username']) ? $_POST['username'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

$mdpHash = password_hash($password, PASSWORD_BCRYPT);

$result = $authService->logUp($db, $username,$email, $mdpHash);
if ($result) {
    echo "Inscription réussie !";
} else {
    echo "Erreur lors de l'inscription.";
}

$db = new DatabaseService();
$authService = new AuthService();


