<?php

namespace controller\api;

use model;
use manager;

class MessageController extends BaseController
{
    /** @var int */
    public $id;

    /** @var string */
    public $imagePath;

    /** @var model\Database */
    private $db;

    public function __construct()
    {
        $this->db = new model\Database();
    }

    public function saveToDatabase()
    {
        $messageModel = new model\MessageModel($this->db, manager\SessionManager::getGroupCode());
        $messageModel->message = manager\ServerRequestManager::postMessage();
        $messageModel->userId = manager\SessionManager::getUserRowId();
        $messageModel->initials = manager\SessionManager::getUserInitials();
        $messageModel->color = manager\SessionManager::getUserColor();
        $messageModel->dateOfMessage = date("Y-m-d");
        $messageModel->timeOfMessage = date("H:i");
        $messageModel->imagePath = $this->imagePath;
        $this->id = $messageModel->save();
    }

    /** @return model\MessageModel[] */
    public function getMyGroupMessages(): array
    {
        $group = new model\GroupModel($this->db, manager\SessionManager::getGroupCode());

        return $group->getGroupMessages();
    }

    public function removeMessagesFromDatabase(): void
    {
        $messageModel = new model\MessageModel($this->db, manager\SessionManager::getGroupCode());
        $messageModel->removeWithGroupCode();
    }
}