<?php

class Group
{
    private const TABLE_NAME = 'groups';
    private const FIELD_GROUP_CODE = 'groupcode';

    /** @var int */
    public $id;

    /** @var string */
    public $groupCode;

    public function __construct($groupCode)
    {
        $this->groupCode = $groupCode;
    }

    public function remove()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();
    }

    public function save()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_GROUP_CODE . ') VALUES (?)');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    public function get()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_GROUP_CODE . ' FROM ' . self::TABLE_NAME);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}