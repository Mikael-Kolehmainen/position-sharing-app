<?php
require './../../required-files/constants.php';
require './../../autoloader.php';

if (isset($_GET[GROUPCODE])) {
    $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

    if (groupExists($groupCode)) {
        $data = getData($groupCode);
    } else {
        $data = "Group doesn't exist";
    }

    echo json_encode($data);
}

function groupExists($groupCode)
{
    $group = new Group($groupCode);

    return $group->getRowCount();
}

function getData($groupCode)
{
    $data = array();
    $data[USERSDATA] = getUsersDetailsFromDatabase($groupCode);
    $data[MESSAGESDATA] = getMessagesFromDatabase($groupCode);

    if (isset($_COOKIE["goalCookieRemoved"])) {
        setcookie("goalCookie", null, -1, "/");
        setcookie("goalCookieRemoved", null, -1, "/");
    }

    if (isset($_COOKIE["goalCookie"])) {
        if (goalCookieEqualsDBgoalCookie($groupCode)) {
            $data[GOALSDATA] = ["already saved"];
        } else {
            $data[GOALSDATA] = getGoalPositionsFromDatabase($groupCode);
            saveCookie($groupCode);
        }
    } else {
        $data[GOALSDATA] = getGoalPositionsFromDatabase($groupCode);
        saveCookie($groupCode);
    }
    return $data;
}

function getUsersDetailsFromDatabase($groupCode)
{
    $user = new User();
    $user->groupCode = $groupCode;

    $userMarkerDetails = $user->getMarkerDetails();

    for ($i = 0; $i < count($userMarkerDetails); $i++) {
        $position = new Position();
        $position->id = $userMarkerDetails[$i][POSITIONS_ID];
        $userMarkerDetails[$i][POSITION] = $position->getLatLng();
    }

    return $userMarkerDetails;
}

function getMessagesFromDatabase($groupCode)
{
    $message = new Message($groupCode);

    return $message->get();
}

function getGoalPositionsFromDatabase($groupCode)
{
    $goal = new Goal($groupCode);

    $startGoalPositionsRowIDs = $goal->getStartGoalPositionsRowIDs();

    $goalsData = array();
    if (count($startGoalPositionsRowIDs) > 0) {
        for ($i = 0; $i < count($startGoalPositionsRowIDs); $i++) {
            $goalsData[$i] = $goal->getIndexes()[$i];

            $goalsData[$i][START_POSITION] = getPosition($startGoalPositionsRowIDs[$i], START_POSITIONS_ID);
            $goalsData[$i][GOAL_POSITION] = getPosition($startGoalPositionsRowIDs[$i], GOAL_POSITIONS_ID);

            $goalsData[$i][WAYPOINTS] = getWaypointPositions($i, $groupCode);
        }
    } else {
        $goalsData[0] = CONSTANT_EMPTY;
    }

    return $goalsData;
}

function goalCookieEqualsDBgoalCookie($groupCode)
{
    $goal = new Goal($groupCode);

    if ($goal->getGoalCookie() != null) {
       return $goal->getGoalCookie()[0]["goalcookie"] == $_COOKIE['goalCookie'];
    }
}

function saveCookie($groupCode)
{
    $goal = new Goal($groupCode);

    setcookie("goalCookie", $goal->getGoalCookie()[0]["goalcookie"], time() + (86400 * 30), "/");
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

    $waypoint->goalsID = $goalsID[$loopIndex][ID];

    $waypoints = array();

    for ($i = 0; $i < count($waypoint->getPositionsRowIDs()); $i++) {
        $position->id = $waypoint->getPositionsRowIDs()[$i][POSITIONS_ID];
        $waypoints[$i] = $position->getLatLng();
    }

    return $waypoints;
}

function getGoalsID($groupCode)
{
    $goal = new Goal($groupCode);

    return $goal->getIDs();
}