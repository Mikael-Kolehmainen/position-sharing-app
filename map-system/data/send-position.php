<?php
require './../../required-files/constants.php';
require './../../autoloader.php';

if (isset($_GET[LAT]) && isset($_GET[LNG]) && isset($_GET[GROUPCODE])) {
    session_start();
    $newLat = filter_input(INPUT_GET, LAT, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $newLng = filter_input(INPUT_GET, LNG, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if (isset($_SESSION[UNIQUEID])) {
        $uniqueID = $_SESSION[UNIQUEID];
        $positionsRowID = getPositionsRowID($uniqueID);

        updatePositionInDatabase($newLat, $newLng, $positionsRowID);
    } else {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        $uniqueID = getUniqueID();
        $_SESSION[UNIQUEID] = $uniqueID;
        $initials = $_SESSION[INITIALS];
        $color = $_SESSION[COLOR];

        $positionsRowID = insertPositionToDatabase($newLat, $newLng);

        insertUserToDatabase($groupCode, $positionsRowID, $uniqueID, $initials, $color);
    }
}

function getPositionsRowID($uniqueID)
{
    $user = new User();
    $user->uniqueId = $uniqueID;
    $positionsRowID = $user->getPositionsRowID();

    return $positionsRowID;
}

function updatePositionInDatabase($lat, $lng, $positionsRowID)
{
    $position = new Position($lat, $lng);
    $position->id = $positionsRowID;
    $position->save();
}

function insertPositionToDatabase($lat, $lng)
{
    $position = new Position($lat, $lng);
    $position->save();
    $positionsRowID = $position->id;

    return $positionsRowID;
}

function insertUserToDatabase($groupCode, $positionsRowID, $uniqueID, $initials, $color)
{
    $user = new User();
    $user->groupCode = $groupCode;
    $user->positionsId = $positionsRowID;
    $user->uniqueId = $uniqueID;
    $user->initials = $initials;
    $user->color = $color;
    $user->save();
}

function getUniqueID() 
{
    require './../../required-files/random-string.php';

    $uniqueID = getRandomString(10);
    $uniqueIDs = getUsersUniqueIDsFromDatabase();

    for ($i = 0; $i < count($uniqueIDs); $i++) {
        if ($uniqueID == $uniqueIDs[$i][UNIQUEID]) {
            $uniqueID = getUniqueID();
        }
    }
    
    return $uniqueID;
}

function getUsersUniqueIDsFromDatabase()
{
    $user = new User();
    return $user->getUniqueIDs();
}