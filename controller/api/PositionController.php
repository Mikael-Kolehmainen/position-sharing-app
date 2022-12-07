<?php
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

        if (SessionManager::getUserRowId() != null && $this->checkIfRowIdExistsInDatabase()) {
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
        $userController->id = SessionManager::getUserRowId();
        $userController->groupCode = SessionManager::getGroupCode();
        $userController->positionsId = $this->id;
        $userController->initials = SessionManager::getUserInitials();
        $userController->color = SessionManager::getUserColor();
        $userController->saveToDatabase();

        SessionManager::saveUserRowId($userController->id);
    }

    public function saveToDatabase()
    {
        $positionModel = new PositionModel();
        $positionModel->latitude = $this->latitude;
        $positionModel->longitude = $this->longitude;
        
        $this->id = $positionModel->save();
    }

    public function updateInDatabase(): void
    {
        $positionModel = new PositionModel();
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
        $positionModel = new PositionModel();
        $positionModel->id = $this->id;

        $positionModel->removeWithId();
    }

    public function getLatLngFromDatabase()
    {
        $positionModel = new PositionModel();
        $positionModel->id = $this->id;

        $latlngs = $positionModel->get();

        return [$latlngs[0][POSITION_LAT], $latlngs[0][POSITION_LNG]];
    }
}