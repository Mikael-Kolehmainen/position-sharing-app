<?php
class MessageController extends BaseController
{
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
    public $userId;

    /** @var string */
    public $fallbackInitials;

    /** @var string */
    public $fallbackColor;

    /** @var string */
    public $groupCode;

    /** @var Y-m-d */
    public $dateOfMessage;

    /** @var H:i */
    public $timeOfMessage;

    /** @var string */
    private $relativeImagePath;

    public function saveToDatabase()
    {
        $messageModel = new MessageModel();
        $messageModel->message = filter_input(INPUT_POST, MESSAGE_MESSAGE, FILTER_SANITIZE_SPECIAL_CHARS);
        $messageModel->groupCode = SessionManager::getGroupCode();
        $messageModel->userId = SessionManager::getUserRowId();
        $messageModel->fallbackInitials = SessionManager::getUserInitials();
        $messageModel->fallbackColor = SessionManager::getUserColor();
        $messageModel->dateOfMessage = date("Y-m-d");
        $messageModel->timeOfMessage = date("H:i");
        $messageModel->imagePath = $this->imagePath;
        $this->id = $messageModel->save();
    }

    public function getMessagesFromDatabase()
    {
        $messageModel = new MessageModel();
        $messageModel->groupCode = SessionManager::getGroupCode();

        return $messageModel->get();
    }

    public function removeMessagesFromDatabase(): void
    {
        $messageModel = new MessageModel();
        $messageModel->groupCode = SessionManager::getGroupCode();
        $messageModel->removeWithGroupCode();
    }
}