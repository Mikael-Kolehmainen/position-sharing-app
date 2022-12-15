<?php
require __DIR__ . "/inc/bootstrap.php";

session_start();

$uri = manager\ServerRequestManager::getUriParts();

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
                if (manager\ServerRequestManager::issetCreateGroup()) {
                    Create();
                } else {
                    misc\Redirect::redirect("Please fill the group creation form.", "/index.php");
                }
                break;
            case "search":
                if (manager\ServerRequestManager::issetSearchGroup()) {
                    Search();
                } else {
                    misc\Redirect::redirect("Please fill the search group form.", "/index.php");
                }
                break;
            case "active":
                if (manager\SessionManager::issetGroupCode()) {
                    ActiveMap();
                } else {
                    misc\Redirect::redirect("Your session has expired, try again.", "/index.php");
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
        if (manager\ServerRequestManager::isPost() || manager\ServerRequestManager::isGet()) {
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
    $homeController = new controller\basic\HomeController();
    $homeController->showHomePage();
}

function CreateForm(): void
{
    $createController = new controller\basic\CreateController();
    $createController->showCreatePage();
}

function SearchForm(): void
{
    $searchController = new controller\basic\SearchController();
    $searchController->showSearchPage();
}

function Create(): void
{
    $groupController = new controller\api\GroupController();
    $groupController->saveToDatabase();

    saveMarkerStyleToSession();
    redirectToGroupMap();
}

function Search(): void
{
    $groupController = new controller\api\GroupController();
    

    if ($groupController->findGroupInDatabase()) {
        saveMarkerStyleToSession();
        redirectToGroupMap();
    } else {
        misc\Redirect::redirect("Couldn\'t find a group with the given code.", "/index.php/search");
    }
}

function redirectToGroupMap(): void
{
    header("LOCATION: /index.php/map/active");
}

function saveMarkerStyleToSession(): void
{
    $userController = new controller\api\UserController();
    $userController->saveMarkerStyleToSession();
}

function ActiveMap(): void
{
    $activeController = new controller\api\ActiveMapController();
    $activeController->showMapPage();
}

function Camera(): void
{
    $cameraController = new controller\api\CameraController();
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
    $groupController = new controller\api\GroupController();
    $groupController->removeGroupFromDatabase();
}

function removeGroupUsers(): void
{
    $userController = new controller\api\UserController();
    $userController->removeUsersFromDatabase();
}

function removeGroupMessages(): void
{
    $messageController = new controller\api\MessageController();
    $messageController->removeMessagesFromDatabase();
}

function sendPosition(): void
{
    $positionController = new controller\api\PositionController();
    $positionController->sendPositionToDatabase();
}

function getData(): void
{
    $dataManager = new manager\DataManager();
    $dataManager->encodeDataToJSON();
}

function removeUser(): void
{
    $userController = new controller\api\UserController();
    $positionController = new controller\api\PositionController();

    $positionController->id = $userController->getUserPositionId();

    $positionController->removeFromDatabase();
    $userController->removeUserFromDatabase();
}

function sendGoal(): void
{
    $goalController = new controller\api\GoalController();
    $goalController->sendGoalToDatabase();
}

function groupExists()
{
    $groupController = new controller\api\GroupController();
    return $groupController->findGroupInDatabase();
}

function removeGoal(): void
{
    $goalController = new controller\api\GoalController();
    $goalController->removeGoal();
}

function sendMessage(): void
{
    $messageController = new controller\api\MessageController();
    $messageController->saveToDatabase();
    redirectToGroupMap();
}

function sendImage(): void
{
    $cameraController = new controller\api\CameraController();
    $cameraController->sendImage();
}