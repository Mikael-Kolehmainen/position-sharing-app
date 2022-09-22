<?php

class Goal
{
    private const TABLE_NAME = 'goals';
    private const FIELD_START_POSITIONS_ID = 'start_positions_id';
    private const FIELD_GOAL_POSITIONS_ID = 'goal_positions_id';
    private const FIELD_GOAL_ID = 'goalIndex';
    private const FIELD_GROUP_CODE = 'groups_groupcode';

    /** @var int */
    public $id;

    /** @var int */
    public $startPositionID;

    /** @var int */
    public $goalPositionID;

    /** @var int */
    public $goalID;

    /** @var string */
    public $groupCode;

    public function __construct()
    {
        
    }

    public function getStartGoalPositionsRowIDs()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_START_POSITIONS_ID . ', ' . self::FIELD_GOAL_POSITIONS_ID . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIDs()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT id FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIndexes()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT goalIndex FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function remove()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();
    }

    public function save(): void
    {
        if (empty($this->id)) {
            $this->insert();
        }
    }

    private function insert(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_START_POSITIONS_ID . ', ' . self::FIELD_GOAL_POSITIONS_ID . ', ' . self::FIELD_GOAL_ID . ', ' . self::FIELD_GROUP_CODE . ') VALUES (?, ?, ?, ?)');
        $stmt->bindParam(1, $this->startPositionID);
        $stmt->bindParam(2, $this->goalPositionID);
        $stmt->bindParam(3, $this->goalID);
        $stmt->bindParam(4, $this->groupCode);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }
}
