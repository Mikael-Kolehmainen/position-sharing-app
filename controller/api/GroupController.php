<?php
class GroupController extends BaseController
{
    /**
     * "index.php/map/create"
     */

    /** @var string */
    public $groupCode;
    
    public function saveToDatabase(): void
    {
        $groupModel = new GroupModel();
        $groupModel->createGroupCode();
        $groupModel->save();
        SessionManager::saveGroupCode($groupModel->groupCode);
    }

    public function findGroupInDatabase()
    {
        $groupModel = new GroupModel();
        $this->groupCode = filter_input(INPUT_POST, GROUP_GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        $groupModel->groupCode = $this->groupCode != null ? $this->groupCode : SessionManager::getGroupCode();

        if ($groupModel->getRowCount()) {
            SessionManager::saveGroupCode(($groupModel->groupCode));
            return true;
        }

        return false;
    }

    public function removeGroupFromDatabase()
    {
        $groupModel = new GroupModel();
        $groupModel->groupCode = SessionManager::getGroupCode();

        $groupModel->removeWithGroupCode();
    }
}