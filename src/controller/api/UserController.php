<?php

namespace controller\api;

use Exception;
use manager\SessionManager;
use manager\ServerRequestManager;
use model\Database;
use model\UserModel;

class UserController extends BaseController
{
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

    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function saveToDatabase()
    {
        $userModel = new UserModel($this->db);
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
        $userModel = new UserModel($this->db);
        $userModel->id = SessionManager::getUserRowId();
        $userModel->delete();
        $this->removeSessions();
    }

    private function removeSessions()
    {
        SessionManager::removeGoalSession();
        SessionManager::removeAmountOfMessages();
    }

    public function removeUsersFromDatabase()
    {
        $userModel = new UserModel($this->db, SessionManager::getGroupCode());
        $userModel->removeWithGroupCode();
    }

    /** @return UserModel[] */
    public function getMyGroupMembers()
    {
        $groupModel = new \model\GroupModel($this->db, SessionManager::getGroupCode());

        return $groupModel->getGroupMembers();
    }

    public function getMarkerFromDatabaseWithID()
    {
        $userModel = new UserModel($this->db, $this->id);

        return $userModel->load();
    }

    public function getUserIdsForMyGroup()
    {
        $IDs = [];
        $i = 0;
        foreach ($this->getMyGroupMembers() as $user) {
            $IDs[$i++] = $user->id;
        }

        return $IDs;
    }

    /** @return int */
    public function getUserPositionId()
    {
        return $this->getUser()->positionsId;
    }

    public function getUser(): UserModel
    {
        $userModel = new UserModel($this->db, SessionManager::getUserRowId());
        return $userModel->load();
    }

    public function saveMarkerStyleToSession()
    {
        $this->initials = ServerRequestManager::postUserInitials();
        $this->color = ServerRequestManager::postUserColor();

        if ($this->color == "") {
            $this->color = "#FF0000";
        }

        $this->initials = strtoupper($this->initials);

        SessionManager::saveUserInitials($this->initials);
        SessionManager::saveUserColor($this->color);
    }

    public function checkIfRowIdExistsInDatabase()
    {
        foreach ($this->getMyGroupMembers() as $user) {
            if ($user->id == SessionManager::getUserRowId()) {
                return true;
            }
        }
        return false;
    }
}
