<?php

namespace controller\api;

use Exception;
use model;
use model\PositionModel;
use model\Database;
use manager\SessionManager;

class PositionController extends BaseController
{
    /** @var int */
    public $id;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function sendPositionToDatabase(): void
    {
        $json = json_decode(file_get_contents('php://input'));

        $this->latitude = $json->lat;
        $this->longitude = $json->lng;

        if (SessionManager::getUserRowId() != null && $this->checkIfRowIdExistsInDatabase()) {
            $this->setUserPosition();
        } else {
            $this->saveToDatabase();
            $this->insertUserToDatabase();
        }
    }

    private function checkIfRowIdExistsInDatabase(): bool
    {
        $userController = new UserController();
        return $userController->checkIfRowIdExistsInDatabase();
    }

    private function insertUserToDatabase(): void
    {
        $userController = new UserController();
        $userController->id = SessionManager::getUserRowId();
        $userController->groupCode = SessionManager::getGroupCode();
        $userController->positionsId = $this->id;
        $userController->initials = SessionManager::getUserInitials();
        $userController->color = SessionManager::getUserColor();
        $userController->saveToDatabase();

        SessionManager::saveUserRowId($userController->id);
    }

    public function saveToDatabase(): void
    {
        $positionModel = new PositionModel($this->db);
        $positionModel->latitude = $this->latitude;
        $positionModel->longitude = $this->longitude;

        $this->id = $positionModel->set();
    }

    public function setUserPosition(): void
    {
        $position = new PositionModel($this->db);
        $position->latitude = $this->latitude;
        $position->longitude = $this->longitude;

        $userController = new UserController();
        $userController->id = SessionManager::getUserRowId();
        $user = $userController->getUser();
        $user->setPosition($position);
    }

    public function deleteFromDatabase(): void
    {
        $position = new PositionModel($this->db, $this->id);
        $position->delete();
    }
}
