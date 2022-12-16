<?php

namespace controller\api;

use model\Database;
use model\WaypointModel;


class WaypointController extends BaseController
{
    /** @var int */
    public $goalId;

    /** @var int */
    public $positionId;

    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function saveToDatabase(): void
    {
        $waypointModel = new WaypointModel($this->db, $this->goalId);
        $waypointModel->positionId = $this->positionId;
        $waypointModel->save();
    }

    public function removeFromDatabase(): void
    {
        $waypointModel = new WaypointModel($this->db, $this->goalId);
        
        $waypointModel->removeWithId();
    }
}