<?php
class DataManager
{
    private const USERSDATA = "usersdata";
    private const MESSAGESDATA = "messagesdata";

    private $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function encodeDataToJSON()
    {
        $this->getUsersDataFromDatabase();
        $this->getMessagesDataFromDatabase();

        if (SessionManager::getGoalSession() != null && $this->goalSessionEqualsDbGoalSession()) {
            $this->data[DATA_GOALSDATA] = DATA_ALREADY_SAVED;
        } else {
            $this->data[DATA_GOALSDATA] = getGoalDataFromDatabase();
            saveSession();
        }

        echo json_encode($this->data);
    }

    private function getUsersDataFromDatabase()
    {
        $userController = new UserController();

        $users = $userController->getMarkersFromDatabase();

        for ($i = 0; $i < count($users); $i++) {
            $users[$i][USER_POSITIONS] = self::getPositionFromDatabase($users[$i][USER_POSITIONS_ID]);
        }

        $this->data[self::USERSDATA] = $users;
    }

    private function getPositionFromDatabase($id)
    {
        $positionController = new PositionController();
        $positionController->id = $id;    

        return $positionController->getLatLngFromDatabase();
    }

    private function getMessagesDataFromDatabase()
    {
        $messageController = new MessageController();
        $messageData = $messageController->getMessagesFromDatabase();

        if (SessionManager::getAmountOfMessages() == null || SessionManager::getAmountOfMessages() != count($messageData)) {
            $userController = new UserController();

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
        $goalController = new GoalController();
        
        return $goalController->goalSessionEqualsDbGoalSession();
    }
}