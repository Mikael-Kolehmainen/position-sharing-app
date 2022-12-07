<?php

namespace controller\api;

use model;
use manager;
use misc;

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

    public function saveToDatabase()
    {
        $goalModel = new model\GoalModel();
        $goalModel->startPositionId = $this->startPositionId;
        $goalModel->goalPositionId = $this->goalPositionId;
        $goalModel->groupCode = manager\SessionManager::getGroupCode();
        $goalModel->userId = $this->userId;
        $goalModel->goalOrderNumber = $this->goalOrderNumber;
        $goalModel->fallbackInitials = $this->fallbackInitials;
        return $goalModel->save();
    }

    public function updateGoalSessionInDatabase()
    {
        $goalModel = new model\GoalModel();
        $goalModel->goalSession = manager\SessionManager::getGoalSession();
        $goalModel->id = $this->id;
        $goalModel->update();
    }

    public function getIdsFromDatabase()
    {
        $goalModel = new model\GoalModel();
        $goalModel->groupCode = manager\SessionManager::getGroupCode();

        return $goalModel->getWithGroupCode();
    }

    public function getGoalSessionFromDatabase()
    {
        $goalModel = new model\GoalModel();
        $goalModel->groupCode = manager\SessionManager::getGroupCode();

        return isset($goalModel->getWithGroupCode()[0][self::FIELD_GOAL_SESSION]) ? $goalModel->getWithGroupCode()[0][self::FIELD_GOAL_SESSION] : null;
    }

    public function goalSessionEqualsDbGoalSession()
    {
        $goalSession = $this->getGoalSessionFromDatabase();

        return $goalSession == manager\SessionManager::getGoalSession();
    }

    public function getOrderNumbersOfGoalsFromDatabase()
    {
        $goalModel = new model\GoalModel();
        $goalModel->groupCode = manager\SessionManager::getGroupCode();

        return $goalModel->getWithGroupCode();
    }

    public function getRowIdsOfGoalPositionsFromDatabase()
    {
        $goalModel = new model\GoalModel();
        $goalModel->groupCode = manager\SessionManager::getGroupCode();
        
        return $goalModel->getWithGroupCode();
    }

    public function getFallbackInitialsFromDatabase()
    {
        $goalModel = new model\GoalModel();
        $goalModel->groupCode = manager\SessionManager::getGroupCode();

        return $goalModel->getWithGroupCode();
    }

    public function createGoalSession()
    {
        $goalSession = misc\RandomString::getRandomString(15);

        if ($goalSession == $_SESSION[SESSION_GOALSESSION]) {
            $this->createGoalSession();
        } else {
            $this->goalSession = $goalSession;
        }
    }

    public function removeFromDatabase()
    {
        $goalModel = new model\GoalModel();
        $goalModel->groupCode = manager\SessionManager::getGroupCode();

        $goalModel->removeWithGroupCode();
    }

    public function sendGoalToDatabase()
    {
        $userController = new UserController();

        $json = json_decode(file_get_contents('php://input'));
        
        $userIDs = $userController->getIDsFromDatabase();

        $rowIdsOfUsersWithGoal = [];

        for ($i = 0; $i < count($json); $i++) {
            $rowIdsOfUsersWithGoal[$i] = $userIDs[$json[$i]->goalordernumber];
        }

        $goalRowIds = [];

        for ($i = 0; $i < count($json); $i++) {
            $jsonObj = $json[$i];
            
            $startPositionRowID = $this->insertPositionToDatabase($jsonObj->startlat, $jsonObj->startlng);
            $goalPositionRowID = $this->insertPositionToDatabase($jsonObj->goallat, $jsonObj->goallng);

            $fallBackInitials = $this->getUserInitialsFromDatabase($rowIdsOfUsersWithGoal[$i])[0][USER_INITIALS];
            
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

    private function getUserInitialsFromDatabase($id)
    {
        $userController = new UserController();
        $userController->id = $id;

        return $userController->getMarkerFromDatabaseWithID();
    }

    public function removeGoal(): void
    {
        $goalsIds = $this->getIdsFromDatabase();
    
        $this->removeGoalPositions($goalsIds);
        $this->removeGoalWaypoints($goalsIds);
        $this->removeFromDatabase();
    }

    private function removeGoalPositions($goalsIds): void
    {
        $positionController = new PositionController();

        $rowIdsOfGoalPositions = $this->getRowIdsOfGoalPositionsFromDatabase();
        $rowIdsOfWaypointPositions = $this->getRowIdsOfWaypointPositions($goalsIds);

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

        manager\SessionManager::removeGoalSession();
    }

    private function getRowIdsOfWaypointPositions($goalsIds)
    {
        $waypointController = new WaypointController();
        $rowIdOfPositions = [];

        for ($i = 0; $i < count($goalsIds); $i++) {
            $waypointController->goalId = $goalsIds[$i]["id"];
            $rowIdOfPositions[$i] = $waypointController->getRowIdsOfWaypointPositionsFromDatabase();
        }
        
        return $rowIdOfPositions;
    }

    private function removeGoalWaypoints($goalsIds): void
    {
        $waypointController = new WaypointController();

        for ($i = 0; $i < count($goalsIds); $i++) {
            $waypointController->goalId = $goalsIds[$i]["id"];
            $waypointController->removeFromDatabase();
        }
    }
}