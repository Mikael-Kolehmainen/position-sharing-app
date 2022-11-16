<?php

class Message
{
    private const TABLE_NAME = 'messages';
    private const FIELD_MESSAGE = 'message';
    private const FIELD_DATE = 'dateofmessage';
    private const FIELD_TIME = 'timeofmessage';
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
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_MESSAGE . ', ' . self::FIELD_USERS_ID . ', ' . self::FIELD_DATE . ', DATE_FORMAT(' . self::FIELD_TIME . ', "%H:%i") AS ' . self::FIELD_TIME . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
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
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_MESSAGE . ', ' . self::FIELD_USERS_ID . ', ' . self::FIELD_GROUP_CODE . ', ' . self::FIELD_DATE . ' ,' . self::FIELD_TIME . ') VALUES (?, ?, ?, ?, ?)');
        $stmt->bindParam(1, $this->message);
        $stmt->bindParam(2, $this->userID);
        $stmt->bindParam(3, $this->groupCode);
        $stmt->bindParam(4, date("Y-m-d"));
        $stmt->bindParam(5, date("H:i"));
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }
}