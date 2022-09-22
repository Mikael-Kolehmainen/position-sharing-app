<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';
    require './../db/Goal.php';
    require './../db/Waypoint.php';

    if (isset($_GET[GROUPCODE])) {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        removeGoalPositions(getPositionsID(getGoalsID($groupCode)));
        removeGoalWaypoints(getGoalsID($groupCode));
        removeGoal($groupCode);
    }

    function removeGoalPositions($positionsID)
    {
        // TODO: do for loop on array and remove positions with id equal to the array element
        $position = new Position();
        $position->id = $positionsID;
        $position->remove();
    }

    function getPositionsID($goalsID)
    {
        // TODO: get positions id of waypoint and start/goal and put them in an array
        $waypoint = new Waypoint();

    }

    function getGoalsID($groupCode)
    {
        $goal = new Goal();
        $goal->groupCode = $groupCode;
        
        return $goal->getGoalsID();
    }

    function removeGoalWaypoints($goalsID)
    {
        $waypoint = new Waypoint();
        $waypoint->goalsID = $goalsID[0];
        $waypoint->remove();
    }

    function removeGoal($groupCode)
    {
        $goal = new Goal();
        $goal->groupCode = $groupCode;
        $goal->remove();
    }