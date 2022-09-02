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
                        $newPosition = $_GET['pos'];
                        $positionID = $row['id'];
                        updatePosition($newPosition, $positionID);
                    }
                }
            }
        } 
        else 
        {
            $position = $_GET['pos'];
            $uniqueID = getUniqueID();
            $initials = $_SESSION['initials'];
            $color = $_SESSION['color'];
            $groupCode = $_GET['groupcode'];

            $_SESSION['uniqueID'] = $uniqueID;

            addPosition($position, $uniqueID, $initials, $color, $groupCode);
        }
    } 
    else if (isset($_GET['goalpos'])) 
    {
        $startPositions = $_GET['startpos'];
        $goalPositions = $_GET['goalpos'];
        $groupCode = $_GET['groupcode'];

        addGoal($startPositions, $goalPositions, $groupCode);
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

    function addGoal($startPositions, $goalPositions, $groupCode)
    {
        dbHandler::query("INSERT INTO goals (startpositions, goalpositions, groups_groupcode) VALUES ('$startPositions', '$goalPositions', '$groupCode')");
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