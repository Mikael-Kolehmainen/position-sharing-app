<?php
use controller\basic\HomeController;
use controller\basic\CreateController;
use controller\basic\SearchController;
use controller\api\ActiveMapController;
use controller\api\GoalController;
use controller\api\PositionController;
use controller\api\UserController;
use controller\api\MessageController;
use controller\api\GroupController;
use controller\api\CameraController;
use manager\SessionManager;
use manager\DataManager;
use manager\ServerRequestManager;
use misc\Redirect;

require __DIR__ . "/inc/bootstrap.php";

session_start();

$uri = ServerRequestManager::getUriParts();

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
                if (ServerRequestManager::issetCreateGroup()) {
                    Create();
                } else {
                    Redirect::redirect("Please fill the group creation form.", "/index.php");
                }
                break;
            case "search":
                if (ServerRequestManager::issetSearchGroup()) {
                    Search();
                } else {
                    Redirect::redirect("Please fill the search group form.", "/index.php");
                }
                break;
            case "active":
                if (SessionManager::issetGroupCode()) {
                    ActiveMap();
                } else {
                    Redirect::redirect("Your session has expired, try again.", "/index.php");
                }
                break;
            case "camera":
                Camera();
                break;
            case "remove-group":
                Delete();
                break;
            case null: default:
                header("HTTP/1.1 404 Not Found");
                exit();
        }
        break;
    case "ajax":
        if (ServerRequestManager::isPost() || ServerRequestManager::isGet()) {
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
                    deleteUser();
                    break;
                case "remove-goal":
                    deleteGoal();
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

    saveMarkerStyleToSession();
    redirectToGroupMap();
}

function Search(): void
{
    $groupController = new GroupController();


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
    $userController = new UserController();
    $userController->saveMarkerStyleToSession();
}

function ActiveMap(): void
{
    $activeController = new ActiveMapController();
    $activeController->showMapPage();
}

function Camera(): void
{
    $cameraController = new CameraController();
    $cameraController->showCamera();
}

function Delete(): void
{
    deleteGroup();
    deleteGroupUsers();
    deleteGroupMessages();
    deleteGoal();

    header("LOCATION: /index.php");
}

function deleteGroup(): void
{
    $groupController = new GroupController();
    $groupController->removeGroupFromDatabase();
}

function deleteGroupUsers(): void
{
    $userController = new UserController();
    $positionController = new PositionController();

    foreach ($userController->getPositionIdsForMyGroup() as $positionId) {
        $positionController->id = $positionId;
        $positionController->deleteFromDatabase();
    }

    $userController->deleteUsersFromDatabase();
}

function deleteGroupMessages(): void
{
    $messageController = new MessageController();
    $messageController->deleteMessagesFromDatabase();
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

function deleteUser(): void
{
    $userController = new UserController();
    $positionController = new PositionController();

    $userController->id = SessionManager::getUserRowId();
    $positionController->id = $userController->getUser()->positionsId;

    $positionController->deleteFromDatabase();
    $userController->deleteUserFromDatabase();
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

function deleteGoal(): void
{
    $goalController = new GoalController();
    $goalController->deleteGoal();
}

function sendMessage(): void
{
    $messageController = new MessageController();
    $messageController->saveToDatabase();
    redirectToGroupMap();
}

function sendImage(): void
{
    $cameraController = new CameraController();
    $cameraController->sendImage();
}