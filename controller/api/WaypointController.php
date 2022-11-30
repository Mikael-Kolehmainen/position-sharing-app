<?php
class WaypointController extends BaseController
{
    /** @var int */
    public $goalId;

    /** @var int */
    public $positionId;

    public function saveToDatabase(): void
    {
        $waypointModel = new WaypointModel();
        $waypointModel->goalId = $this->goalId;
        $waypointModel->positionId = $this->positionId;
        $waypointModel->save();
    }

    public function getRowIdsOfWaypointPositionsFromDatabase()
    {
        $waypointModel = new WaypointModel();
        $waypointModel->goalId = $this->goalId;

        return $waypointModel->getWithGoalId();
    }

    public function removeFromDatabase(): void
    {
        $waypointModel = new WaypointModel();
        $waypointModel->goalId = $this->goalId;
        
        $waypointModel->removeWithId();
    }
}