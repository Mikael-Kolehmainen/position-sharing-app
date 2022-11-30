<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";
 
class WaypointModel extends Database
{
    private const TABLE_NAME = 'waypoints';
    private const FIELD_GOALS_ID = 'goals_id';
    private const FIELD_POSITIONS_ID = 'positions_id';

    /** @var int */
    public $id;

    /** @var int */
    public $goalId;

    /** @var int */
    public $positionId;

    public function save()
    {
        $this->id = $this->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_GOALS_ID . ', ' . self::FIELD_POSITIONS_ID . ') VALUES (?, ?)', [['ii'], [$this->goalId, $this->positionId]]);
    }

    public function removeWithId()
    {
        $this->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GOALS_ID . ' = ?', [['i'], [$this->goalId]]);
    }

    public function getWithGoalId()
    {
        return $this->select('SELECT ' . self::FIELD_POSITIONS_ID . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GOALS_ID . ' = ?', [['i'], [$this->goalId]]);
    }
}