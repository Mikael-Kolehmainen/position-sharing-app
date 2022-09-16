<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';

    session_start();

    if (isset($_GET['lat']) && isset($_GET['lng'])) {
        $newLat = filter_input(INPUT_GET, 'lat', FILTER_DEFAULT);
        $newLng = filter_input(INPUT_GET, 'lng', FILTER_DEFAULT);

        if (isset($_SESSION[UNIQUEID])) {
            $uniqueID = $_SESSION[UNIQUEID];

            updatePositionInDatabase($newLat, $newLng, $uniqueID);
        } else {
            $uniqueID = getUniqueID();
            $initials = $_SESSION[INITIALS];
            $color = $_SESSION[COLOR];
            $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

            $_SESSION[UNIQUEID] = $uniqueID;

            insertPositionToDatabase($newLat, $newLng, $uniqueID, $initials, $color, $groupCode);
        }
    } else if (isset($_GET[GOALAMOUNT])) {
        $amountOfGoals = filter_input(INPUT_GET, GOALAMOUNT, FILTER_DEFAULT);
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        insertGoalsToDatabase($amountOfGoals, $groupCode);
    } else {
        header("LOCATION: ./../index.php");
    }

    function updatePositionInDatabase($lat, $lng, $uniqueID)
    {
        dbHandler::query("UPDATE ".POSITIONS." SET lat = '$lat', lng = '$lng' WHERE ".UNIQUEID." = '$uniqueID'");
    }

    function insertPositionToDatabase($lat, $lng, $uniqueID, $initials, $color, $groupCode) 
    {
        dbHandler::query("INSERT INTO ".POSITIONS." (lat, lng, ".UNIQUEID.", ".INITIALS.", ".COLOR.", ".GROUPS_GROUPCODE.") VALUES ('$lat', '$lng', '$uniqueID', '$initials', '$color', '$groupCode')");
	}

    function insertGoalsToDatabase($amountOfGoals, $groupCode)
    {
        for ($userIndex = 0; $userIndex < $amountOfGoals; $userIndex++) {
            $startKey = 'startpos'.$userIndex;
            $startPosition = filter_input(INPUT_GET, $startKey, FILTER_DEFAULT);

            $goalKey = 'goalpos'.$userIndex;
            $goalPosition = filter_input(INPUT_GET, $goalKey, FILTER_DEFAULT);

            $waypointIndex = 0;
            $waypointKey = WAYPOINT.$userIndex.'-'.$waypointIndex;
            $waypoints = "";

            $goalIDKey = 'goalid'.$userIndex;
            $goalID = filter_input(INPUT_GET, $goalIDKey, FILTER_DEFAULT);

            while (isset($_GET[$waypointKey])) {
                $waypoints .= filter_input(INPUT_GET, $waypointKey, FILTER_DEFAULT);

                $waypointIndex = $waypointIndex + 1;
                $waypointKey = WAYPOINT.$userIndex.'-'.$waypointIndex;
            }

            insertGoalToDatabase($startPosition, $goalPosition, $waypoints, $goalID, $groupCode);
        }
    }

    function insertGoalToDatabase($startPosition, $goalPosition, $waypoints, $goalID, $groupCode)
    {
        dbHandler::query("INSERT INTO ".GOALS." (".STARTPOSITION.", ".GOALPOSITION.", ".WAYPOINTS.", ".GOALID.", ".GROUPS_GROUPCODE.") VALUES ('$startPosition', '$goalPosition', '$waypoints', '$goalID', '$groupCode')");
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
        return dbHandler::query("SELECT id, ".UNIQUEID." FROM ".POSITIONS);
    }