<?php

class Message
{
    private const TABLE_NAME = 'messages';
    private const FIELD_MESSAGE = 'message';
    private const FIELD_GROUP_CODE = 'groups_groupcode';
    private const FIELD_USERS_ID = 'users_id';

    /** @var int */
    public $id;

    /** @var string */
    public $message;

    /** @var int */
    public $userID;

    /** @var string */
    public $groupCode;

    public function __construct($groupCode)
    {
        $this->groupCode = $groupCode;
    }

    public function get()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_MESSAGE . ', ' . self::FIELD_USERS_ID . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_MESSAGE . ', ' . self::FIELD_USERS_ID . ', ' . self::FIELD_GROUP_CODE . ') VALUES (?, ?, ?)');
        $stmt->bindParam(1, $this->message);
        $stmt->bindParam(2, $this->userID);
        $stmt->bindParam(3, $this->groupCode);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }
}