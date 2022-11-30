<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";
 
class GoalModel extends Database
{
    private const TABLE_NAME = 'goals';
    private const FIELD_START_POSITIONS_ID = 'start_positions_id';
    private const FIELD_GOAL_POSITIONS_ID = 'goal_positions_id';
    private const FIELD_GOAL_ID = 'goalIndex';
    private const FIELD_USER_ID = 'users_id';
    private const FIELD_GROUP_CODE = 'groups_groupcode';
    private const FIELD_GOAL_SESSION = 'goalsession';

    /** @var int */
    public $id;

    /** @var int */
    public $startPositionId;

    /** @var int */
    public $goalPositionId;

    /** @var int */
    public $goalOrderNumber;

    /** @var string */
    public $groupCode;

    /** @var string */
    public $goalSession;

    /** @var string */
    public $userId;

    public function save()
    {
        return $this->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_START_POSITIONS_ID . ', ' . self::FIELD_GOAL_POSITIONS_ID . ', ' . self::FIELD_GOAL_ID . ', ' . self::FIELD_USER_ID . ', ' . self::FIELD_GROUP_CODE . ', ' . self::FIELD_GOAL_SESSION . ') VALUES (?, ?, ?, ?, ?, ?)', [['iiiiss'], [$this->startPositionId, $this->goalPositionId, $this->goalOrderNumber, $this->userId, $this->groupCode, $this->goalSession]]);
    }

    public function removeWithGroupCode()
    {
        $this->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    public function getWithGroupCode()
    {
        return $this->select('SELECT id, ' . self::FIELD_GOAL_ID . ', ' . self::FIELD_START_POSITIONS_ID . ', ' . self::FIELD_GOAL_POSITIONS_ID . ', ' . self::FIELD_GOAL_SESSION . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [["s"], [$this->groupCode]]);
    }
}