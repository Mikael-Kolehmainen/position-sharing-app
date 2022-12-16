<?php

namespace controller\api;

use Exception;
use model;
use model\PositionModel;
use manager;

class PositionController extends BaseController
{
    /** @var int */
    public $id;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    /** @var model\Database */
    private $db;

    public function __construct()
    {
        $this->db = new model\Database();
    }

    public function sendPositionToDatabase()
    {
        $json = json_decode(file_get_contents('php://input'));

        $this->latitude = $json->lat;
        $this->longitude = $json->lng;

        if (manager\SessionManager::getUserRowId() != null && $this->checkIfRowIdExistsInDatabase()) {
            $this->setUserPosition();
        } else {
            $this->saveToDatabase();
            $this->insertUserToDatabase();
        }
    }

    private function checkIfRowIdExistsInDatabase()
    {
        $userController = new UserController();
        return $userController->checkIfRowIdExistsInDatabase();
    }

    private function insertUserToDatabase(): void
    {
        $userController = new UserController();
        $userController->id = manager\SessionManager::getUserRowId();
        $userController->groupCode = manager\SessionManager::getGroupCode();
        $userController->positionsId = $this->id;
        $userController->initials = manager\SessionManager::getUserInitials();
        $userController->color = manager\SessionManager::getUserColor();
        $userController->saveToDatabase();

        manager\SessionManager::saveUserRowId($userController->id);
    }

    public function saveToDatabase()
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
        $userController->id = manager\SessionManager::getUserRowId();
        $user = $userController->getUser();
        $user->setPosition($position);
    }

    public function removeFromDatabase(): void
    {
        $position = new PositionModel($this->db, $this->id);
        $position->delete();
    }

    /** @return PositionModel */
    public function getPosition()
    {
        $positionModel = new PositionModel($this->db, $this->id);

        return $positionModel->load();
    }
}
