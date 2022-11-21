<?php
require './../../required-files/constants.php';
require './../../autoloader.php';

if (isset($_GET[LAT]) && isset($_GET[LNG]) && isset($_GET[GROUPCODE])) {
    session_start();
    $newLat = filter_input(INPUT_GET, LAT, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $newLng = filter_input(INPUT_GET, LNG, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

    if (isset($_SESSION[USER_ROW_ID]) && checkIfRowIdExistsInDatabase($groupCode)) {
        $id = $_SESSION[USER_ROW_ID];
        $positionsRowID = getPositionsRowID($id);

        updatePositionInDatabase($newLat, $newLng, $positionsRowID);
    } else {
        $initials = $_SESSION[INITIALS];
        $color = $_SESSION[COLOR];

        $positionsRowID = insertPositionToDatabase($newLat, $newLng);

        insertUserToDatabase($groupCode, $positionsRowID, $initials, $color);
    }
}

function checkIfRowIdExistsInDatabase($groupCode)
{
    $user = new User();
    $user->groupCode = $groupCode;
    $IDs = $user->getIDs();

    for ($i = 0; $i < count($IDs); $i++) {
        if ($IDs[$i]["id"] == $_SESSION[USER_ROW_ID]) {
            return true;
        }
    }

    return false;
}

function getPositionsRowID($id)
{
    $user = new User();
    $user->id = $id;
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

function insertUserToDatabase($groupCode, $positionsRowID, $initials, $color)
{
    $user = new User();
    if (isset($_SESSION[USER_ROW_ID])) {
        $user->id = $_SESSION[USER_ROW_ID];
    }
    $user->groupCode = $groupCode;
    $user->positionsId = $positionsRowID;
    $user->initials = $initials;
    $user->color = $color;
    $user->save();

    $_SESSION[USER_ROW_ID] = $user->id;
}