<?php
namespace manager;

use controller;

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
        $this->getUsersDataFromDatabase();
        $this->getMessagesDataFromDatabase();

        if (SessionManager::getGoalSession() != null && $this->goalSessionEqualsDbGoalSession()) {
            $this->data[DATA_GOALSDATA] = DATA_ALREADY_SAVED;
        } else {
            $this->getGoalDataFromDatabase();
            $this->saveGoalSession();
        }

        echo json_encode($this->data);
    }

    private function saveGoalSession(): void
    {
        $goalController = new controller\api\GoalController();
        $_SESSION[SESSION_GOALSESSION] = $goalController->getGoalSessionFromDatabase();
    }

    private function getUsersDataFromDatabase(): void
    {
        $userController = new controller\api\UserController();

        $users = $userController->getMarkersFromDatabase();

        for ($i = 0; $i < count($users); $i++) {
            $users[$i][USER_POSITIONS] = $this->getPositionFromDatabase($users[$i][USER_POSITIONS_ID]);
        }

        $this->data[self::USERSDATA] = $users;
    }

    private function getPositionFromDatabase($id)
    {
        $positionController = new controller\api\PositionController();
        $positionController->id = $id;    

        return $positionController->getLatLngFromDatabase();
    }

    private function getMessagesDataFromDatabase(): void
    {
        $messageController = new controller\api\MessageController();
        $messageData = $messageController->getMessagesFromDatabase();

        if (SessionManager::getAmountOfMessages() == null || SessionManager::getAmountOfMessages() != count($messageData)) {
            $userController = new controller\api\UserController();

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

            SessionManager::saveAmountOfMessages(count($messageData));
        } else {
            $messageData = DATA_ALREADY_SAVED;
        }

        $this->data[self::MESSAGESDATA] = $messageData;
    }

    private function goalSessionEqualsDbGoalSession()
    {
        $goalController = new controller\api\GoalController();
        
        return $goalController->goalSessionEqualsDbGoalSession();
    }

    private function getGoalDataFromDatabase(): void
    {
        $goalController = new controller\api\GoalController();

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

    private function getWaypointPositionsFromDatabase($i)
    {
        $goalController = new controller\api\GoalController();
        $positionController = new controller\api\PositionController();
        $waypointController = new controller\api\WaypointController();

        $waypointController->goalId = $goalController->getIdsFromDatabase()[$i]["id"];
        $waypointPositionsRowIds = $waypointController->getRowIdsOfWaypointPositionsFromDatabase();

        $waypoints = [];

        for ($j = 0; $j < count($waypointPositionsRowIds); $j++) {
            $positionController->id = $waypointPositionsRowIds[$j][USER_POSITIONS_ID];
            $waypoints[$j] = $positionController->getLatLngFromDatabase();
        }

        return $waypoints;
    }
}