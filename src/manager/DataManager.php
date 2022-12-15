<?php

namespace manager;

use model;
use controller;
use controller\api\UserController;
use controller\api\GoalController;
use controller\api\PositionController;
use Exception;

class DataManager
{
    private const USERSDATA = "usersdata";
    private const MESSAGESDATA = "messagesdata";
    private const GOALSDATA = "goalsdata";

    private $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function encodeDataToJSON(): void
    {
        $this->data[self::USERSDATA] = $this->getUsersFromDatabase();
        $this->data[self::MESSAGESDATA] = $this->getMessagesFromDatabase();

        if (SessionManager::getGoalSession() != null && $this->goalSessionEqualsDbGoalSession()) {
            $this->data[DATA_GOALSDATA] = DATA_ALREADY_SAVED;
        } else {
            $this->getGoalsFromDatabase();
            $this->saveGoalSession();
        }

        echo json_encode($this->data);
    }

    private function saveGoalSession(): void
    {
        $goalController = new GoalController();
        SessionManager::saveGoalSession($goalController->getGoalSessionFromDatabase());
    }

    /** @return model\UserModel[] */
    private function getUsersFromDatabase(): array
    {
        $userController = new UserController();
        $users = $userController->getMyGroupMembers();

        foreach ($users as $user) {
            $x = $user->loadPosition();
        }

        return $users;
    }

    /** @return model\MessageModel[] */
    private function getMessagesFromDatabase()
    {
        $messageController = new controller\api\MessageController();
        $messages = $messageController->getMyGroupMessages();

        if (SessionManager::getAmountOfMessages() == null || SessionManager::getAmountOfMessages() != count((array)$messages)) {
            $userController = new UserController();

            foreach ($messages as $message) {
                $userController->id = $message->userId;

                $markerStyle = $userController->getMarkerFromDatabaseWithID();

                if ($markerStyle->initials != null && $markerStyle->color != null) {
                    $message->initials = $markerStyle->initials;
                    $message->color = $markerStyle->color;
                }

                $message->sentByUser = $message->userId == SessionManager::getUserRowId();

                unset($message->userId);
            }

            SessionManager::saveAmountOfMessages(count((array)$messages));
        } else {
            $messages = DATA_ALREADY_SAVED;
        }

        return $messages;
    }

    private function goalSessionEqualsDbGoalSession()
    {
        $goalController = new GoalController();

        return $goalController->goalSessionEqualsDbGoalSession();
    }

    private function getGoalsFromDatabase(): void
    {
        $goalController = new GoalController();
        $goals = $goalController->getMyGroupGoals();

        foreach ($goals as $goal) {
            $x = $goal->loadStartPosition();
            $y = $goal->loadGoalPosition();


        }


        $rowIdsOfGoalPositions = $goalController->getRowIdsOfGoalPositionsFromDatabase();

        $goalsData = [];

        if (count($rowIdsOfGoalPositions) > 0) {
            $orderNumbers = $goalController->getOrderNumbersOfGoalsFromDatabase();
            $fallBackInitials = $goalController->getFallbackInitialsFromDatabase();

            for ($i = 0; $i < count($rowIdsOfGoalPositions); $i++) {
                $goalsData[$i][GOAL_ORDER_NUMBER] = $orderNumbers[$i][GOAL_ORDER_NUMBER];

                $goalsData[$i][GOAL_START_POSITION] = $this->getPositionFromDatabase($rowIdsOfGoalPositions[$i][GOAL_START_POSITIONS_ID]);
                $goalsData[$i][GOAL_GOAL_POSITION] = $this->getPositionFromDatabase($rowIdsOfGoalPositions[$i][GOAL_GOAL_POSITIONS_ID]);

                $goalsData[$i][GOAL_WAYPOINTS] = $this->getWaypointPositionsFromDatabase($i);

                $goalsData[$i][USER_FALLBACK_INITIALS] = $fallBackInitials[$i][USER_FALLBACK_INITIALS];
            }
        } else {
            $goalsData = DATA_EMPTY;
        }

        $this->data[self::GOALSDATA] = $goalsData;
    }

    private function getPositionFromDatabase(int $id): \model\PositionModel
    {
        $positionController = new PositionController();
        $positionController->id = $id;

        return $positionController->getPosition();
    }

    private function getWaypointPositionsFromDatabase($i)
    {
        $goalController = new GoalController();
        $positionController = new PositionController();
        $waypointController = new controller\api\WaypointController();

        $waypointController->goalId = $goalController->getIdsFromDatabase()[$i]["id"];
        $waypointPositionsRowIds = $waypointController->getRowIdsOfWaypointPositionsFromDatabase();

        $waypoints = [];

        for ($j = 0; $j < count($waypointPositionsRowIds); $j++) {
            $positionController->id = $waypointPositionsRowIds[$j][USER_POSITIONS_ID];
            $waypoints[$j] = $positionController->getPosition();
        }

        return $waypoints;
    }
}
