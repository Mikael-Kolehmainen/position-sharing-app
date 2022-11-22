<?php

class Message
{
    private const TABLE_NAME = 'messages';
    private const FIELD_MESSAGE = 'message';
    private const FIELD_IMAGE_PATH = 'imagepath';
    private const FIELD_DATE = 'dateofmessage';
    private const FIELD_TIME = 'timeofmessage';
    private const FIELD_GROUP_CODE = 'groups_groupcode';
    private const FIELD_FALLBACK_INITIALS = 'fallbackInitials';
    private const FIELD_FALLBACK_COLOR = 'fallbackColor';
    private const FIELD_USERS_ID = 'users_id';

    /** @var int */
    public $id;

    /** @var string */
    public $message;

    /** @var string */
    public $imagePath;

    /** @var string */
    public $webImagePath;

    /** @var string */
    public $webImageType;

    /** @var int */
    public $userID;

    /** @var string */
    public $fallbackInitials;

    /** @var string */
    public $fallbackColor;

    /** @var string */
    public $groupCode;

    /** @var string */
    private $relativeImagePath;

    public function __construct($groupCode)
    {
        $this->groupCode = $groupCode;
    }

    public function get()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_MESSAGE . ', ' . self::FIELD_IMAGE_PATH . ', ' . self::FIELD_USERS_ID . ', ' . self::FIELD_DATE . ', DATE_FORMAT(' . self::FIELD_TIME . ', "%H:%i") AS ' . self::FIELD_TIME . ', ' . self::FIELD_FALLBACK_INITIALS . ', ' . self::FIELD_FALLBACK_COLOR . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?');
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
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_MESSAGE . ', ' . self::FIELD_USERS_ID . ', ' . self::FIELD_FALLBACK_INITIALS . ', ' . self::FIELD_FALLBACK_COLOR .  ', ' . self::FIELD_GROUP_CODE . ', ' . self::FIELD_DATE . ' ,' . self::FIELD_TIME . ') VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bindParam(1, $this->message);
        $stmt->bindParam(2, $this->userID);
        $stmt->bindParam(3, $this->fallbackInitials);
        $stmt->bindParam(4, $this->fallbackColor);
        $stmt->bindParam(5, $this->groupCode);
        $stmt->bindParam(6, date("Y-m-d"));
        $stmt->bindParam(7, date("H:i"));
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    public function createImagePath(): void
    {
        $this->imagePath = "media/chat_images/".$this->groupCode;
        $this->relativeImagePath = "./../".$this->imagePath;
        $this->createDirIfDoesNotExist();
        $fileExt = $this->webImageType;
        $this->imagePath = $this->imagePath."/".getRandomString(10).".".$fileExt;
    }

    private function createDirIfDoesNotExist(): void
    {
        if (!file_exists($this->relativeImagePath) || !is_dir($this->relativeImagePath)) {
            mkdir($this->relativeImagePath, 0755, true);
        }
    }

    public function saveImageToServer()
    {
        return move_uploaded_file($this->webImagePath["tmp_name"], "./../".$this->imagePath);
    }

    public function saveImagePath(): void
    {
        $currentDate = date("Y-m-d");
        $currentTime = date("H:i");

        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_IMAGE_PATH . ', ' . self::FIELD_USERS_ID . ', ' . self::FIELD_GROUP_CODE . ', ' . self::FIELD_DATE . ' ,' . self::FIELD_TIME . ') VALUES (?, ?, ?, ?, ?)');
        $stmt->bindParam(1, $this->imagePath);
        $stmt->bindParam(2, $this->userID);
        $stmt->bindParam(3, $this->groupCode);
        $stmt->bindParam(4, $currentDate);
        $stmt->bindParam(5, $currentTime);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }
}