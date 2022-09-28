<?php
    require './../../required-files/dbHandler.php';
    require './../../required-files/constants.php';
    require './../../db/Goal.php';
    require './../../db/Waypoint.php';
    require './../../db/Position.php';

    if (isset($_GET[GROUPCODE])) {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        $goalsID = getGoalsID($groupCode);
        removeGoalPositions(getStartGoalPositionsIDs($groupCode), getWaypointPositionsIDs($goalsID));
        removeGoalWaypoints($goalsID);
        removeGoal($groupCode);
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
            $position->id = $startGoalPositionsIDs[$i]['start_positions_id'];
            $position->remove();
            $position->id = $startGoalPositionsIDs[$i]['goal_positions_id'];
            $position->remove();
        }

        for ($i = 0; $i < count($waypointPositionsIDs); $i++) {
            $position->id = $waypointPositionsIDs[$i]['positions_id'];
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
        $waypoint->goalsID = $goalsID[0]['id'];

        return $waypoint->getPositionsRowIDs();
    }

    function removeGoalWaypoints($goalsID)
    {
        $waypoint = new Waypoint();
        $waypoint->goalsID = $goalsID[0]['id'];
        $waypoint->remove();
    }

    function removeGoal($groupCode)
    {
        $goal = new Goal($groupCode);

        $goal->remove();
    }