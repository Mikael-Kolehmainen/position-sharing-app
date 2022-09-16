<?php

class Goal
{
    private const TABLE_NAME = 'goals';
    private const FIELD_START_POSITION = 'startposition';
    private const FIELD_GOAL_POSITION = 'goalposition';
    private const FIELD_GROUP_CODE = 'groups_groupcode';

    /** @var int */
    private $id;

    /** @var Position */
    public $startPosition;

    /** @var Position */
    public $goalPosition;

    /** @var string */
    private $groupCode;

    public function __construct(Position $startPosition, Position $goalPosition)
    {
        $this->startPosition = $startPosition;
        $this->goalPosition = $goalPosition;
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
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_START_POSITION . ', ' . self::FIELD_GOAL_POSITION . ', ' . self::FIELD_GROUP_CODE . ') VALUES (?, ?, ?)');
        $stmt->bindParam(1, $this->startPosition);
        $stmt->bindParam(2, $this->goalPosition);
        $stmt->bindParam(3, $this->groupCode);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    private function update(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('UPDATE ' . self::TABLE_NAME . ' SET ' . self::FIELD_START_POSITION . ' =  ?, ' . self::FIELD_GOAL_POSITION . ' = ?, ' . self::FIELD_GROUP_CODE . ' = ? WHERE ID = ?');
        $stmt->bindParam(1, $this->startPosition);
        $stmt->bindParam(2, $this->goalPosition);
        $stmt->bindParam(3, $this->groupCode);
        $stmt->bindParam(4, $this->id);
        $stmt->execute();
    }


    public function getGroup()
    {
        
    }
}
