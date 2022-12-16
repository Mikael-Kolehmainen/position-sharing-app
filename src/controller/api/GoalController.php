<?php

namespace controller\api;

use model;
use manager;
use misc;
use model\GoalModel;

class GoalController extends BaseController
{
    private const FIELD_GOAL_SESSION = 'goalsession';

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

    /** @var model\Database */
    private $db;

    public function __construct()
    {
        $this->db = new model\Database();
    }

    public function saveToDatabase()
    {
        $goalModel = new model\GoalModel($this->db);
        $goalModel->startPositionId = $this->startPositionId;
        $goalModel->goalPositionId = $this->goalPositionId;
        $goalModel->groupCode = manager\SessionManager::getGroupCode();
        $goalModel->userId = $this->userId;
        $goalModel->goalOrderNumber = $this->goalOrderNumber;
        $goalModel->fallbackInitials = $this->fallbackInitials;
        
        return $goalModel->save();
    }

    private function updateGoalSessionInDatabase()
    {
        $goalModel = new model\GoalModel($this->db);
        $goalModel->goalSession = $this->goalSession;
        $goalModel->id = $this->id;
        $goalModel->update();
    }

    /** @return model\GoalModel[] */
    public function getMyGroupGoals(): array
    {
        $group = new model\GroupModel($this->db, manager\SessionManager::getGroupCode());

        return $group->getGroupGoals();
    }

    /** @return model\GoalModel */
    public function getGoal(): GoalModel
    {
        $goalModel = new GoalModel($this->db, manager\SessionManager::getGroupCode());
        return $goalModel->load();
    }

    public function goalSessionEqualsDbGoalSession()
    {
        $goalSession = $this->getGoal()->goalSession;

        return $goalSession == manager\SessionManager::getGoalSession();
    }

    public function createGoalSession()
    {
        $goalSession = misc\RandomString::getRandomString(15);

        if ($goalSession == manager\SessionManager::getGoalSession()) {
            $this->createGoalSession();
        } else {
            $this->goalSession = $goalSession;
        }
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

    private function getUser($id)
    {
        $userController = new UserController();
        $userController->id = $id;

        return $userController->getUser();
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

        manager\SessionManager::removeGoalSession();
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
        $goalModel = new model\GoalModel($this->db);
        $goalModel->groupCode = manager\SessionManager::getGroupCode();

        $goalModel->removeWithGroupCode();
    }
}