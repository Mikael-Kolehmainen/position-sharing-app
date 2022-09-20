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

    public function getPositionRowID()
    {
        $pdo = dbHandler::getPdbConnection();

        $stmt = $pdo->prepare('SELECT ' . self::FIELD_POSITIONS_ID . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_UNIQUE_ID . ' = ?');
        $stmt->bindParam(1, $this->uniqueId);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row[self::FIELD_POSITIONS_ID];
        }
    }

    public function getAllPositionRowIDs()
    {
        $pdo = dbHandler::getPdbConnection();

        $stmt = $pdo->prepare('SELECT ' . self::FIELD_POSITIONS_ID . ' FROM ' . self::TABLE_NAME);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInitials()
    {
        $pdo = dbHandler::getPdbConnection();

        $stmt = $pdo->prepare('SELECT ' . self::FIELD_INITIALS . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUPCODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getColor()
    {
        $pdo = dbHandler::getPdbConnection();

        $stmt = $pdo->prepare('SELECT ' . self::FIELD_COLOR . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUPCODE . ' = ?');
        $stmt->bindParam(1, $this->$groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function returnArrayFromDatabase($result, $rowKeys)
    {
        $array = array();

        for ($i = 0; $i < $result->num_rows; $i++) {
            array_push($array, $result[$i]);
        }

        return $result;
    }
}