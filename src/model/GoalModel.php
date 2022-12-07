<?php

namespace model;
 
class GoalModel extends Database
{
    private const TABLE_NAME = 'goals';
    private const FIELD_START_POSITIONS_ID = 'start_positions_id';
    private const FIELD_GOAL_POSITIONS_ID = 'goal_positions_id';
    private const FIELD_GOAL_ID = 'goalordernumber';
    private const FIELD_USER_ID = 'users_id';
    private const FIELD_GROUP_CODE = 'groups_groupcode';
    private const FIELD_GOAL_SESSION = 'goalsession';
    private const FIELD_FALLBACK_INITIALS = 'fallbackinitials';

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

    /** @var string */
    public $fallbackInitials;

    public function save()
    {
        return $this->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_START_POSITIONS_ID . ', ' . self::FIELD_GOAL_POSITIONS_ID . ', ' . self::FIELD_GOAL_ID . ', ' . self::FIELD_USER_ID . ', ' . self::FIELD_GROUP_CODE . ', ' . self::FIELD_FALLBACK_INITIALS . ') VALUES (?, ?, ?, ?, ?, ?)', [['iiiiss'], [$this->startPositionId, $this->goalPositionId, $this->goalOrderNumber, $this->userId, $this->groupCode, $this->fallbackInitials]]);
    }

    public function update()
    {
        $this->id = $this->insert('UPDATE ' . self::TABLE_NAME . ' SET ' . self::FIELD_GOAL_SESSION . ' = ? WHERE id = ?', [['si'], [$this->goalSession, $this->id]]);
    }

    public function removeWithGroupCode()
    {
        $this->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    public function getWithGroupCode()
    {
        return $this->select('SELECT id, ' . self::FIELD_GOAL_ID . ', ' . self::FIELD_START_POSITIONS_ID . ', ' . self::FIELD_GOAL_POSITIONS_ID . ', ' . self::FIELD_GOAL_SESSION . ', ' . self::FIELD_FALLBACK_INITIALS . ' FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [["s"], [$this->groupCode]]);
    }
}