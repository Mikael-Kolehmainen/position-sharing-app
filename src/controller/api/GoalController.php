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

    private function updateGoalSessionInDatabase()
    {
        $goalModel = new GoalModel($this->db);
        $goalModel->goalSession = $this->goalSession;
        $goalModel->id = $this->id;
        $goalModel->update();
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

    public function sendGoalToDatabase()
    {
        $userController = new UserController();

        $json = json_decode(file_get_contents('php://input'));

        $userIDs = $userController->getUserIdsForMyGroup();

        $rowIdsOfUsersWithGoal = [];

        for ($i = 0; $i < count($json); $i++) {
            $rowIdsOfUsersWithGoal[$i] = $userIDs[$json[$i]->goalordernumber];
        }

        $goalRowIds = [];

        for ($i = 0; $i < count($json); $i++) {
            $jsonObj = $json[$i];

            $startPositionRowID = $this->insertPositionToDatabase($jsonObj->startlat, $jsonObj->startlng);
            $goalPositionRowID = $this->insertPositionToDatabase($jsonObj->goallat, $jsonObj->goallng);

            $fallBackInitials = $this->getUser($rowIdsOfUsersWithGoal[$i])->initials;

            $this->startPositionId = $startPositionRowID;
            $this->goalPositionId = $goalPositionRowID;
            $this->goalOrderNumber = $jsonObj->goalordernumber;
            $this->userId = $rowIdsOfUsersWithGoal[$i];
            $this->fallbackInitials = $fallBackInitials;
            $goalRowID = $this->saveToDatabase();

            $goalRowIds[$i] = $goalRowID;

            $waypoints = $jsonObj->routewaypoints;
            for ($j = 0; $j < count($waypoints); $j++) {
                $waypointPositionRowID = $this->insertPositionToDatabase($waypoints[$j]->lat, $waypoints[$j]->lng);

                $waypointController = new WaypointController();
                $waypointController->goalId = $goalRowID;
                $waypointController->positionId = $waypointPositionRowID;
                $waypointController->saveToDatabase();
            }
        }

        $this->createGoalSession();

        for ($i = 0; $i < count($goalRowIds); $i++) {
            $this->id = $goalRowIds[$i];
            $this->updateGoalSessionInDatabase();
        }
    }

    private function insertPositionToDatabase($lat, $lng)
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

    private function createGoalSession(): void
    {
        $goalSession = RandomString::getRandomString(15);

        if ($goalSession == SessionManager::getGoalSession()) {
            $this->createGoalSession();
        } else {
            $this->goalSession = $goalSession;
        }
    }

    public function removeGoal(): void
    {
        $this->removeGoalPositions();
        $this->removeGoalWaypoints();
        $this->removeFromDatabase();
    }

    private function removeGoalPositions(): void
    {
        $positionController = new PositionController();

        foreach ($this->getMyGroupGoals() as $goal) {
            $positionController->id = $goal->startPositionId;
            $positionController->removeFromDatabase();

            $positionController->id = $goal->goalPositionId;
            $positionController->removeFromDatabase();

            foreach ($goal->getMyWaypoints()->waypointPositions as $waypoint) {
                $positionController->id = $waypoint->id;
                $positionController->removeFromDatabase();
            }
        }

        SessionManager::removeGoalSession();
    }

    private function removeGoalWaypoints(): void
    {
        $waypointController = new WaypointController();

        foreach ($this->getMyGroupGoals() as $goal) {
            $waypointController->goalId = $goal->id;
            $waypointController->removeFromDatabase();
        }
    }

    private function removeFromDatabase(): void
    {
        $goalModel = new GoalModel($this->db);
        $goalModel->groupCode = SessionManager::getGroupCode();

        $goalModel->removeWithGroupCode();
    }
}