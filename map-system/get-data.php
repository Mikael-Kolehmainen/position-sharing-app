<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';

    if (isset($_GET['groupcode'])) {
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        $data = getData($groupCode);

        echo json_encode($data);
    }

    function getData($groupCode)
    {
        $data = array();
        $data['positionsdata'] = getPositions($groupCode);
        $data['messagesdata'] = getMessages($groupCode);
        $data['goalsdata'] = getGoals($groupCode);

        return $data;
    }

    function getPositions($groupCode)
    {
        $positionsData = array();
        $positionsData['positions'] = array();
        $positionsData['initials'] = array();
        $positionsData['colors'] = array();

        $result = selectPositionsFromDatabase();
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($row[GROUPS_GROUPCODE] == $groupCode) {
                    array_push($positionsData['positions'] , $row['position']);
                    array_push($positionsData['initials'], $row['initials']);
                    array_push($positionsData['colors'], $row['color']);
                }
            }
        }

        return $positionsData;
    }

    function selectPositionsFromDatabase()
    {
        return dbHandler::query("SELECT position, initials, color, ".GROUPS_GROUPCODE." FROM positions");
    }

    function getMessages($groupCode)
    {
        $messagesData = array();
        $messagesData['messages'] = array();
        $messagesData['initials'] = array();
        $messagesData['colors'] = array();

        $result = selectMessagesFromDatabase();
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);

                if ($row[GROUPS_GROUPCODE] == $groupCode) {
                    array_push($messagesData['messages'], $row['message']);
                    array_push($messagesData['initials'], $row['initials']);
                    array_push($messagesData['colors'], $row['color']);
                }
            }
        }

        return $messagesData;
    }

    function selectMessagesFromDatabase()
    {
        return dbHandler::query("SELECT message, initials, color, ".GROUPS_GROUPCODE." FROM messages");
    }

    function getGoals($groupCode)
    {
        $goalsData = array();
        $goalsData['startpositions'] = array();
        $goalsData['goalpositions'] = array();
        $goalsData['waypoints'] = array();
        $goalsData['goalids'] = array();

        $result = selectGoalsFromDatabase();
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);

                if ($row[GROUPS_GROUPCODE] == $groupCode) {
                    array_push($goalsData['startpositions'], formatPosition($row['startposition']));

                    array_push($goalsData['goalpositions'], formatPosition($row['goalposition']));

                    array_push($goalsData['goalids'], $row['goalID']);

                    if (isset($row['waypoints'])) {
                        array_push($goalsData['waypoints'], formatPositionsArray($row['waypoints']));
                    }
                }
            }
        }
        if (count($goalsData['startpositions']) == 0) {
            array_push($goalsData['startpositions'], "empty");
            array_push($goalsData['goalpositions'], "empty");
        }

        return $goalsData;
    }

    function selectGoalsFromDatabase()
    {
        return dbHandler::query("SELECT startposition, goalposition, waypoints, goalID, ".GROUPS_GROUPCODE." FROM goals");
    }

    function formatPosition($position)
    {
        // We remove 'LatLng(' and ')' from string
        $position = substr($position, 7);
        $position = substr($position, 0, -1);

        return $position;
    }

    function formatPositionsArray($positionsArr)
    {
        // We remove 'LatLng(' and ')' from each element in array
        $positions = explode('LatLng(', $positionsArr);
        
        for ($i = 0; $i < count($positions); $i++) {
            $positions[$i] = substr($positions[$i], 0, -1);
        } 

        // Remove elements that are emtpy
        return array_values(array_filter($positions));
    }