<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';
    require './../db/Position.php';
    require './../db/Goal.php';
    require './../db/Waypoint.php';

    session_start();

    if (isset($_GET[GOALAMOUNT]) && isset($_GET[GROUPCODE])) {
        $amountOfGoals = filter_input(INPUT_GET, GOALAMOUNT, FILTER_DEFAULT);
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        insertGoalsToDatabase($amountOfGoals, $groupCode);
    } else {
        header("LOCATION: ./../index.php");
    }

    function insertGoalsToDatabase($amountOfGoals, $groupCode)
    {
        for ($userIndex = 0; $userIndex < $amountOfGoals; $userIndex++) {
            $startPosition = getPositionFromURL('startlat'.$userIndex, 'startlng'.$userIndex);
            $goalPosition = getPositionFromURL('goallat'.$userIndex, 'goallng'.$userIndex);

            $startPositionRowID = insertPositionToDatabase($startPosition->latitude, $startPosition->longitude);
            $goalPositionRowID = insertPositionToDatabase($goalPosition->latitude, $goalPosition->longitude);

            $goalIDKey = 'goalid'.$userIndex;
            $goalID = filter_input(INPUT_GET, $goalIDKey, FILTER_DEFAULT);
            $goalRowID = insertGoalToDatabase($startPositionRowID, $goalPositionRowID, $goalID, $groupCode);
        
            $waypointIndex = 0;
            $waypointLatKey = WAYPOINT.$userIndex.'-'.$waypointIndex.'-lat';
            $waypointLngKey = WAYPOINT.$userIndex.'-'.$waypointIndex.'-lng';

            while (isset($_GET[$waypointLatKey]) && isset($_GET[$waypointLngKey])) {
                $waypointPosition = getPositionFromURL($waypointLatKey, $waypointLngKey);

                $waypointPositionRowID = insertPositionToDatabase($waypointPosition->latitude, $waypointPosition->longitude);
                insertWaypointToDatabase($goalRowID, $waypointPositionRowID);

                $waypointIndex = $waypointIndex + 1;
                $waypointLatKey = WAYPOINT.$userIndex.'-'.$waypointIndex.'-lat';
                $waypointLngKey = WAYPOINT.$userIndex.'-'.$waypointIndex.'-lng';
            }
        }
    }

    function getPositionFromURL($latKey, $lngKey)
    {
        $latValue = filter_input(INPUT_GET, $latKey, FILTER_DEFAULT);
        $lngValue = filter_input(INPUT_GET, $lngKey, FILTER_DEFAULT);

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
        $goal = new Goal();
        $goal->startPositionID = $startPositionRowID;
        $goal->goalPositionID = $goalPositionRowID;
        $goal->goalID = $goalID;
        $goal->groupCode = $groupCode;
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