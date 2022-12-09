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

    public function saveToDatabase()
    {
        $messageModel = new model\MessageModel();
        $messageModel->message = manager\ServerRequestManager::postMessage();
        $messageModel->groupCode = manager\SessionManager::getGroupCode();
        $messageModel->userId = manager\SessionManager::getUserRowId();
        $messageModel->fallbackInitials = manager\SessionManager::getUserInitials();
        $messageModel->fallbackColor = manager\SessionManager::getUserColor();
        $messageModel->dateOfMessage = date("Y-m-d");
        $messageModel->timeOfMessage = date("H:i");
        $messageModel->imagePath = $this->imagePath;
        $this->id = $messageModel->save();
    }

    public function getMessagesFromDatabase()
    {
        $messageModel = new model\MessageModel();
        $messageModel->groupCode = manager\SessionManager::getGroupCode();

        return $messageModel->get();
    }

    public function removeMessagesFromDatabase(): void
    {
        $messageModel = new model\MessageModel();
        $messageModel->groupCode = manager\SessionManager::getGroupCode();
        $messageModel->removeWithGroupCode();
    }
}