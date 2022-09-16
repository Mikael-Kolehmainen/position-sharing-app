<?php
require './../required-files/dbHandler.php';
require './../db/Position.php';
require './../db/User.php';
require './../required-files/constants.php';

session_start();

if (isset($_GET['lat']) && isset($_GET['lng']) && isset($_GET[GROUPCODE])) {
    $newLat = filter_input(INPUT_GET, 'lat', FILTER_DEFAULT);
    $newLng = filter_input(INPUT_GET, 'lng', FILTER_DEFAULT);

    if (isset($_SESSION[UNIQUEID])) {
        $uniqueID = $_SESSION[UNIQUEID];

        $user = new User($uniqueID);
        $positionRowId = $user->getPositionRowID();

        $position = new Position($newLat, $newLng);
        $position->id = $positionRowId;
        $position->save();

        updatePositionInDatabase($newLat, $newLng, $positionRowId, $uniqueID);
    } else {
        $uniqueID = getUniqueID();
        $initials = $_SESSION[INITIALS];
        $color = $_SESSION[COLOR];
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        $_SESSION[UNIQUEID] = $uniqueID;

        $position = new Position($newLat, $newLng);
        $position->save();
        $positionRowId = $position->id;

        insertPositionToDatabase($newLat, $newLng, $positionRowId, $uniqueID, $initials, $color, $groupCode);
    }
}

function updatePositionInDatabase($lat, $lng, $positionId, $uniqueID)
{
    dbHandler::query("UPDATE users SET lat = '$lat', lng = '$lng', positions_id = '$positionId' WHERE ".UNIQUEID." = '$uniqueID'");
}

function insertPositionToDatabase($lat, $lng, $positionId, $uniqueID, $initials, $color, $groupCode) 
{
    dbHandler::query("INSERT INTO users (lat, lng, positions_id, ".UNIQUEID.", ".INITIALS.", ".COLOR.", ".GROUPS_GROUPCODE.") VALUES ('$lat', '$lng', '$positionId', '$uniqueID', '$initials', '$color', '$groupCode')");
}

function getUniqueID() 
{
    require './../required-files/random-string.php';

    $uniqueID = getRandomString(10);
    $result = selectPositionsFromDatabase();

    if (mysqli_num_rows($result) > 0) {
        for ($i = 0; $i < mysqli_num_rows($result); $i++) {
            $row = mysqli_fetch_assoc($result);

            if ($uniqueID == $row[UNIQUEID]) {
                $uniqueID = getUniqueID();
            }
        }
    }
    
    return $uniqueID;
}

function selectPositionsFromDatabase()
{
    return dbHandler::query("SELECT id, ".UNIQUEID." FROM users");
}