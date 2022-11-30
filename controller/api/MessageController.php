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
        $messageModel->message = $this->message;
        $messageModel->groupCode = $this->groupCode;
        $messageModel->userId = $this->userId;
        $messageModel->fallbackInitials = $this->fallbackInitials;
        $messageModel->fallbackColor = $this->fallbackColor;
        $messageModel->groupCode = $this->groupCode;
        $messageModel->dateOfMessage = $this->dateOfMessage;
        $messageModel->timeOfMessage = $this->timeOfMessage;
        $messageModel->imagePath = $this->imagePath;
        $this->id = $messageModel->save();
    }

    public function getMessagesFromDatabase()
    {
        $messageModel = new MessageModel();
        $messageModel->groupCode = $this->groupCode;

        return $messageModel->get();
    }

    public function removeMessagesFromDatabase(): void
    {
        $messageModel = new MessageModel();
        $messageModel->groupCode = $this->groupCode;
        $messageModel->removeWithGroupCode();
    }
}