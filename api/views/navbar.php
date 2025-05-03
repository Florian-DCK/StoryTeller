<?php
require __DIR__ . '/../../vendor/autoload.php';

$mustache = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../templates/partials')
]);

$fmt = datefmt_create(
    'fr_FR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::FULL,
    'Europe/Paris',
    IntlDateFormatter::GREGORIAN,
    'dd MMMM yyyy'
);

$url = $_SERVER['REQUEST_URI'];
$isAuthRoute = preg_match('/\/auth/', $url) ? false : true;
$navbarData = [
    "date" => datefmt_format($fmt, time()),
    "isAuthRoute" => $isAuthRoute,
    "isConnected" => isset($_SESSION['userId']),
    "username" => isset($_SESSION['username']) ? $_SESSION['username'] : null,
    "avatar" => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : null,
    "isAdmin" => isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : null,
    "userId" => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
];

echo $mustache->render('navbar', $navbarData);
