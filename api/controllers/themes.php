<?php
function getAllThemes($db) {
    $query = "SELECT * FROM Themes ORDER BY name ASC";
    return $db->query($query);
}