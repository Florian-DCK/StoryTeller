<?php

require_once __DIR__ . '/../models/databaseService.php';

$db = new DatabaseService();

function fetchTotalUsers($db)
{
    $query = "SELECT COUNT(*) FROM Users";
    $result = $db->Query($query);

    return $result[0]['COUNT(*)'];
}

function fetchTotalPosts($db)
{
    $query = "SELECT COUNT(*) FROM Stories";
    $result = $db->Query($query);

    return $result[0]['COUNT(*)'];
}

function fetchAvgUsers($db)
{
    $query = "
        SELECT FLOOR(COUNT(DISTINCT Users.id) / DAY(LAST_DAY(CURDATE()))) AS avgUsers
        FROM Users
        JOIN Stories ON Users.id = Stories.author
        WHERE MONTH(Stories.creation_date) = MONTH(CURDATE()) AND YEAR(Stories.creation_date) = YEAR(CURDATE())
    ";
    $result = $db->Query($query);

    return $result[0]['avgUsers'];
}
