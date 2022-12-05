<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";
 
class UserModel extends Database
{
    private const TABLE_NAME = 'users';
    private const FIELD_POSITIONS_ID = 'positions_id';
    private const FIELD_INITIALS = 'initials';
    private const FIELD_COLOR = 'color';
    private const FIELD_GROUPCODE = 'groups_groupcode';

    /** @var int */
    public $id;

    /** @var int */
    public $positionsId;

    /** @var string */
    public $initials;

    /** @var string */
    public $color;

    /** @var string */
    public $groupCode;

    public function save(): void
    {
        if (empty($this->id)) {
            $this->saveWithAutoID();
        } else {
            $this->saveWithID();
        }
    }

    public function saveWithAutoID(): void
    {
        $this->id = $this->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_POSITIONS_ID . ', ' . self::FIELD_INITIALS . ', ' . self::FIELD_COLOR . ', ' . self::FIELD_GROUPCODE . ') VALUES (?, ?, ?, ?)', [["isss"], [$this->positionsId, $this->initials, $this->color, $this->groupCode]]);
    }

    public function saveWithID(): void
    {
        $this->id = $this->insert('INSERT INTO ' . self::TABLE_NAME . ' (id, ' . self::FIELD_POSITIONS_ID . ', ' . self::FIELD_INITIALS . ', ' . self::FIELD_COLOR . ', ' . self::FIELD_GROUPCODE . ') VALUES (?, ?, ?, ?, ?)', [["iisss"], [$this->id, $this->positionsId, $this->initials, $this->color, $this->groupCode]]);
    }

    public function removeWithID(): void
    {
        $this->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE id = ?', [['i'], [$this->id]]);
    }

    public function removeWithGroupCode(): void
    {
        $this->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUPCODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    public function getWithGroupCode()
    {
        return $this->select('SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUPCODE . ' = ? ', [['s'], [$this->groupCode]]);
    }

    public function getWithId()
    {
        return $this->select('SELECT ' . self::FIELD_INITIALS . ', ' . self::FIELD_COLOR . ', ' . self::FIELD_POSITIONS_ID . ' FROM ' . self::TABLE_NAME . ' WHERE id = ?', [["i"], [$this->id]]);
    }
}