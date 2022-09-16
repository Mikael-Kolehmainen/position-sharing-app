<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';

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
            $startLatKey = 'startlat'.$userIndex;
            $startLat = filter_input(INPUT_GET, $startLatKey, FILTER_DEFAULT);
            $startLngKey = 'startlng'.$userIndex;
            $startLng = filter_input(INPUT_GET, $startLngKey, FILTER_DEFAULT);

            $goalLatKey = 'goallat'.$userIndex;
            $goalLat = filter_input(INPUT_GET, $goalLatKey, FILTER_DEFAULT);
            $goalLngKey = 'goallng'.$userIndex;
            $goalLng = filter_input(INPUT_GET, $goalLngKey, FILTER_DEFAULT);

            $waypointIndex = 0;
            $waypointKey = WAYPOINT.$userIndex.'-'.$waypointIndex;
            $waypoints = "";

            $goalIDKey = 'goalid'.$userIndex;
            $goalID = filter_input(INPUT_GET, $goalIDKey, FILTER_DEFAULT);

            while (isset($_GET[$waypointKey])) {
                $waypoints .= filter_input(INPUT_GET, $waypointKey, FILTER_DEFAULT);

                $waypointIndex = $waypointIndex + 1;
                $waypointKey = WAYPOINT.$userIndex.'-'.$waypointIndex;
            }

            insertGoalToDatabase($startLat, $startLng, $goalLat, $goalLng, $waypoints, $goalID, $groupCode);
        }
    }

    function insertGoalToDatabase($startLat, $startLng, $goalLat, $goalLng, $waypoints, $goalID, $groupCode)
    {
        dbHandler::query("INSERT INTO ".GOALS." (startlat, startlng, goallat, goallng, ".WAYPOINTS.", ".GOALID.", ".GROUPS_GROUPCODE.") 
                            VALUES ('$startLat', '$startLng', '$goalLat', '$goalLng', '$waypoints', '$goalID', '$groupCode')");
    }