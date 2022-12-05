<?php
require __DIR__ . "/inc/bootstrap.php";
require PROJECT_ROOT_PATH . "/controller/basic/HomeController.php";
require PROJECT_ROOT_PATH . "/controller/basic/CreateController.php";
require PROJECT_ROOT_PATH . "/controller/basic/SearchController.php";
require PROJECT_ROOT_PATH . "/controller/api/ActiveMapController.php";
require PROJECT_ROOT_PATH . "/controller/api/CameraController.php";

require PROJECT_ROOT_PATH . "/controller/api/GroupController.php";
require PROJECT_ROOT_PATH . "/controller/api/UserController.php";
require PROJECT_ROOT_PATH . "/controller/api/GoalController.php";
require PROJECT_ROOT_PATH . "/controller/api/PositionController.php";
require PROJECT_ROOT_PATH . "/controller/api/WaypointController.php";
require PROJECT_ROOT_PATH . "/controller/api/MessageController.php";

session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if ($uri[2] != "ajax") {
    echo "
    <!DOCTYPE html>
    <html>
        <head>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <link href='/appearance/styles/css/main.css' rel='stylesheet' type='text/css'>
            <script src='/js/ElementDisplay.js' defer></script>
            <script src='/js/Style.js' defer></script>
            <script src='/js/remove-children.js' defer></script>
            <script src='/js/open.js' defer></script>
            <script src='/js/onclick-events.js' defer></script>
    ";
}

switch ($uri[2]) {
    case null:
        Home();
        break;
    case "create":
        CreateForm();
        break;
    case "search":
        SearchForm();
        break;
    case "map":
        switch ($uri[3]) {
            case "create":
                if (isset($_POST[FORM_CREATE_GROUP])) {
                    Create();
                } else {
                    Redirect::redirect("Please fill the group creation form.", "/index.php");
                }
                break;
            case "search":
                if (isset($_POST[FORM_SEARCH_GROUP])) {
                    Search();
                } else {
                    Redirect::redirect("Please fill the search group form.", "/index.php");
                }
                break;
            case "active":
                if (isset($_SESSION[GROUP_GROUPCODE])) {
                    ActiveMap();
                } else {
                    Redirect::redirect("Your session has expired, try again.", "/index.php");
                }
                break;
            case "camera":
                Camera();
                break;
            case "remove-group":
                Remove();
                break;
            case null: default:
                header("HTTP/1.1 404 Not Found");
                exit();
                break;
        }
        break;
    case "ajax":
        if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
            header('Content-type: Application/json, charset=UTF-8');
            switch ($uri[3]) {
                case "send-position":
                    sendPosition();
                    break;
                case "send-goal":
                    sendGoal();
                    break;
                case "get-data":
                    if (groupExists()) {
                        $data = getData();
                        echo json_encode($data);
                    } else {
                        $data = "Group doesn't exist";
                        echo json_encode($data);
                    }
                    break;
                case "remove-user":
                    removeUser();
                    break;
                case "remove-goal":
                    removeGoal();
                    break;
                case "send-message":
                    sendMessage();
                    break;
                case "send-image":
                    sendImage();
                    break;
                case null: default:
                    header("HTTP/1.1 404 Not Found");
                    exit();
                    break;
            }
        }
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        exit();
        break;
}

if ($uri[2] != "ajax") {
    echo "
            </body>
        </html>
    ";
}

function Home(): void
{
    $homeController = new HomeController();
    $homeController->showHomePage();
}

function CreateForm(): void
{
    $createController = new CreateController();
    $createController->showCreatePage();
}

function SearchForm(): void
{
    $searchController = new SearchController();
    $searchController->showSearchPage();
}

function Create(): void
{
    $groupController = new GroupController();
    $groupController->saveToDatabase();

    $_SESSION[GROUP_GROUPCODE] = $groupController->groupCode;
    
    redirectToActive();
}

function Search(): void
{
    $groupController = new GroupController();
    $groupController->groupCode = filter_input(INPUT_POST, GROUP_GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
    
    if ($groupController->findGroupInDatabase()) {
        $_SESSION[GROUP_GROUPCODE] = $groupController->groupCode;
        redirectToActive();
    } else {
        Redirect::redirect("Couldn\'t find a group with the given code.", "/index.php/search");
    }
}

function redirectToActive(): void
{
    $userController = new UserController();
    $userController->initials = filter_input(INPUT_POST, USER_INITIALS, FILTER_DEFAULT);
    $userController->color = filter_input(INPUT_POST, USER_COLOR, FILTER_DEFAULT);
    $userController->saveMarkerStyleToSession();
    header("LOCATION: /index.php/map/active");
}

function ActiveMap(): void
{
    $activeController = new ActiveController($_SESSION[GROUP_GROUPCODE]);
    $activeController->showMapPage();
}

function Camera(): void
{
    $cameraController = new CameraController();
    $cameraController->showCamera();
}

function Remove(): void
{
    removeGroup();
    removeGroupUsers();
    removeGroupMessages();
    
    removeGoal();

    header("LOCATION: /index.php");
}

function removeGroup(): void
{
    $groupController = new GroupController();
    $groupController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $groupController->removeGroupFromDatabase();
}

function removeGroupUsers(): void
{
    $userController = new UserController();
    $userController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $userController->removeUsersFromDatabase();
}

function removeGroupMessages(): void
{
    $messageController = new MessageController();
    $messageController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $messageController->removeMessagesFromDatabase();
}

function sendPosition(): void
{
    $json = json_decode(file_get_contents('php://input'));

    $lat = $json->lat;
    $lng = $json->lng;

    if (isset($_SESSION[USER_DB_ROW_ID]) && checkIfRowIdExistsInDatabase()) {
        updatePositionInDatabase($lat, $lng, getRowIdOfPositionFromDatabase());
    } else {
        insertUserToDatabase(insertPositionToDatabase($lat, $lng));
    }
}

function checkIfRowIdExistsInDatabase()
{
    $userController = new UserController();
    $userController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $IDs = $userController->getIDsFromDatabase();

    for ($i = 0; $i < count($IDs); $i++) {
        if ($IDs[$i] == $_SESSION[USER_DB_ROW_ID]) {
            return true;
        }
    }

    return false;
}

function getRowIdOfPositionFromDatabase()
{
    $userController = new UserController();
    $userController->id = $_SESSION[USER_DB_ROW_ID];

    return $userController->getRowIdOfPositionFromDatabase();
}

function updatePositionInDatabase($lat, $lng, $positionsRowId): void
{
    $positionController = new PositionController();
    $positionController->latitude = $lat;
    $positionController->longitude = $lng;
    $positionController->id = $positionsRowId;
    $positionController->updateInDatabase();
}

function insertPositionToDatabase($lat, $lng)
{
    $positionController = new PositionController();
    $positionController->latitude = $lat;
    $positionController->longitude = $lng;
    
    return $positionController->saveToDatabase();
}

function insertUserToDatabase($positionsRowId): void
{
    $userController = new UserController();
    $userController->id = isset($_SESSION[USER_DB_ROW_ID]) ? $_SESSION[USER_DB_ROW_ID] : null;
    $userController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $userController->positionsId = $positionsRowId;
    $userController->initials = $_SESSION[USER_INITIALS];
    $userController->color = $_SESSION[USER_COLOR];
    $userController->saveToDatabase();

    $_SESSION[USER_DB_ROW_ID] = $userController->id;
}

function getData()
{
    $data = [];
    $data[DATA_USERSDATA] = getUsersDataFromDatabase();
    $data[DATA_MESSAGESDATA] = getMessagesDataFromDatabase();

    if (isset($_SESSION[SESSION_GOALSESSION]) && goalSessionEqualsDbGoalSession()) {
        $data[DATA_GOALSDATA] = DATA_ALREADY_SAVED;
    } else {
        $data[DATA_GOALSDATA] = getGoalDataFromDatabase();
        saveSession();
    }

    return $data;
}

function getUsersDataFromDatabase()
{
    $userController = new UserController();
    $userController->groupCode = $_SESSION[GROUP_GROUPCODE];

    $users = $userController->getMarkersFromDatabase();

    for ($i = 0; $i < count($users); $i++) {
        $users[$i][USER_POSITIONS] = getPositionFromDatabase($users[$i][USER_POSITIONS_ID]);
    }

    return $users;
}

function getPositionFromDatabase($id)
{
    $positionController = new PositionController();
    $positionController->id = $id;    

    return $positionController->getLatLngFromDatabase();
}

function getMessagesDataFromDatabase()
{
    $messageController = new MessageController();
    $messageController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $messageData = $messageController->getMessagesFromDatabase();

    if (!isset($_SESSION[MESSAGE_AMOUNT_OF_MESSAGES]) || $_SESSION[MESSAGE_AMOUNT_OF_MESSAGES] != count($messageData)) {
        $userController = new UserController();
        $userController->groupCode = $_SESSION[GROUP_GROUPCODE];

        for ($i = 0; $i < count($messageData); $i++) {
            $userController->id = $messageData[$i][POSITION_USERS_ID];

            $markerStyle = $userController->getMarkerFromDatabaseWithID();
            $messageData[$i][USER_INITIALS] = $markerStyle[0][USER_INITIALS];
            $messageData[$i][USER_COLOR] = $markerStyle[0][USER_COLOR];
        
            if ($messageData[$i][USER_INITIALS] == null || $messageData[$i][USER_COLOR] == null) {
                $messageData[$i][USER_INITIALS] = $messageData[$i][USER_FALLBACK_INITIALS];
                $messageData[$i][USER_COLOR] = $messageData[$i][USER_FALLBACK_COLOR];
            }

            $messageData[$i][MESSAGE_MESSAGE_SENT_BY_USER] = $messageData[$i][POSITION_USERS_ID] == $_SESSION[USER_DB_ROW_ID];

            unset($messageData[$i][POSITION_USERS_ID]);
        }

        $_SESSION[MESSAGE_AMOUNT_OF_MESSAGES] = count($messageData);
    } else {
        $messageData = DATA_ALREADY_SAVED;
    }

    return $messageData;
}

function goalSessionEqualsDbGoalSession()
{
    $goalController = new GoalController();
    $goalController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $goalSession = $goalController->getGoalSessionFromDatabase();

    if ($goalSession != null) {
        return $goalSession == $_SESSION[SESSION_GOALSESSION];
    }
}

function getGoalDataFromDatabase()
{
    $goalController = new GoalController();
    $goalController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $rowIdsOfGoalPositions = $goalController->getRowIdsOfGoalPositionsFromDatabase();

    $goalsData = [];

    if (count($rowIdsOfGoalPositions) > 0) {
        $orderNumbers = $goalController->getOrderNumbersOfGoalsFromDatabase();
        $fallBackInitials = $goalController->getFallbackInitialsFromDatabase();
        for ($i = 0; $i < count($rowIdsOfGoalPositions); $i++) {
            $goalsData[$i][GOAL_ORDER_NUMBER] = $orderNumbers[$i][GOAL_ORDER_NUMBER];

            $goalsData[$i][GOAL_START_POSITION] = getPositionFromDatabase($rowIdsOfGoalPositions[$i][GOAL_START_POSITIONS_ID]);
            $goalsData[$i][GOAL_GOAL_POSITION] = getPositionFromDatabase($rowIdsOfGoalPositions[$i][GOAL_GOAL_POSITIONS_ID]);

            $goalsData[$i][GOAL_WAYPOINTS] = getWaypointPositionsFromDatabase($i);

            $goalsData[$i][USER_FALLBACK_INITIALS] = $fallBackInitials[$i][USER_FALLBACK_INITIALS];
        }
    } else {
        $goalsData = DATA_EMPTY;
    }

    return $goalsData;
}

function getWaypointPositionsFromDatabase($i)
{
    $goalController = new GoalController();
    $positionController = new PositionController();
    $waypointController = new WaypointController();

    $goalController->groupCode = $_SESSION[GROUP_GROUPCODE];

    $waypointController->goalId = $goalController->getIdsFromDatabase()[$i]["id"];

    $waypoints = [];

    $waypointPositionsRowIds = $waypointController->getRowIdsOfWaypointPositionsFromDatabase();

    for ($j = 0; $j < count($waypointPositionsRowIds); $j++) {
        $positionController->id = $waypointPositionsRowIds[$j][USER_POSITIONS_ID];
        $waypoints[$j] = $positionController->getLatLngFromDatabase();
    }

    return $waypoints;
}

function saveSession(): void
{
    $goalController = new GoalController();
    $goalController->groupCode = $_SESSION[GROUP_GROUPCODE];

    $_SESSION[SESSION_GOALSESSION] = $goalController->getGoalSessionFromDatabase();
}

function removeUser(): void
{
    $userController = new UserController();
    $positionController = new PositionController();

    $id = $_SESSION[USER_DB_ROW_ID];

    $userController->id = $id;
    $positionController->id = $userController->getRowIdOfPositionFromDatabase();

    $positionController->removeFromDatabase();
    $userController->removeUserFromDatabase();

    unset($_SESSION[SESSION_GOALSESSION]);
    unset($_SESSION[MESSAGE_AMOUNT_OF_MESSAGES]);
}

function sendGoal(): void
{
    $goalController = new GoalController();
    $userController = new UserController();

    $json = json_decode(file_get_contents('php://input'));
    
    $userController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $userIDs = $userController->getIDsFromDatabase();

    $rowIdsOfUsersWithGoal = [];

    for ($i = 0; $i < count($json); $i++) {
        $rowIdsOfUsersWithGoal[$i] = $userIDs[$json[$i]->goalordernumber];
    }

    $goalRowIds = [];

    for ($i = 0; $i < count($json); $i++) {
        $jsonObj = $json[$i];
        
        $startPositionRowID = insertPositionToDatabase($jsonObj->startlat, $jsonObj->startlng);
        $goalPositionRowID = insertPositionToDatabase($jsonObj->goallat, $jsonObj->goallng);

        $fallBackInitials = getUserInitialsFromDatabase($rowIdsOfUsersWithGoal[$i])[0][USER_INITIALS];

        $goalRowID = insertGoalToDatabase($startPositionRowID, $goalPositionRowID, $jsonObj->goalordernumber, $rowIdsOfUsersWithGoal[$i], $fallBackInitials);
        $goalRowIds[$i] = $goalRowID;

        $waypoints = $jsonObj->routewaypoints;
        for ($j = 0; $j < count($waypoints); $j++) {
            $waypointPositionRowID = insertPositionToDatabase($waypoints[$j]->lat, $waypoints[$j]->lng);

            insertWaypointToDatabase($goalRowID, $waypointPositionRowID);
        }
    }

    $goalController->createGoalSession();

    for ($i = 0; $i < count($goalRowIds); $i++) {
        $goalController->id = $goalRowIds[$i];
        $goalController->updateGoalSessionInDatabase();
    }
}

function getUserInitialsFromDatabase($id)
{
    $userController = new UserController();
    $userController->id = $id;

    return $userController->getMarkerFromDatabaseWithID();
}

function insertGoalToDatabase($startPositionRowID, $goalPositionRowID, $goalOrderNumber, $userID, $fallBackInitials)
{
    $goalController = new GoalController();
    $goalController->startPositionId = $startPositionRowID;
    $goalController->goalPositionId = $goalPositionRowID;
    $goalController->goalOrderNumber = $goalOrderNumber;
    $goalController->userId = $userID;
    $goalController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $goalController->fallbackInitials = $fallBackInitials;
    
    return $goalController->saveToDatabase();
}

function insertWaypointToDatabase($goalRowID, $positionRowID): void
{
    $waypointController = new WaypointController();
    $waypointController->goalId = $goalRowID;
    $waypointController->positionId = $positionRowID;
    $waypointController->saveToDatabase();
}

function groupExists()
{
    $groupController = new GroupController();
    $groupController->groupCode = $_SESSION[GROUP_GROUPCODE];

    return count($groupController->findGroupInDatabase());
}

function removeGoal(): void
{
    $goalController = new GoalController();
    $goalController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $goalsIds = $goalController->getIdsFromDatabase();
    
    removeGoalPositions($goalController->getRowIdsOfGoalPositionsFromDatabase(), getRowIdsOfWaypointPositions($goalsIds));
    removeGoalWaypoints($goalsIds);
    $goalController->removeFromDatabase();
}

function removeGoalPositions($rowIdsOfGoalPositions, $rowIdsOfWaypointPositions): void
{
    $positionController = new PositionController();

    for ($i = 0; $i < count($rowIdsOfGoalPositions); $i++) {
        $positionController->id = $rowIdsOfGoalPositions[$i][GOAL_START_POSITIONS_ID];
        $positionController->removeFromDatabase();

        $positionController->id = $rowIdsOfGoalPositions[$i][GOAL_GOAL_POSITIONS_ID];
        $positionController->removeFromDatabase();
    }

    for ($i = 0; $i < count($rowIdsOfWaypointPositions); $i++) {
        for ($j = 0; $j < count($rowIdsOfWaypointPositions[$i]); $j++) {
            $positionController->id = $rowIdsOfWaypointPositions[$i][$j][USER_POSITIONS_ID];
            $positionController->removeFromDatabase();
        }
    }

    unset($_SESSION[SESSION_GOALSESSION]);
}

function getRowIdsOfWaypointPositions($goalsIds)
{
    $waypointController = new WaypointController();
    $rowIdOfPositions = [];

    for ($i = 0; $i < count($goalsIds); $i++) {
        $waypointController->goalId = $goalsIds[$i]["id"];
        $rowIdOfPositions[$i] = $waypointController->getRowIdsOfWaypointPositionsFromDatabase();
    }
    
    return $rowIdOfPositions;
}

function removeGoalWaypoints($goalsIds): void
{
    $waypointController = new WaypointController();

    for ($i = 0; $i < count($goalsIds); $i++) {
        $waypointController->goalId = $goalsIds[$i]["id"];
        $waypointController->removeFromDatabase();
    }
}

function sendMessage(): void
{
    $messageController = new MessageController();
    $messageController->message = filter_input(INPUT_POST, MESSAGE_MESSAGE, FILTER_SANITIZE_SPECIAL_CHARS);
    $messageController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $messageController->fallbackInitials = $_SESSION[USER_INITIALS];
    $messageController->fallbackColor = $_SESSION[USER_COLOR];
    $messageController->userId = $_SESSION[USER_DB_ROW_ID];
    $messageController->dateOfMessage = date("Y-m-d");
    $messageController->timeOfMessage = date("H:i");
    $messageController->saveToDatabase();

    header("LOCATION: /index.php/map/active");
}

function sendImage(): void
{
    $cameraController = new CameraController();
    $cameraController->groupCode = $_SESSION[GROUP_GROUPCODE];
    $cameraController->webImagePath = $_FILES[MESSAGE_WEB_IMAGE_PATH];
    $cameraController->webImageType = filter_input(INPUT_POST, MESSAGE_WEB_IMAGE_TYPE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

    $cameraController->createImagePath();

    if ($cameraController->saveImageToServer()) {
        $messageController = new MessageController();
        $messageController->groupCode = $_SESSION[GROUP_GROUPCODE];
        $messageController->imagePath = $cameraController->imagePath;
        $messageController->dateOfMessage = date("Y-m.d");
        $messageController->timeOfMessage = date("H:i");
        $messageController->userId = $_SESSION[USER_DB_ROW_ID];
        $messageController->saveToDatabase();
    } else {
        Redirect::redirect("Something went wrong with saving the image to the server.", "/index.php/map/camera");
    }
}