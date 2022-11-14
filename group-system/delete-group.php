<?php
    require './../required-files/constants.php';
    require './../autoloader.php';

    if (isset($_GET[GROUPCODE])) {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        removeGroup($groupCode);
        removeGroupUsers($groupCode);
        removeGroupMessages($groupCode);
        $goalsID = getGoalsID($groupCode);
        removeGoalPositions(getStartGoalPositionsIDs($groupCode), getWaypointPositionsIDs($goalsID));
        removeGoalWaypoints($goalsID);
        removeGoal($groupCode);

        header("LOCATION: ./../index.php");
    }

    function removeGroup($groupCode)
    {
        $group = new Group($groupCode);
        $group->remove();
    }

    function removeGroupUsers($groupCode)
    {
        $user = new User();
        $user->groupCode = $groupCode;
        $user->removeWithGroupCode();
    }

    function removeGroupMessages($groupCode)
    {
        $message = new Message($groupCode);
        $message->remove();
    }

    function getGoalsID($groupCode)
    {
        $goal = new Goal($groupCode);
        
        return $goal->getIDs();
    }

    function removeGoalPositions($startGoalPositionsIDs, $waypointPositionsIDs)
    {
        $position = new Position();

        for ($i = 0; $i < count($startGoalPositionsIDs); $i++) {
            $position->id = $startGoalPositionsIDs[$i][START_POSITIONS_ID];
            $position->remove();
            $position->id = $startGoalPositionsIDs[$i][GOAL_POSITIONS_ID];
            $position->remove();
        }

        for ($i = 0; $i < count($waypointPositionsIDs); $i++) {
            $position->id = $waypointPositionsIDs[$i][POSITIONS_ID];
            $position->remove();
        }
    }

    function getStartGoalPositionsIDs($groupCode)
    {
        $goal = new Goal($groupCode);

        return $goal->getStartGoalPositionsRowIDs();
    }

    function getWaypointPositionsIDs($goalsID)
    {
        $waypoint = new Waypoint();
        $waypoint->goalsID = $goalsID[0][ID];

        return $waypoint->getPositionsRowIDs();
    }

    function removeGoalWaypoints($goalsID)
    {
        $waypoint = new Waypoint();
        $waypoint->goalsID = $goalsID[0][ID];
        $waypoint->remove();
    }

    function removeGoal($groupCode)
    {
        $goal = new Goal($groupCode);
        $goal->remove();
    }