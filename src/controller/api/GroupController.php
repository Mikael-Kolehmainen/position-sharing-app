<?php

namespace controller\api;

use model;
use manager;

class GroupController extends BaseController
{
    /**
     * "index.php/map/create"
     */

    /** @var string */
    public $groupCode;
    
    public function saveToDatabase(): void
    {
        $groupModel = new model\GroupModel();
        $groupModel->createGroupCode();
        $groupModel->save();
        manager\SessionManager::saveGroupCode($groupModel->groupCode);
    }

    public function findGroupInDatabase()
    {
        $groupModel = new model\GroupModel();
        $this->groupCode = filter_input(INPUT_POST, GROUP_GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        $groupModel->groupCode = $this->groupCode != null ? $this->groupCode : manager\SessionManager::getGroupCode();

        if ($groupModel->getRowCount()) {
            manager\SessionManager::saveGroupCode(($groupModel->groupCode));
            return true;
        }

        return false;
    }

    public function removeGroupFromDatabase()
    {
        $groupModel = new model\GroupModel();
        $groupModel->groupCode = manager\SessionManager::getGroupCode();

        $groupModel->removeWithGroupCode();
    }
}