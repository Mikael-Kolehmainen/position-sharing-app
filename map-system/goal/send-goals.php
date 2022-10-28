<?php
    require './../../required-files/constants.php';
    require './../../autoloader.php';

    session_start();

    header('Content-Type: application/json, charset=UTF-8');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $json = json_decode(file_get_contents('php://input'));
        
        if ($json[0]->groupcode) {
            $groupCode = $json[0]->groupcode;
            $goalSession = createGoalSession($groupCode);

            for ($i = 0; $i < count($json); $i++) {
                $jsonObj = $json[$i];
                
                $startPositionRowID = insertPositionToDatabase($jsonObj->startlat, $jsonObj->startlng);
                $goalPositionRowID = insertPositionToDatabase($jsonObj->goallat, $jsonObj->goallng);

                $goalRowID = insertGoalToDatabase($jsonObj->groupcode, $startPositionRowID, $goalPositionRowID, $jsonObj->goalindex, $goalSession);

                $waypoints = $jsonObj->routewaypoints;

                for ($j = 0; $j < count($waypoints); $j++) {
                    $waypointPositionRowID = insertPositionToDatabase($waypoints[$j]->lat, $waypoints[$j]->lng);

                    insertWaypointToDatabase($goalRowID, $waypointPositionRowID);
                }
            }
        }
    } else {
        header("LOCATION: ./../../index.php");
    }

    function createGoalSession($groupCode)
    {
        $goal = new Goal($groupCode);

        return $goal->createGoalSession();
    }

    function insertPositionToDatabase($lat, $lng)
    {
        $position = new Position($lat, $lng);
        $position->save();

        return $position->id;
    }

    function insertGoalToDatabase($groupCode, $startPositionRowID, $goalPositionRowID, $goalIndex, $goalSession)
    {
        $goal = new Goal($groupCode);
        $goal->startPositionID = $startPositionRowID;
        $goal->goalPositionID = $goalPositionRowID;
        $goal->goalIndex = $goalIndex;
        $goal->goalSession = $goalSession;
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