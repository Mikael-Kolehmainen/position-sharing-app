<?php

namespace controller\api;

use model;
use manager;

class UserController extends BaseController
{
    private const FIELD_POSITIONS_ID = 'positions_id';

    /** @var int */
    public $id;

    /** @var int */
    public $positionsId;

    /** @var string */
    public $initials;

    /** @var string */
    public $color;

    /** @var string */
    public $groupCode;

    public function saveToDatabase()
    {
        $userModel = new model\UserModel();
        $userModel->id = $this->id;
        $userModel->positionsId = $this->positionsId;
        $userModel->initials = $this->initials;
        $userModel->color = $this->color;
        $userModel->groupCode = $this->groupCode;
        $userModel->save();

        $this->id = $userModel->id;
    }

    public function removeUserFromDatabase()
    {
        $userModel = new model\UserModel();
        $userModel->id = manager\SessionManager::getUserRowId();
        $userModel->removeWithID();
        $this->removeSessions();
    }

    private function removeSessions()
    {
        manager\SessionManager::removeGoalSession();
        manager\SessionManager::removeAmountOfMessages();
    }

    public function removeUsersFromDatabase()
    {
        $userModel = new model\UserModel();
        $userModel->groupCode = manager\SessionManager::getGroupCode();
        $userModel->removeWithGroupCode();
    }

    public function getMarkersFromDatabase()
    {
        $userModel = new model\UserModel();
        $userModel->groupCode = manager\SessionManager::getGroupCode();

        return $userModel->getWithGroupCode();
    }

    public function getMarkerFromDatabaseWithID()
    {
        $userModel = new model\UserModel();
        $userModel->id = $this->id;

        return $userModel->getWithId();
    }

    public function getIDsFromDatabase()
    {
        $userModel = new model\UserModel();
        $userModel->groupCode = manager\SessionManager::getGroupCode();
        $userData = $userModel->getWithGroupCode();
        $IDs = [];

        for ($i = 0; $i < count($userData); $i++) {
            $IDs[$i] = $userData[$i]["id"];
        }

        return $IDs;
    }

    public function getRowIdOfPositionFromDatabase()
    {
        $userModel = new model\UserModel();
        $userModel->id = manager\SessionManager::getUserRowId();
        
        return $userModel->getWithId()[0][self::FIELD_POSITIONS_ID];
    }

    public function saveMarkerStyleToSession()
    {
        $this->initials = manager\ServerRequestManager::postUserInitials();
        $this->color = manager\ServerRequestManager::postUserColor();

        if ($this->color == "") {
            $this->color = "#FF0000";
        }
        
        $this->initials = strtoupper($this->initials);

        manager\SessionManager::saveUserInitials($this->initials);
        manager\SessionManager::saveUserColor($this->color);
    }

    public function checkIfRowIdExistsInDatabase()
    {
        $IDs = self::getIDsFromDatabase();

        for ($i = 0; $i < count($IDs); $i++) {
            if ($IDs[$i] == manager\SessionManager::getUserRowId()) {
                return true;
            }
        }

        return false;
    }
}