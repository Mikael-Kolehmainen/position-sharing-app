<?php
    require './../../required-files/dbHandler.php';
    require './../../required-files/constants.php';
    require './../../db/Position.php';
    require './../../db/Goal.php';
    require './../../db/Waypoint.php';

    session_start();

    if (isset($_GET[GOALAMOUNT]) && isset($_GET[GROUPCODE])) {
        $amountOfGoals = filter_input(INPUT_GET, GOALAMOUNT, FILTER_VALIDATE_INT);
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        insertGoalsToDatabase($amountOfGoals, $groupCode);
    } else {
        header("LOCATION: ./../index.php");
    }

    function insertGoalsToDatabase($amountOfGoals, $groupCode)
    {
        for ($userIndex = 0; $userIndex < $amountOfGoals; $userIndex++) {
            $startPosition = getPositionFromURL(STARTLAT.$userIndex, STARTLNG.$userIndex);
            $goalPosition = getPositionFromURL(GOALLAT.$userIndex, GOALLNG.$userIndex);

            $startPositionRowID = insertPositionToDatabase($startPosition->latitude, $startPosition->longitude);
            $goalPositionRowID = insertPositionToDatabase($goalPosition->latitude, $goalPosition->longitude);

            $goalIDKey = GOALID.$userIndex;
            $goalID = filter_input(INPUT_GET, $goalIDKey, FILTER_VALIDATE_INT);
            $goalRowID = insertGoalToDatabase($startPositionRowID, $goalPositionRowID, $goalID, $groupCode);
        
            $waypointIndex = 0;
            $waypointLatKey = WAYPOINT.$userIndex.'-'.$waypointIndex.'-'.LAT;
            $waypointLngKey = WAYPOINT.$userIndex.'-'.$waypointIndex.'-'.LNG;

            while (isset($_GET[$waypointLatKey]) && isset($_GET[$waypointLngKey])) {
                $waypointPosition = getPositionFromURL($waypointLatKey, $waypointLngKey);

                $waypointPositionRowID = insertPositionToDatabase($waypointPosition->latitude, $waypointPosition->longitude);
                insertWaypointToDatabase($goalRowID, $waypointPositionRowID);

                $waypointIndex = $waypointIndex + 1;
                $waypointLatKey = WAYPOINT.$userIndex.'-'.$waypointIndex.'-'.LAT;
                $waypointLngKey = WAYPOINT.$userIndex.'-'.$waypointIndex.'-'.LNG;
            }
        }
    }

    function getPositionFromURL($latKey, $lngKey)
    {
        $latValue = filter_input(INPUT_GET, $latKey, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $lngValue = filter_input(INPUT_GET, $lngKey, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $position = new Position($latValue, $lngValue);

        return $position;
    }

    function insertPositionToDatabase($lat, $lng)
    {
        $position = new Position($lat, $lng);
        $position->save();
        $positionRowID = $position->id;
    
        return $positionRowID;
    }

    function insertGoalToDatabase($startPositionRowID, $goalPositionRowID, $goalID, $groupCode)
    {
        $goal = new Goal($groupCode);
        $goal->startPositionID = $startPositionRowID;
        $goal->goalPositionID = $goalPositionRowID;
        $goal->goalID = $goalID;
        $goal->save();

        return $goal->id;
    }

    function insertWaypointToDatabase($goalRowID, $positionRowID)
    {
        $waypoint = new Waypoint();
        $waypoint->goalsID = $goalRowID;
        $waypoint->positionsID = $positionRowID;
        $waypoint->save();
    }