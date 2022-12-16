<?php

namespace controller\api;

use model;

class WaypointController extends BaseController
{
    /** @var int */
    public $goalId;

    /** @var int */
    public $positionId;

    /** @var model\Database */
    private $db;

    public function __construct()
    {
        $this->db = new model\Database();
    }

    public function saveToDatabase(): void
    {
        $waypointModel = new model\WaypointModel($this->db, $this->goalId);
        $waypointModel->positionId = $this->positionId;
        $waypointModel->save();
    }

    public function getRowIdsOfWaypointPositionsFromDatabase()
    {
        $waypointModel = new model\WaypointModel($this->db, $this->goalId);

        return $waypointModel->getWithGoalId();
    }

    public function removeFromDatabase(): void
    {
        $waypointModel = new model\WaypointModel($this->db, $this->goalId);
        
        $waypointModel->removeWithId();
    }
}