<?php

namespace model;

class WaypointModel
{
    private const TABLE_NAME = 'waypoints';
    private const FIELD_ID = 'id';
    private const FIELD_GOALS_ID = 'goals_id';
    private const FIELD_POSITIONS_ID = 'positions_id';

    /** @var int */
    public $id;

    /** @var int */
    public $goalId;

    /** @var int */
    public $positionId;

    /** @var Database */
    private $db;

    public function __construct($database, $idOfGoal)
    {
        $this->db = $database;
        $this->goalId = $idOfGoal;
    }

    public function save()
    {
        $this->id = $this->db->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_GOALS_ID . ', ' . self::FIELD_POSITIONS_ID . ') VALUES (?, ?)', [['ii'], [$this->goalId, $this->positionId]]);
    }

    public function removeWithId()
    {
        $this->db->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GOALS_ID . ' = ?', [['i'], [$this->goalId]]);
    }

    public function getWithGoalId()
    {
        return $this->db->select('SELECT ' . self::FIELD_POSITIONS_ID . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GOALS_ID . ' = ?', [['i'], [$this->goalId]]);
    }

    /** @return $this[] */
    public function getGoalWaypoints(): array
    {
        $records = $this->db->select('SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GOALS_ID . ' = ?', [['i'], [$this->goalId]]);
        $waypoints = [];
        foreach ($records as $record) {
            $waypoint = new WaypointModel($this->db, $this->goalId);
            $waypoint->mapFromDbRecord($record);
            $waypoints[] = $waypoint;
        }
        return $waypoints;
    }

    public function mapFromDbRecord($record)
    {
        $this->id = $record[self::FIELD_ID];
        $this->goalId = $record[self::FIELD_GOALS_ID];
        $this->positionId = $record[self::FIELD_POSITIONS_ID];
        return $this;
    }
}
