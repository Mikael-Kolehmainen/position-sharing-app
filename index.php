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
                        getData();
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
            }
        }
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        exit();
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
    
    redirectToGroupMap();
}

function Search(): void
{
    $groupController = new GroupController();
    
    if ($groupController->findGroupInDatabase()) {
        redirectToGroupMap();
    } else {
        Redirect::redirect("Couldn\'t find a group with the given code.", "/index.php/search");
    }
}

function redirectToGroupMap(): void
{
    $userController = new UserController();
    $userController->saveMarkerStyleToSession();
    
    header("LOCATION: /index.php/map/active");
}

function ActiveMap(): void
{
    $activeController = new ActiveController();
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
    $groupController->removeGroupFromDatabase();
}

function removeGroupUsers(): void
{
    $userController = new UserController();
    $userController->removeUsersFromDatabase();
}

function removeGroupMessages(): void
{
    $messageController = new MessageController();
    $messageController->removeMessagesFromDatabase();
}

function sendPosition(): void
{
    $positionController = new PositionController();
    $positionController->sendPositionToDatabase();
}

function getData(): void
{
    $dataManager = new DataManager();
    $dataManager->encodeDataToJSON();
}

function removeUser(): void
{
    $userController = new UserController();
    $positionController = new PositionController();

    $positionController->id = $userController->getRowIdOfPositionFromDatabase();

    $positionController->removeFromDatabase();
    $userController->removeUserFromDatabase();
}

function sendGoal(): void
{
    $goalController = new GoalController();
    $goalController->sendGoalToDatabase();
}

function groupExists()
{
    $groupController = new GroupController();
    return $groupController->findGroupInDatabase();
}

function removeGoal(): void
{
    $goalController = new GoalController();
    $goalController->removeGoal();
}

function sendMessage(): void
{
    $messageController = new MessageController();
    $messageController->saveToDatabase();
    header("LOCATION: /index.php/map/active");
}

function sendImage(): void
{
    $cameraController = new CameraController();
    $cameraController->sendImage();
}