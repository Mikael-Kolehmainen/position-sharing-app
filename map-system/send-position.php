<?php
require './../required-files/dbHandler.php';
require './../db/Position.php';
require './../db/User.php';
require './../required-files/constants.php';

if (isset($_GET['lat']) && isset($_GET['lng']) && isset($_GET[GROUPCODE])) {
    session_start();
    $newLat = filter_input(INPUT_GET, 'lat', FILTER_DEFAULT);
    $newLng = filter_input(INPUT_GET, 'lng', FILTER_DEFAULT);

    if (isset($_SESSION[UNIQUEID])) {
        $uniqueID = $_SESSION[UNIQUEID];
        $positionRowId = getPositionRowID($uniqueID);

        updatePositionInDatabase($newLat, $newLng, $positionRowId);
    } else {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        $uniqueID = getUniqueID();
        $_SESSION[UNIQUEID] = $uniqueID;
        $initials = $_SESSION[INITIALS];
        $color = $_SESSION[COLOR];

        $positionRowId = insertPositionToDatabase($newLat, $newLng);

        insertUserToDatabase($groupCode, $positionRowId, $uniqueID, $initials, $color);
    }
}

function getPositionRowID($uniqueID)
{
    $user = new User();
    $user->uniqueId = $uniqueID;
    $positionRowId = $user->getPositionRowID();

    return $positionRowId;
}

function updatePositionInDatabase($lat, $lng, $positionRowId)
{
    $position = new Position($lat, $lng);
    $position->id = $positionRowId;
    $position->save();
}

function insertPositionToDatabase($lat, $lng)
{
    $position = new Position($lat, $lng);
    $position->save();
    $positionRowId = $position->id;

    return $positionRowId;
}

function insertUserToDatabase($groupCode, $positionRowId, $uniqueID, $initials, $color)
{
    $user = new User();
    $user->groupCode = $groupCode;
    $user->positionsId = $positionRowId;
    $user->uniqueId = $uniqueID;
    $user->initials = $initials;
    $user->color = $color;
    $user->save();
}

function getUniqueID() 
{
    require './../required-files/random-string.php';

    $uniqueID = getRandomString(10);
    $uniqueIDs = getUsersUniqueIDsFromDatabase();

    for ($i = 0; $i < count($uniqueIDs); $i++) {
        if ($uniqueID == $uniqueIDs[$i]['uniqueID']) {
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