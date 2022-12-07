<?php
class MessageController extends BaseController
{
    private const MESSAGE = "message";

    /** @var int */
    public $id;

    /** @var string */
    public $imagePath;

    public function saveToDatabase()
    {
        $messageModel = new MessageModel();
        $messageModel->message = filter_input(INPUT_POST, self::MESSAGE, FILTER_SANITIZE_SPECIAL_CHARS);
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