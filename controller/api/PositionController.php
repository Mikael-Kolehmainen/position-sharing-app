<?php
class PositionController extends BaseController
{
    /** @var int */
    public $id;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    public function saveToDatabase()
    {
        $positionModel = new PositionModel();
        $positionModel->latitude = $this->latitude;
        $positionModel->longitude = $this->longitude;
        
        return $positionModel->save();
    }

    public function updateInDatabase(): void
    {
        $positionModel = new PositionModel();
        $positionModel->id = $this->id;
        $positionModel->latitude = $this->latitude;
        $positionModel->longitude = $this->longitude;

        $this->id = $positionModel->update();
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

        $latlngs = $positionModel->getLatLng();

        return [$latlngs[0][POSITION_LAT], $latlngs[0][POSITION_LNG]];
    }
}