<?php

class Goal
{
    private const TABLE_NAME = 'goals';
    private const FIELD_START_POSITIONS_ID = 'start_positions_id';
    private const FIELD_GOAL_POSITIONS_ID = 'goal_positions_id';
    private const FIELD_GOAL_ID = 'goalIndex';
    private const FIELD_USER_ID = 'users_id';
    private const FIELD_GROUP_CODE = 'groups_groupcode';
    private const FIELD_GOAL_SESSION = 'goalsession';

    /** @var int */
    public $id;

    /** @var int */
    public $startPositionID;

    /** @var int */
    public $goalPositionID;

    /** @var int */
    public $goalIndex;

    /** @var string */
    public $groupCode;

    /** @var string */
    public $goalCookie;

    /** @var string */
    public $userID;

    public function __construct($groupCode)
    {
        $this->groupCode = $groupCode;
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
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_GOAL_ID . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createGoalSession()
    {
        require __DIR__.'/../random-string.php';

        $goalSession = getRandomString(15);
        
        session_start();
        if ($goalSession == $_SESSION[GOALSESSION]) {
            return $this->createGoalSession();
        } else {
            return $goalSession;
        }
    }

    public function getGoalSession()
    {
        $pdo = dbHandler::getPDbConnection();
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_GOAL_SESSION . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function remove(): void
    {
        session_start();
        unset($_SESSION[GOALSESSION]);

        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();
    }

    public function save(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_START_POSITIONS_ID . ', ' . self::FIELD_GOAL_POSITIONS_ID . ', ' . self::FIELD_GOAL_ID . ', ' . self::FIELD_USER_ID . ', ' . self::FIELD_GROUP_CODE . ', ' . self::FIELD_GOAL_SESSION . ') VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bindParam(1, $this->startPositionID);
        $stmt->bindParam(2, $this->goalPositionID);
        $stmt->bindParam(3, $this->goalIndex);
        $stmt->bindParam(4, $this->userID);
        $stmt->bindParam(5, $this->groupCode);
        $stmt->bindParam(6, $this->goalSession);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }
}
