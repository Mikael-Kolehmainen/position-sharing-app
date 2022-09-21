<?php

class Goal
{
    private const TABLE_NAME = 'goals';
    private const FIELD_START_POSITIONS_ID = 'start_positions_id';
    private const FIELD_GOAL_POSITIONS_ID = 'goal_positions_id';
    private const FIELD_GROUP_CODE = 'groups_groupcode';

    /** @var int */
    private $id;

    /** @var int */
    public $startPositionsID;

    /** @var int */
    public $goalPositionsID;

    /** @var string */
    public $groupCode;

    public function __construct()
    {
        
    }

    public function addToGroup(string $groupCode): void
    {
        $this->groupCode = $groupCode;
    }

    public function save(): void
    {
        if (empty($this->id)) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    private function insert(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_START_POSITIONS_ID . ', ' . self::FIELD_GOAL_POSITIONS_ID . ', ' . self::FIELD_GROUP_CODE . ') VALUES (?, ?, ?)');
        $stmt->bindParam(1, $this->startPosition);
        $stmt->bindParam(2, $this->goalPosition);
        $stmt->bindParam(3, $this->groupCode);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    private function update(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('UPDATE ' . self::TABLE_NAME . ' SET ' . self::FIELD_START_POSITIONS_ID . ' =  ?, ' . self::FIELD_GOAL_POSITIONS_ID . ' = ?, ' . self::FIELD_GROUP_CODE . ' = ? WHERE ID = ?');
        $stmt->bindParam(1, $this->startPosition);
        $stmt->bindParam(2, $this->goalPosition);
        $stmt->bindParam(3, $this->groupCode);
        $stmt->bindParam(4, $this->id);
        $stmt->execute();
    }

    public function getStartGoalPositionsRowIDs()
    {
        $pdo = dbHandler::getPdbConnection();

        $stmt = $pdo->prepare('SELECT ' . self::FIELD_START_POSITIONS_ID . ', ' . self::FIELD_GOAL_POSITIONS_ID . ' FROM ' . self::TABLE_NAME);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
