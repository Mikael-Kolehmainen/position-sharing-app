<?php

namespace model;

class GoalModel
{
    private const TABLE_NAME = 'goals';
    private const FIELD_ID = 'id';
    private const FIELD_START_POSITIONS_ID = 'start_positions_id';
    private const FIELD_GOAL_POSITIONS_ID = 'goal_positions_id';
    private const FIELD_GOAL_ORDER_NUMBER = 'goalordernumber';
    private const FIELD_USER_ID = 'users_id';
    private const FIELD_GROUP_CODE = 'groups_groupcode';
    private const FIELD_GOAL_SESSION = 'goalsession';
    private const FIELD_FALLBACK_INITIALS = 'fallbackinitials';

    /** @var int */
    public $id;

    /** @var int */
    public $startPositionId;

    /** @var float */
    public $startPosition;

    /** @var int */
    public $goalPositionId;

    /** @var float */
    public $goalPosition;

    /** @var PositionModel[] */
    public $waypointPositions;

    /** @var int */
    public $goalOrderNumber;

    /** @var string */
    public $groupCode;

    /** @var string */
    public $goalSession;

    /** @var string */
    public $userId;

    /** @var string */
    public $fallbackInitials;

    /** @var Database */
    private $db;

    public function __construct($database, string $groupCode = null)
    {
        $this->db = $database;

        if (!is_null($groupCode)) {
            $this->groupCode = $groupCode;
        }
    }

    public function save()
    {
        return $this->db->insert(
            'INSERT INTO ' . self::TABLE_NAME . 
                ' (' . 
                self::FIELD_START_POSITIONS_ID . ', ' . 
                self::FIELD_GOAL_POSITIONS_ID . ', ' . 
                self::FIELD_GOAL_ORDER_NUMBER . ', ' . 
                self::FIELD_USER_ID . ', ' . 
                self::FIELD_GROUP_CODE . ', ' . 
                self::FIELD_FALLBACK_INITIALS . 
                ') VALUES (?, ?, ?, ?, ?, ?)', 
            [
                ['iiiiss'], 
                [$this->startPositionId, $this->goalPositionId, $this->goalOrderNumber, 
                $this->userId, $this->groupCode, $this->fallbackInitials]]);
    }

    public function update()
    {
        $this->id = $this->db->insert(
            'UPDATE ' . self::TABLE_NAME . 
            ' SET ' . self::FIELD_GOAL_SESSION . ' = ? 
            WHERE id = ?', 
            [
                ['si'], 
                [$this->goalSession, $this->id]
            ]);
    }

    public function removeWithGroupCode()
    {
        $this->db->remove(
            'DELETE FROM ' . self::TABLE_NAME . 
            ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', 
            [
                ['s'], [$this->groupCode]
            ]);
    }

    /** @return $this */
    public function load()
    {
        $records = $this->db->select(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?',
            [["s"], [$this->groupCode]]
        );
        $record = array_pop($records);
        return $this->mapFromDbRecord($record);
    }

    /** @return $this */
    public function getMyWaypoints()
    {
        $waypointModel = new WaypointModel($this->db, $this->id);

        foreach ($waypointModel->getGoalWaypoints() as $waypoint) {
            $this->waypointPositions[] = new PositionModel($this->db, $waypoint->positionId);
            $this->waypointPositions[count($this->waypointPositions)-1]->load();
        }

        return $this;
    }

    /** @return $this */
    public function loadPositions()
    {
        $this->startPosition = new PositionModel($this->db, $this->startPositionId);
        $this->startPosition->load();

        $this->goalPosition = new PositionModel($this->db, $this->goalPositionId);
        $this->goalPosition->load();

        return $this;
    }

    /**
     * @param mixed[] $record Associative array of one db record
     * @return $this
     */
    public function mapFromDbRecord($record)
    {
        $this->id = $record[self::FIELD_ID];
        $this->startPositionId = $record[self::FIELD_START_POSITIONS_ID];
        $this->goalPositionId = $record[self::FIELD_GOAL_POSITIONS_ID];
        $this->goalOrderNumber = $record[self::FIELD_GOAL_ORDER_NUMBER];
        $this->userId = $record[self::FIELD_USER_ID];
        $this->fallbackInitials = $record[self::FIELD_FALLBACK_INITIALS];
        $this->groupCode = $record[self::FIELD_GROUP_CODE];
        $this->goalSession = $record[self::FIELD_GOAL_SESSION];
        return $this;
    }
}
