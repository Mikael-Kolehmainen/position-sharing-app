<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';
    require './../db/Position.php';
    require './../db/User.php';

    if (isset($_GET[GROUPCODE])) {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        $data = getData($groupCode);

        echo json_encode($data);
    }

    function getData($groupCode)
    {
        $data = array();
        $data[POSITIONSDATA] = getPositions($groupCode); /* use getAllPositionRowIDs() from User class in this function */
        $data['usersdata'] = getUsersFromDatabase($groupCode);
        $data[MESSAGESDATA] = getMessages($groupCode);
        $data[GOALSDATA] = getGoals($groupCode);

        return $data;
    }

    function getPositions($groupCode)
    {
        $positionsData = array();
        $positionsData['lat'] = array();
        $positionsData['lng'] = array();
        $positionsData[INITIALS] = array();
        $positionsData[COLORS] = array();

        $result = selectPositionsFromDatabase();
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($row[GROUPS_GROUPCODE] == $groupCode) {
                    array_push($positionsData['lat'] , $row['lat']);
                    array_push($positionsData['lng'], $row['lng']);
                    array_push($positionsData[INITIALS], $row[INITIALS]);
                    array_push($positionsData[COLORS], $row[COLOR]);
                }
            }
        }

        return $positionsData;
    }

    function selectPositionsFromDatabase()
    {
        return dbHandler::query("SELECT lat, lng, ".INITIALS.", ".COLOR.", ".GROUPS_GROUPCODE." FROM users");
    }

    function getUsersFromDatabase($groupCode)
    {
        $user = new User();
        $user->groupCode = $groupCode;

        $userInitials = $user->getInitials();

        return $userInitials;
    }

    function getMessages($groupCode)
    {
        $messagesData = array();
        $messagesData[MESSAGES] = array();
        $messagesData[INITIALS] = array();
        $messagesData[COLORS] = array();

        $result = selectMessagesFromDatabase();
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);

                if ($row[GROUPS_GROUPCODE] == $groupCode) {
                    array_push($messagesData[MESSAGES], $row[MESSAGE]);
                    array_push($messagesData[INITIALS], $row[INITIALS]);
                    array_push($messagesData[COLORS], $row[COLOR]);
                }
            }
        }

        return $messagesData;
    }

    function selectMessagesFromDatabase()
    {
        return dbHandler::query("SELECT ".MESSAGE.", ".INITIALS.", ".COLOR.", ".GROUPS_GROUPCODE." FROM ".MESSAGES);
    }

    function getGoals($groupCode)
    {
        $goalsData = array();
        $goalsData['startlat'] = array();
        $goalsData['startlng'] = array();
        $goalsData['goallat'] = array();
        $goalsData['goallng'] = array();
        $goalsData[WAYPOINTS] = array();
        $goalsData[GOALIDS] = array();

        $result = selectGoalsFromDatabase();
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);

                if ($row[GROUPS_GROUPCODE] == $groupCode) {
                    array_push($goalsData['startlat'], $row['startlat']);
                    array_push($goalsData['startlng'], $row['startlng']);

                    array_push($goalsData['goallat'], $row['goallat']);
                    array_push($goalsData['goallng'], $row['goallng']);

                    array_push($goalsData[GOALIDS], $row[GOALID]);

                    if (isset($row[WAYPOINTS])) {
                        array_push($goalsData[WAYPOINTS], formatPositionsArray($row[WAYPOINTS]));
                    }
                }
            }
        }
        if (count($goalsData['startlat']) == 0) {
            array_push($goalsData['startlat'], "empty");
            array_push($goalsData['startlng'], "empty");
            array_push($goalsData['goallat'], "empty");
            array_push($goalsData['goallng'], "empty");
        }

        return $goalsData;
    }

    function selectGoalsFromDatabase()
    {
        return dbHandler::query("SELECT startlat, startlng, goallat, goallng, 
                                ".WAYPOINTS.", ".GOALID.", ".GROUPS_GROUPCODE." FROM ".GOALS);
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