<?php

namespace manager;

use model\MessageModel;
use model\UserModel;
use model\GoalModel;
use controller\api\UserController;
use controller\api\GoalController;
use controller\api\MessageController;
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
            $this->data[self::GOALSDATA] = DATA_ALREADY_SAVED;
        } else {
            $this->data[self::GOALSDATA] = $this->getGoalsFromDatabase();
            $this->saveGoalSession();
        }

        echo json_encode($this->data);
    }

    private function saveGoalSession(): void
    {
        $goalController = new GoalController();
        SessionManager::saveGoalSession($goalController->getGoal()->goalSession);
    }

    /** @return UserModel[] */
    private function getUsersFromDatabase(): array
    {
        $userController = new UserController();
        $users = $userController->getMyGroupMembers();

        foreach ($users as $user) {
            $x = $user->loadPosition();
        }

        return $users;
    }

    /** @return MessageModel[] */
    private function getMessagesFromDatabase()
    {
        $messageController = new MessageController();
        $messages = $messageController->getMyGroupMessages();

        if (SessionManager::getAmountOfMessages() == null || SessionManager::getAmountOfMessages() != count($messages)) {
            $userController = new UserController();

            foreach ($messages as $message) {
                $userController->id = $message->userId;
                $markerStyle = $userController->getUser();

                if ($markerStyle->initials != null && $markerStyle->color != null) {
                    $message->initials = $markerStyle->initials;
                    $message->color = $markerStyle->color;
                }

                $message->sentByUser = $message->userId == SessionManager::getUserRowId();
            }

            SessionManager::saveAmountOfMessages(count((array)$messages));
        } else {
            $messages = DATA_ALREADY_SAVED;
        }

        return $messages;
    }

    private function goalSessionEqualsDbGoalSession(): bool
    {
        $goalController = new GoalController();

        return $goalController->goalSessionEqualsDbGoalSession();
    }

    /** @return GoalModel[] */
    private function getGoalsFromDatabase()
    {
        $goalController = new GoalController();
        $goals = $goalController->getMyGroupGoals();

        foreach ($goals as $goal) {
            $x = $goal->loadPositions();
            $y = $goal->getMyWaypoints();
        }

        if (count($goals) == 0) {
            $goals = DATA_EMPTY;
        }

        return $goals;
    }
}
