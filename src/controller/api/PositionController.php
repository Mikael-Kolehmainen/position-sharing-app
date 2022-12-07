<?php

namespace controller\api;

use model;
use manager;

class PositionController extends BaseController
{
    /** @var int */
    public $id;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    public function sendPositionToDatabase()
    {
        $json = json_decode(file_get_contents('php://input'));

        $this->latitude = $json->lat;
        $this->longitude = $json->lng;

        if (manager\SessionManager::getUserRowId() != null && $this->checkIfRowIdExistsInDatabase()) {
            $this->updateInDatabase();
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
        $positionModel = new model\PositionModel();
        $positionModel->latitude = $this->latitude;
        $positionModel->longitude = $this->longitude;
        
        $this->id = $positionModel->save();
    }

    public function updateInDatabase(): void
    {
        $positionModel = new model\PositionModel();
        $positionModel->id = $this->getRowIdOfPositionFromDatabase();;
        $positionModel->latitude = $this->latitude;
        $positionModel->longitude = $this->longitude;
    }

    private function getRowIdOfPositionFromDatabase()
    {
        $userController = new UserController();

        return $userController->getRowIdOfPositionFromDatabase();
    }

    public function removeFromDatabase()
    {
        $positionModel = new model\PositionModel();
        $positionModel->id = $this->id;

        $positionModel->removeWithId();
    }

    public function getLatLngFromDatabase()
    {
        $positionModel = new model\PositionModel();
        $positionModel->id = $this->id;

        $latlngs = $positionModel->get();

        return [$latlngs[0][POSITION_LAT], $latlngs[0][POSITION_LNG]];
    }
}