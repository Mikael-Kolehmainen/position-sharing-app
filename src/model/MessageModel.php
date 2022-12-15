<?php

namespace model;

class MessageModel
{
    private const TABLE_NAME = 'messages';
    private const FIELD_ID = 'id';
    private const FIELD_MESSAGE = 'message';
    private const FIELD_IMAGE_PATH = 'imagepath';
    private const FIELD_DATE = 'dateofmessage';
    private const FIELD_TIME = 'timeofmessage';
    private const FIELD_GROUP_CODE = 'groups_groupcode';
    private const FIELD_FALLBACK_INITIALS = 'fallbackinitials';
    private const FIELD_FALLBACK_COLOR = 'fallbackcolor';
    private const FIELD_USERS_ID = 'users_id';

    /** @var int */
    public $id;

    /** @var string */
    public $message;

    /** @var string */
    public $imagePath;

    /** @var int */
    public $userId;

    /** @var string */
    public $initials;

    /** @var string */
    public $color;

    /** @var string */
    public $groupCode;

    /** @var date(Y-m-d) */
    public $dateOfMessage;

    /** @var date(H:i) */
    public $timeOfMessage;

    /** @var bool */
    public $sentByUser;

    /** @var Database */
    private $db;

    public function __construct($database, $groupCode = null)
    {
        $this->db = $database;
        $this->groupCode = $groupCode;
    }

    public function save()
    {
        return $this->db->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_MESSAGE . ', ' . self::FIELD_USERS_ID . ', ' . self::FIELD_FALLBACK_INITIALS . ', ' . self::FIELD_FALLBACK_COLOR . ', ' . self::FIELD_GROUP_CODE . ', ' . self::FIELD_DATE . ' ,' . self::FIELD_TIME . ', ' . self::FIELD_IMAGE_PATH . ') VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [['sissssss'], [$this->message, $this->userId, $this->initials, $this->color, $this->groupCode, $this->dateOfMessage, $this->timeOfMessage, $this->imagePath]]);
    }

    public function removeWithGroupCode(): void
    {
        $this->db->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    public function mapFromDbRecord($record)
    {
        $this->id = $record[self::FIELD_ID];
        $this->message = $record[self::FIELD_MESSAGE];
        $this->imagePath = $record[self::FIELD_IMAGE_PATH];
        $this->userId = $record[self::FIELD_USERS_ID];
        $this->dateOfMessage = $record[self::FIELD_DATE];
        $this->timeOfMessage = $record[self::FIELD_TIME];
        $this->color = $record[self::FIELD_FALLBACK_COLOR];
        $this->initials = $record[self::FIELD_FALLBACK_INITIALS];
        $this->sentByUser = null;
        $this->groupCode = $record[self::FIELD_GROUP_CODE];
    }
}
