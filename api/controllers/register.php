<?php
require_once __DIR__ . '/../models/databaseService.php';
require_once __DIR__ . '/../models/authService.php';
require_once __DIR__ . '/../controllers/users.php';

$authService = new AuthService();
$db = new DatabaseService();

$username = isset($_POST['username']) ? $_POST['username'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$bio = isset($_POST['bio']) ? $_POST['bio'] : null;
$avatar = isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE ? $_FILES['avatar'] : null;


if ($avatar !== null && !checkValidity($avatar)) {
    echo "Erreur lors de l'upload de l'image.";
    exit;
}


$mdpHash = password_hash($password, PASSWORD_BCRYPT);

$result = $authService->logUp($db, $username,$email, $bio, $mdpHash);
if ($result) {
    $userId = getUserId($db, $username);
    
    if ($avatar !== null) {
        $avatarPath = saveAvatar($avatar, __DIR__ . '/../ressources/avatars/', $userId);
        changeAvatar($db, $userId, $avatarPath);
    }
    
    header('Location: /auth?success=1');
} else {
    echo "Erreur lors de l'inscription.";
}


