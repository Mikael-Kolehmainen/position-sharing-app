<?php
    require './../required-files/dbHandler.php';

    session_start();
    if (isset($_GET['pos'])) 
    {
        if (isset($_SESSION['uniqueID'])) 
        {
            $result = selectPositions();

            if (mysqli_num_rows($result) > 0) 
            {
                for ($i = 0; $i < mysqli_num_rows($result); $i++) 
                {
                    $row = mysqli_fetch_assoc($result);
                    if ($_SESSION['uniqueID'] == $row['uniqueID']) 
                    {
                        $newPosition = filter_input(INPUT_GET, 'pos', FILTER_DEFAULT);;
                        $positionID = $row['id'];
                        updatePosition($newPosition, $positionID);
                    }
                }
            }
        } 
        else 
        {
            $position = filter_input(INPUT_GET, 'pos', FILTER_DEFAULT);;
            $uniqueID = getUniqueID();
            $initials = $_SESSION['initials'];
            $color = $_SESSION['color'];
            $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_DEFAULT);;

            $_SESSION['uniqueID'] = $uniqueID;

            addPosition($position, $uniqueID, $initials, $color, $groupCode);
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

            addGoal($startPosition, $goalPosition, $waypoints, $groupCode);
        }
    }

    function addPosition($position, $uniqueID, $initials, $color, $groupCode) 
    {
        dbHandler::query("INSERT INTO positions (position, uniqueID, initials, color, groups_groupcode) VALUES ('$position', '$uniqueID', '$initials', '$color', '$groupCode')");
	}

    function updatePosition($position, $id)
    {
        dbHandler::query("UPDATE positions SET position = '$position' WHERE id = '$id'");
    }

    function selectPositions()
    {
        return dbHandler::query("SELECT id, uniqueID FROM positions");
    }

    function addGoal($startPosition, $goalPosition, $waypoints, $groupCode)
    {
        dbHandler::query("INSERT INTO goals (startposition, goalposition, waypoints, groups_groupcode) VALUES ('$startPosition', '$goalPosition', '$waypoints', '$groupCode')");
    }

    // Checks if the unique id is actually unique
    function getUniqueID() 
    {
        require './../required-files/random-string.php';

        $uniqueID = getRandomString(10);
        $result = selectPositions();

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
?>