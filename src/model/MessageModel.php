<?php

namespace model;
 
class MessageModel extends Database
{
    private const TABLE_NAME = 'messages';
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
    public $fallbackInitials;

    /** @var string */
    public $fallbackColor;

    /** @var string */
    public $groupCode;

    /** @var date(Y-m-d) */
    public $dateOfMessage;

    /** @var date(H:i) */
    public $timeOfMessage;

    public function save()
    {
        return $this->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_MESSAGE . ', ' . self::FIELD_USERS_ID . ', ' . self::FIELD_FALLBACK_INITIALS . ', ' . self::FIELD_FALLBACK_COLOR .  ', ' . self::FIELD_GROUP_CODE . ', ' . self::FIELD_DATE . ' ,' . self::FIELD_TIME . ', ' . self::FIELD_IMAGE_PATH . ') VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [['sissssss'], [$this->message, $this->userId, $this->fallbackInitials, $this->fallbackColor, $this->groupCode, $this->dateOfMessage, $this->timeOfMessage, $this->imagePath]]);
    }

    public function get()
    {
        return $this->select('SELECT ' . self::FIELD_MESSAGE . ', ' . self::FIELD_IMAGE_PATH . ', ' . self::FIELD_USERS_ID . ', ' . self::FIELD_DATE . ', DATE_FORMAT(' . self::FIELD_TIME . ', "%H:%i") AS ' . self::FIELD_TIME . ', ' . self::FIELD_FALLBACK_INITIALS . ', ' . self::FIELD_FALLBACK_COLOR . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    public function removeWithGroupCode(): void
    {
        $this->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }
}