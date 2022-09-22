<?php

class Waypoint
{
    private const TABLE_NAME = 'waypoints';
    private const FIELD_GOALS_ID = 'goals_id';
    private const FIELD_POSITIONS_ID = 'positions_id';

    /** @var int */
    public $id;

    /** @var int */
    public $goalsID;

    /** @var int */
    public $positionsID;

    public function __construct()
    {

    }

    public function getWaypointsPositionIDs()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_POSITIONS_ID . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GOALS_ID . ' = ?');
        $stmt->bindParam(1, $this->goalsID);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save()
    {
        if (empty($this->id)) {
            $this->insert();
        }
    }

    private function insert()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_GOALS_ID . ', ' . self::FIELD_POSITIONS_ID . ') VALUES (?, ?)');
        $stmt->bindParam(1, $this->goalsID);
        $stmt->bindParam(2, $this->positionsID);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }
}