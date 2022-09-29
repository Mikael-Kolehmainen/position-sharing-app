<?php
    require './../../required-files/dbHandler.php';
    require './../../required-files/constants.php';
    require './../../db/Goal.php';
    require './../../db/Waypoint.php';
    require './../../db/Position.php';

    if (isset($_GET[GROUPCODE])) {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        $goal = new Goal($groupCode);
        $goalsID = $goal->getIDs();
        removeGoalPositions($goal->getStartGoalPositionsRowIDs(), getWaypointPositionsIDs($goalsID));
        removeGoalWaypoints($goalsID);
        $goal->remove();
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
            for ($j = 0; $j < count($waypointPositionsIDs[$i]); $j++) {
                $position->id = $waypointPositionsIDs[$i][$j][POSITIONS_ID];
                $position->remove();
            }
        }
    }

    function getWaypointPositionsIDs($goalsID)
    {
        $waypoint = new Waypoint();
        $positionRowIDs = array();

        for ($i = 0; $i < count($goalsID); $i++) {
            $waypoint->goalsID = $goalsID[$i][ID];
            array_push($positionRowIDs, $waypoint->getPositionsRowIDs());
        }

        return $positionRowIDs;
    }

    function removeGoalWaypoints($goalsID)
    {
        $waypoint = new Waypoint();
        
        for ($i = 0; $i < count($goalsID); $i++) {
            $waypoint->goalsID = $goalsID[$i][ID];
            $waypoint->remove();
        }
    }