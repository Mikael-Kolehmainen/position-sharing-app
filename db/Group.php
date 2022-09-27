<?php

class Group
{
    private const TABLE_NAME = 'groups';
    private const FIELD_GROUP_CODE = 'groupcode';

    /** @var int */
    public $id;

    /** @var string */
    public $groupCode;

    public function __construct($groupCode = "empty")
    {
        if ($groupCode == "empty") {
            $this->createGroupCode();
        } else {
            $this->groupCode = $groupCode;
        }
    }

    public function remove(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();
    }

    public function save(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_GROUP_CODE . ') VALUES (?)');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    public function getRowCount()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function redirectUserToGroupMap()
    {
        header("LOCATION: ./../map-system/active.php?".self::FIELD_GROUP_CODE."=".$this->groupCode);
    }

    private function createGroupCode(): void
    {
        require './../required-files/random-string.php';

        $this->groupCode = getRandomString(3);

        if ($this->getRowCount()) {
            $this->groupCode = createGroupCode();
        }
    }
}