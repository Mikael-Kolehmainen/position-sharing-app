<?php

class Message
{
    private const TABLE_NAME = 'messages';
    private const FIELD_MESSAGE = 'message';
    private const FIELD_INITIALS = 'initials';
    private const FIELD_COLOR = 'color';
    private const FIELD_GROUP_CODE = 'groups_groupcode';

    /** @var int */
    public $id;

    /** @var string */
    public $message;

    /** @var string */
    public $initials;

    /** @var string */
    public $color;

    /** @var string */
    public $groupCode;

    public function __construct($groupCode)
    {
        $this->groupCode = $groupCode;
    }

    public function get()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_MESSAGE . ', ' . self::FIELD_INITIALS . ', ' . self::FIELD_COLOR . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
        $stmt->bindParam(1, $this->groupCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function remove(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('DELTE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
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
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_MESSAGE . ', ' . self::FIELD_INITIALS . ', ' . self::FIELD_COLOR . ', ' . self::FIELD_GROUP_CODE . ') VALUES (?, ?, ?, ?)');
        $stmt->bindParam(1, $this->message);
        $stmt->bindParam(2, $this->initials);
        $stmt->bindParam(3, $this->color);
        $stmt->bindParam(4, $this->groupCode);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }
}