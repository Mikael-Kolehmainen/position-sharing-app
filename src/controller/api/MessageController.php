<?php

namespace controller\api;

use manager\SessionManager;
use manager\ServerRequestManager;
use model\Database;
use model\GroupModel;
use model\MessageModel;

class MessageController extends BaseController
{
    /** @var int */
    public $id;

    /** @var string */
    public $imagePath;

    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function saveToDatabase()
    {
        $messageModel = new MessageModel($this->db, SessionManager::getGroupCode());
        $messageModel->message = ServerRequestManager::postMessage();
        $messageModel->userId = SessionManager::getUserRowId();
        $messageModel->initials = SessionManager::getUserInitials();
        $messageModel->color = SessionManager::getUserColor();
        $messageModel->dateOfMessage = date("Y-m-d");
        $messageModel->timeOfMessage = date("H:i");
        $messageModel->imagePath = $this->imagePath;
        $this->id = $messageModel->save();
    }

    /** @return MessageModel[] */
    public function getMyGroupMessages(): array
    {
        $group = new GroupModel($this->db, SessionManager::getGroupCode());

        return $group->getGroupMessages();
    }

    public function deleteMessagesFromDatabase(): void
    {
        $groupModel = new GroupModel($this->db, SessionManager::getGroupCode());
        $groupModel->deleteAllMessages();
    }
}