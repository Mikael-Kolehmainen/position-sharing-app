<?php
const PROJECT_ROOT_PATH = __DIR__ . "/../";

require_once PROJECT_ROOT_PATH . "/inc/config.php";

require_once PROJECT_ROOT_PATH . "/manager/SessionManager.php";
require_once PROJECT_ROOT_PATH . "/manager/DataManager.php";

require_once PROJECT_ROOT_PATH . "/controller/api/BaseController.php";

require_once PROJECT_ROOT_PATH . "/model/UserModel.php";
require_once PROJECT_ROOT_PATH . "/model/GroupModel.php";
require_once PROJECT_ROOT_PATH . "/model/PositionModel.php";
require_once PROJECT_ROOT_PATH . "/model/WaypointModel.php";
require_once PROJECT_ROOT_PATH . "/model/GoalModel.php";
require_once PROJECT_ROOT_PATH . "/model/MessageModel.php";

require_once PROJECT_ROOT_PATH . "/misc/RandomString.php";
require_once PROJECT_ROOT_PATH . "/misc/Redirect.php";