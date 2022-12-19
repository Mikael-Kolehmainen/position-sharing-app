<?php

namespace controller\api;

use model\GroupModel;
use model\GoalModel;
use model\Database;
use manager\SessionManager;
use misc\RandomString;
use model\UserModel;

class GoalController extends BaseController
{
    /** @var int */
    public $id;

    /** @var int */
    public $startPositionId;

    /** @var int */
    public $goalPositionId;

    /** @var int */
    public $goalOrderNumber;

    /** @var string */
    public $goalSession;

    /** @var string */
    public $userId;

    /** @var string */
    public $fallbackInitials;

    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    private function saveToDatabase()
    {
        $goalModel = new GoalModel($this->db, SessionManager::getGroupCode());
        $goalModel->startPositionId = $this->startPositionId;
        $goalModel->goalPositionId = $this->goalPositionId;
        $goalModel->userId = $this->userId;
        $goalModel->goalOrderNumber = $this->goalOrderNumber;
        $goalModel->fallbackInitials = $this->fallbackInitials;

        return $goalModel->save();
    }

    /** @return GoalModel[] */
    public function getMyGroupGoals(): array
    {
        $group = new GroupModel($this->db, SessionManager::getGroupCode());

        return $group->getGroupGoals();
    }

    /** @return GoalModel */
    public function getGoal(): GoalModel
    {
        $goalModel = new GoalModel($this->db, SessionManager::getGroupCode());

        return $goalModel->load();
    }

    public function goalSessionEqualsDbGoalSession()
    {
        $goalSession = $this->getGoal()->goalSession;

        return $goalSession == SessionManager::getGoalSession();
    }

    public function sendGoalToDatabase(): void
    {
        $json = json_decode(file_get_contents('php://input'));

        $rowIdsOfUsersWithGoal = $this->getOrderOfUsersWithGoal();

        $IDs = [];
        $i = 0;
        foreach ($json as $goal) {
            $this->id = $this->insertGoal($goal, $rowIdsOfUsersWithGoal, $i);
            $this->insertWaypoints($goal->routewaypoints);

            $IDs[$i] = $this->id;

            $i++;
        }

        $this->createGoalSession();
        $this->updateGoalSessionsInDatabase($IDs);
    }

    /** @return int[] */
    private function getOrderOfUsersWithGoal()
    {
        $json = json_decode(file_get_contents('php://input'));

        $userController = new UserController();
        $userIDs = $userController->getUserIdsForMyGroup();

        $rowIdsOfUsersWithGoal = [];
        $i = 0;
        foreach ($json as $item) {
            $rowIdsOfUsersWithGoal[$i++] = $userIDs[$item->goalordernumber];
        }

        return $rowIdsOfUsersWithGoal;
    }

    private function insertGoal($goal, $rowIdsOfUsersWithGoal, $i): int
    {
        $this->startPositionId = $this->insertPositionToDatabase($goal->startlat, $goal->startlng);
        $this->goalPositionId = $this->insertPositionToDatabase($goal->goallat, $goal->goallng);
        $this->goalOrderNumber = $goal->goalordernumber;
        $this->userId = $rowIdsOfUsersWithGoal[$i];
        $this->fallbackInitials = $this->getUser($rowIdsOfUsersWithGoal[$i])->initials;

        return $this->saveToDatabase();
    }

    private function insertPositionToDatabase($lat, $lng): int
    {
        $positionController = new PositionController();
        $positionController->latitude = $lat;
        $positionController->longitude = $lng;

        $positionController->saveToDatabase();

        return $positionController->id;
    }

    /** @return UserModel */
    private function getUser($id): UserModel
    {
        $userController = new UserController();
        $userController->id = $id;

        return $userController->getUser();
    }

    private function insertWaypoints($waypoints): void
    {
        foreach ($waypoints as $waypoint) {
            $waypointController = new WaypointController();
            $waypointController->goalId = $this->id;
            $waypointController->positionId = $this->insertPositionToDatabase($waypoint->lat, $waypoint->lng);;
            $waypointController->saveToDatabase();
        }
    }

    private function createGoalSession(): void
    {
        $goalSession = RandomString::getRandomString(15);

        if ($goalSession == SessionManager::getGoalSession()) {
            $this->createGoalSession();
        } else {
            $this->goalSession = $goalSession;
        }
    }

    private function updateGoalSessionsInDatabase($IDs): void
    {
        foreach ($IDs as $ID) {
            $goalModel = new GoalModel($this->db);
            $goalModel->goalSession = $this->goalSession;
            $goalModel->id = $ID;
            $goalModel->update();
        }
    }

    public function deleteGoal(): void
    {
        $this->deleteGoalPositions();
        $this->deleteGoalWaypoints();
        $this->deleteFromDatabase();
    }

    private function deleteGoalPositions(): void
    {
        foreach ($this->getMyGroupGoals() as $goal) {
            $this->deletePosition($goal->startPositionId);
            $this->deletePosition($goal->goalPositionId);

            foreach ($goal->getMyWaypoints()->waypointPositions as $waypoint) {
                $this->deletePosition($waypoint->id);
            }
        }

        SessionManager::removeGoalSession();
    }

    private function deletePosition($id): void
    {
        $positionController = new PositionController();
        $positionController->id = $id;
        $positionController->deleteFromDatabase();
    }

    private function deleteGoalWaypoints(): void
    {
        $waypointController = new WaypointController();

        foreach ($this->getMyGroupGoals() as $goal) {
            $waypointController->goalId = $goal->id;
            $waypointController->deleteFromDatabase();
        }
    }

    private function deleteFromDatabase(): void
    {
        $goalModel = new GoalModel($this->db);
        $goalModel->groupCode = SessionManager::getGroupCode();

        $goalModel->deleteWithGroupCode();
    }
}