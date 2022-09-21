<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';
    require './../db/Position.php';
    require './../db/User.php';
    require './../db/Goal.php';
    require './../db/Waypoint.php';

    if (isset($_GET[GROUPCODE])) {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        $data = getData($groupCode);

        echo json_encode($data);
    }

    function getData($groupCode)
    {
        $data = array();
        $data['usersdata'] = getUsersDetailsFromDatabase($groupCode);
        $data[MESSAGESDATA] = getMessages($groupCode);
        $data[GOALSDATA] = getGoalPositionsFromDatabase($groupCode);

        return $data;
    }

    function getUsersDetailsFromDatabase($groupCode)
    {
        $user = new User();
        $user->groupCode = $groupCode;

        $userMarkerDetails = $user->getMarkerDetails();

        $userPositions = array();

        for ($i = 0; $i < count($userMarkerDetails); $i++) {
            $position = new Position();
            $position->id = $userMarkerDetails[$i]["positions_id"];
            $userMarkerDetails[$i]["position"] = $position->getPosition();
        }

        return $userMarkerDetails;
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

    function getGoalPositionsFromDatabase($groupCode)
    {
        $startGoalPositionsRowIDs = getStartGoalPositionsRowIDs($groupCode);

        $goalsID = getGoalsID($groupCode);

        $goalsData = array();

        for ($i = 0; $i < count($startGoalPositionsRowIDs); $i++) {
            $position = new Position();
            $position->id = $startGoalPositionsRowIDs[$i]["start_positions_id"];
            $goalsData[$i]["start_position"] = $position->getPosition();

            $position->id = $startGoalPositionsRowIDs[$i]["goal_positions_id"];
            $goalsData[$i]["goal_position"] = $position->getPosition();

            $waypoint = new Waypoint();
            $waypoint->goalsID = $goalsID[$i];
            $goalsData[$i]["waypoints"] = $waypoint->getWaypointsPositionIDs();
        }

        return $goalsData;
    }

    function getStartGoalPositionsRowIDs($groupCode)
    {
        $goal = new Goal();
        $goal->groupCode = $groupCode;
        $startGoalPositionsRowIds = $goal->getStartGoalPositionsRowIDs();

        return $startGoalPositionsRowIds;
    }

    function getGoalsID($groupCode)
    {
        $goal = new Goal();
        $goal->groupCode = $groupCode;
        return $goal->getGoalsID();
    }