<?php

require_once __DIR__ . '/../models/databaseService.php';

function fetchTickets($db)
{
    //fetch all tickets
    $query = "SELECT * FROM Tickets";
    $result = $db->Query($query);

    return $result;
}
