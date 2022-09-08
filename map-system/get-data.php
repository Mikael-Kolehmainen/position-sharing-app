<?php
    require './../required-files/dbHandler.php';

    if (isset($_GET['groupcode'])) {
        $positionsData = array();
        $positionsData['positions'] = array();
        $positionsData['initials'] = array();
        $positionsData['colors'] = array();

        $messagesData = array();
        $messagesData['messages'] = array();
        $messagesData['initials'] = array();
        $messagesData['colors'] = array();

        $goalsData = array();
        $goalsData['startpositions'] = array();
        $goalsData['goalpositions'] = array();
        $goalsData['waypoints'] = array();

        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_DEFAULT);

        // GET POSITIONS
        $result = selectPositions();
        if (mysqli_num_rows($result) > 0) 
        {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) 
            {
                $row = mysqli_fetch_assoc($result);
                if ($row['groups_groupcode'] == $groupCode) 
                {
                    array_push($positionsData['positions'] , $row['position']);
                    array_push($positionsData['initials'], $row['initials']);
                    array_push($positionsData['colors'], $row['color']);
                }
            }
        }

        // GET MESSAGES
        $result = selectMessages();
        if (mysqli_num_rows($result) > 0) 
        {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) 
            {
                $row = mysqli_fetch_assoc($result);
                if ($row['groups_groupcode'] == $groupCode) 
                {
                    array_push($messagesData['messages'], $row['message']);
                    array_push($messagesData['initials'], $row['initials']);
                    array_push($messagesData['colors'], $row['color']);
                }
            }
        }

        // GET GOALS
        $result = selectGoals();
        if (mysqli_num_rows($result) > 0) 
        {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) 
            {
                $row = mysqli_fetch_assoc($result);
                if ($row['groups_groupcode'] == $groupCode) 
                {
                    // We remove 'LatLng(' and ')' from string
                    $startPosition = substr($row['startposition'], 7);
                    $startPosition = substr($startPosition, 0, -1);

                    array_push($goalsData['startpositions'], $startPosition);

                    // We remove 'LatLng(' and ')' from string
                    $goalPosition = substr($row['goalposition'], 7);
                    $goalPosition = substr($goalPosition, 0, -1);

                    array_push($goalsData['goalpositions'], $goalPosition);

                    // We remove 'LatLng(' and ')'
                    if (isset($row['waypoints']))
                    {
                        $waypoints = explode('LatLng(', $row['waypoints']);
                        for ($j = 0; $j < count($waypoints); $j++) 
                        {
                            $waypoints[$j] = substr($waypoints[$j], 0, -1);
                        } 
                        // Remove elements that are emtpy
                        $waypoints = array_values(array_filter($waypoints));

                        array_push($goalsData['waypoints'], $waypoints);
                    }
                }
            }
        }
        if (count($goalsData['startpositions']) == 0)
        {
            array_push($goalsData['startpositions'], "empty");
            array_push($goalsData['goalpositions'], "empty");
        }

        $data = array();
        $data['positionsdata'] = $positionsData;
        $data['messagesdata'] = $messagesData;
        $data['goalspositions'] = $goalsData;

        echo json_encode($data);
    }

    function selectPositions()
    {
        return dbHandler::query("SELECT position, initials, color, groups_groupcode FROM positions");
    }

    function selectMessages()
    {
        return dbHandler::query("SELECT message, initials, color, groups_groupcode FROM messages");
    }

    function selectGoals()
    {
        return dbHandler::query("SELECT startposition, goalposition, waypoints, groups_groupcode FROM goals");
    }
?>