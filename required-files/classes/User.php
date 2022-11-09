<?php

class User
{
    private const TABLE_NAME = 'users';
    private const FIELD_POSITIONS_ID = 'positions_id';
    private const FIELD_UNIQUE_ID = 'uniqueID';
    private const FIELD_INITIALS = 'initials';
    private const FIELD_COLOR = 'color';
    private const FIELD_GROUPCODE = 'groups_groupcode';

    /** @var int */
    private $id;

    /** @var int */
    public $positionsId;

    /** @var varchar */
    public $uniqueId;

    /** @var string */
    public $initials;

    /** @var string */
    public $color;

    /** @var string */
    public $groupCode;

    public function __construct()
    {

    }

    public function getPositionsRowID()
    {
        $pdo = dbHandler::getPdbConnection();

        $stmt = $pdo->prepare('SELECT ' . self::FIELD_POSITIONS_ID . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_UNIQUE_ID . ' = ?');
        $stmt->bindParam(1, $this->uniqueId);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row[self::FIELD_POSITIONS_ID];
        }
    }

    public function getIDs()
    {
        $pdo = dbHandler::getPdbConnection();

        $stmt = $pdo->prepare('SELECT id FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUPCODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMarkerDetails()
    {
        $pdo = dbHandler::getPdbConnection();

        $stmt = $pdo->prepare('SELECT ' . self::FIELD_INITIALS . ', ' . self::FIELD_COLOR . ', ' . self::FIELD_POSITIONS_ID . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUPCODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveMarkerToSession()
    {
        $initials = filter_input(INPUT_POST, self::FIELD_INITIALS, FILTER_DEFAULT);;
        $color = filter_input(INPUT_POST, self::FIELD_COLOR, FILTER_DEFAULT);;

        if ($color == "") {
            $color = "#FF0000";
        }
        
        $initials = strtoupper($initials);

        session_start();

        $_SESSION[INITIALS] = $initials;
        $_SESSION[COLOR] = $color;
    }

    public function getUniqueIDs()
    {
        $pdo = dbHandler::getPdbConnection();

        $stmt = $pdo->prepare('SELECT ' . self::FIELD_UNIQUE_ID . ' FROM ' . self::TABLE_NAME);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function remove(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('DELETE FROM ' . self::TABLE_NAME . ' WHERE uniqueID = ?');
        $stmt->bindParam(1, $this->uniqueId);
        $stmt->execute();
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
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_POSITIONS_ID . ', ' . self::FIELD_UNIQUE_ID . ', ' . self::FIELD_INITIALS . ', ' . self::FIELD_COLOR . ', ' . self::FIELD_GROUPCODE . ') VALUES (?, ?, ?, ?, ?)');
        $stmt->bindParam(1, $this->positionsId);
        $stmt->bindParam(2, $this->uniqueId);
        $stmt->bindParam(3, $this->initials);
        $stmt->bindParam(4, $this->color);
        $stmt->bindParam(5, $this->groupCode);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    private function update(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('UPDATE ' . self::TABLE_NAME . ' SET ' . self::FIELD_POSITIONS_ID . ' =  ?, ' . self::FIELD_UNIQUE_ID . ' = ?, ' . self::FIELD_INITIALS . ' = ?, ' . self::FIELD_COLOR . ' = ?, ' . self::FIELD_GROUPCODE . ' = ?' . 'WHERE id = ?');
        $stmt->bindParam(1, $this->positionsId);
        $stmt->bindParam(2, $this->uniqueId);
        $stmt->bindParam(3, $this->initials);
        $stmt->bindParam(4, $this->color);
        $stmt->bindParam(5, $this->groupCode);
        $stmt->bindParam(6, $this->id);
        $stmt->execute();
    }
}