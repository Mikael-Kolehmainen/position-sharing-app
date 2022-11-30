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
        $this->groupCode = $groupModel->groupCode;
    }

    public function findGroupInDatabase()
    {
        $groupModel = new GroupModel();
        $groupModel->groupCode = $this->groupCode;

        return $groupModel->getRowCount();
    }

    public function removeGroupFromDatabase()
    {
        $groupModel = new GroupModel();
        $groupModel->groupCode = $this->groupCode;

        $groupModel->removeWithGroupCode();
    }
}