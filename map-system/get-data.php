<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';
    require './../db/Position.php';
    require './../db/User.php';
    require './../db/Goal.php';
    require './../db/Waypoint.php';
    require './../db/Message.php';

    if (isset($_GET[GROUPCODE])) {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        $data = getData($groupCode);

        echo json_encode($data);
    }

    function getData($groupCode)
    {
        $data = array();
        $data['usersdata'] = getUsersDetailsFromDatabase($groupCode);
        $data[MESSAGESDATA] = getMessagesFromDatabase($groupCode);
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
            $userMarkerDetails[$i]["position"] = $position->getLatLng();
        }

        return $userMarkerDetails;
    }

    function getMessagesFromDatabase($groupCode)
    {
        $message = new Message();
        $message->groupCode = $groupCode;
        $messages = $message->get();

        return $messages;
    }

    function selectMessagesFromDatabase()
    {
        return dbHandler::query("SELECT ".MESSAGE.", ".INITIALS.", ".COLOR.", ".GROUPS_GROUPCODE." FROM ".MESSAGES);
    }

    function getGoalPositionsFromDatabase($groupCode)
    {
        $startGoalPositionsRowIDs = getStartGoalPositionsRowIDs($groupCode);

        $goalsData = array();
        if (count($startGoalPositionsRowIDs) > 0) {
            for ($i = 0; $i < count($startGoalPositionsRowIDs); $i++) {
                $goalsData[$i]["goal_id"] = getGoalIndexes($groupCode)[$i];

                $goalsData[$i]["start_position"] = getPosition($startGoalPositionsRowIDs[$i], "start_positions_id");
                $goalsData[$i]["goal_position"] = getPosition($startGoalPositionsRowIDs[$i], "goal_positions_id");
    
                $goalsData[$i]["waypoints"] = getWaypointPositions($i, $groupCode);
            }
        } else {
            $goalsData[0] = "empty";
        }

        return $goalsData;
    }

    function getStartGoalPositionsRowIDs($groupCode)
    {
        $goal = new Goal();
        $goal->groupCode = $groupCode;

        return $goal->getStartGoalPositionsRowIDs();
    }

    function getGoalIndexes($groupCode)
    {
        $goal = new Goal();
        $goal->groupCode = $groupCode;
        
        return $goal->getIndexes();
    }

    function getPosition($rowID, $positionName)
    {
        $position = new Position();
        $position->id = $rowID[$positionName];

        return $position->getLatLng();
    }

    function getWaypointPositions($loopIndex, $groupCode)
    {
        $waypoint = new Waypoint();
        $position = new Position();
        $goalsID = getGoalsID($groupCode);

        $waypoint->goalsID = $goalsID[$loopIndex]["id"];

        $waypoints = array();

        for ($i = 0; $i < count($waypoint->getPositionsRowIDs()); $i++) {
            $position->id = $waypoint->getPositionsRowIDs()[$i]["positions_id"];
            $waypoints[$i] = $position->getLatLng();
        }

        return $waypoints;
    }

    function getGoalsID($groupCode)
    {
        $goal = new Goal();
        $goal->groupCode = $groupCode;
        return $goal->getIDs();
    }