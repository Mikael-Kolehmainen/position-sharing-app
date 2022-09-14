<?php
    require './../required-files/dbHandler.php';

    session_start();
    if (isset($_GET['pos'])) 
    {
        $newPosition = filter_input(INPUT_GET, 'pos', FILTER_DEFAULT);

        if (isset($_SESSION['uniqueID'])) 
        {
            $uniqueID = $_SESSION['uniqueID'];

            updatePositionInDatabase($newPosition, $uniqueID);
        } 
        else 
        {
            $uniqueID = getUniqueID();
            $initials = $_SESSION['initials'];
            $color = $_SESSION['color'];
            $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_DEFAULT);;

            $_SESSION['uniqueID'] = $uniqueID;

            insertPositionToDatabase($newPosition, $uniqueID, $initials, $color, $groupCode);
        }
    } 
    else if (isset($_GET['goalamount'])) 
    {
        $amountOfGoals = filter_input(INPUT_GET, 'goalamount', FILTER_DEFAULT);
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_DEFAULT);

        for ($i = 0; $i < $amountOfGoals; $i++)
        {
            $startKey = 'startpos'.$i;
            $startPosition = filter_input(INPUT_GET, $startKey, FILTER_DEFAULT);

            $goalKey = 'goalpos'.$i;
            $goalPosition = filter_input(INPUT_GET, $goalKey, FILTER_DEFAULT);

            $waypointID = 0;
            $waypointKey = 'waypoint'.$i.'-'.$waypointID;
            $waypoints = "";

            while (isset($_GET[$waypointKey]))
            {
                $waypoints .= filter_input(INPUT_GET, $waypointKey, FILTER_DEFAULT);

                $waypointID = $waypointID + 1;
                $waypointKey = 'waypoint'.$i.'-'.$waypointID;
            }

            insertGoalToDatabase($startPosition, $goalPosition, $waypoints, $groupCode);
        }
    }

    function insertPositionToDatabase($position, $uniqueID, $initials, $color, $groupCode) 
    {
        dbHandler::query("INSERT INTO positions (position, uniqueID, initials, color, groups_groupcode) VALUES ('$position', '$uniqueID', '$initials', '$color', '$groupCode')");
	}

    function updatePositionInDatabase($position, $uniqueID)
    {
        dbHandler::query("UPDATE positions SET position = '$position' WHERE uniqueID = '$uniqueID'");
    }

    function insertGoalToDatabase($startPosition, $goalPosition, $waypoints, $groupCode)
    {
        dbHandler::query("INSERT INTO goals (startposition, goalposition, waypoints, groups_groupcode) VALUES ('$startPosition', '$goalPosition', '$waypoints', '$groupCode')");
    }

    function getUniqueID() 
    {
        require './../required-files/random-string.php';

        $uniqueID = getRandomString(10);
        $result = selectPositionsFromDatabase();

        if (mysqli_num_rows($result) > 0) 
        {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) 
            {
                $row = mysqli_fetch_assoc($result);

                if ($uniqueID == $row['uniqueID']) 
                {
                    $uniqueID = getUniqueID();
                }
            }
        }
        
        return $uniqueID;
    }

    function selectPositionsFromDatabase()
    {
        return dbHandler::query("SELECT id, uniqueID FROM positions");
    }
?>