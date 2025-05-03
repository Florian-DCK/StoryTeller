<?php
session_start();
header('Content-Type: application/json');

$isLoggedIn = isset($_SESSION['userId']) && !empty($_SESSION['userId']);

echo json_encode([
    'isLoggedIn' => $isLoggedIn,
    'userId' => $isLoggedIn ? $_SESSION['userId'] : null
]);