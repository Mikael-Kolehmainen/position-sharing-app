<?php
class UserController extends BaseController
{
    private const FIELD_INITIALS = 'initials';
    private const FIELD_COLOR = 'color';
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
        $userModel = new UserModel();
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
        $userModel = new UserModel();
        $userModel->id = $this->id;
        $userModel->removeWithID();
    }

    public function removeUsersFromDatabase()
    {
        $userModel = new UserModel();
        $userModel->groupCode = $this->groupCode;
        $userModel->removeWithGroupCode();
    }

    public function getMarkersFromDatabase()
    {
        $userModel = new UserModel();
        $userModel->groupCode = $this->groupCode;

        return $userModel->getWithGroupCode();
    }

    public function getMarkerFromDatabaseWithID()
    {
        $userModel = new UserModel();
        $userModel->id = $this->id;

        return $userModel->getWithId();
    }

    public function getIDsFromDatabase()
    {
        $userModel = new UserModel();
        $userModel->groupCode = $this->groupCode;
        $userData = $userModel->getWithGroupCode();
        $IDs = [];

        for ($i = 0; $i < count($userData); $i++) {
            $IDs[$i] = $userData[$i]["id"];
        }

        return $IDs;
    }

    public function getRowIdOfPositionFromDatabase()
    {
        $userModel = new UserModel();
        $userModel->id = $this->id;
        
        return $userModel->getWithId()[0][self::FIELD_POSITIONS_ID];
    }

    public function saveMarkerStyleToSession()
    {
        if ($this->color == "") {
            $this->color = "#FF0000";
        }
        
        $this->initials = strtoupper($this->initials);

        $_SESSION[USER_INITIALS] = $this->initials;
        $_SESSION[USER_COLOR] = $this->color;
    }
}