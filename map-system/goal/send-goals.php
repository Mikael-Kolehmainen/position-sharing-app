<?php
    require './../../required-files/constants.php';
    require './../../autoloader.php';

    session_start();

    $message = new Message('qOv');
    $message->message = $_POST["testValue"];
    $message->initials = "";
    $message->color = "";
    $message->save();

    if (json_decode(stripslashes($_POST[GROUPCODE]))) {
   //     $amountOfGoals = filter_input(INPUT_GET, GOALAMOUNT, FILTER_VALIDATE_INT);
        $groupCode = filter_input(INPUT_POST, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        $position = new Position(0, 0);
        $position->save();
        $positionRowID = $position->id;
    
    //    insertGoalsToDatabase($amountOfGoals, $groupCode);
    } else {
        header("LOCATION: ./../../index.php");
    }

/*    function insertGoalsToDatabase($amountOfGoals, $groupCode)
    {
        for ($userIndex = 0; $userIndex < $amountOfGoals; $userIndex++) {
            $startPosition = getPositionFromURL(STARTLAT.$userIndex, STARTLNG.$userIndex);
            $goalPosition = getPositionFromURL(GOALLAT.$userIndex, GOALLNG.$userIndex);

            $startPositionRowID = insertPositionToDatabase($startPosition->latitude, $startPosition->longitude);
            $goalPositionRowID = insertPositionToDatabase($goalPosition->latitude, $goalPosition->longitude);

            $goalIndexDKey = GOALINDEX.$userIndex;
            $goalIndex = filter_input(INPUT_GET, $goalIndexDKey, FILTER_VALIDATE_INT);
            $goalRowID = insertGoalToDatabase($startPositionRowID, $goalPositionRowID, $goalIndex, $groupCode);
        
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

    function insertGoalToDatabase($startPositionRowID, $goalPositionRowID, $goalIndex, $groupCode)
    {
        $goal = new Goal($groupCode);
        $goal->startPositionID = $startPositionRowID;
        $goal->goalPositionID = $goalPositionRowID;
        $goal->goalIndex = $goalIndex;
        $goal->save();

        return $goal->id;
    }

    function insertWaypointToDatabase($goalRowID, $positionRowID)
    {
        $waypoint = new Waypoint();
        $waypoint->goalsID = $goalRowID;
        $waypoint->positionsID = $positionRowID;
        $waypoint->save();
    } */