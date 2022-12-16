<?php

namespace model;

use __PHP_Incomplete_Class;
use Exception;

class UserModel
{
    private const TABLE_NAME = 'users';
    private const FIELD_ID = 'id';
    private const FIELD_POSITIONS_ID = 'positions_id';
    private const FIELD_INITIALS = 'initials';
    private const FIELD_COLOR = 'color';
    private const FIELD_GROUP_CODE = 'groups_groupcode';

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

    /** @var PositionModel */
    public $position;

    /** @var Database */
    private $db;
    
    public function __construct(Database $database, int $id = null, string $groupCode = null)
    {
        $this->db = $database;
        if (!is_null($id)) {
            $this->id = $id;
        }

        if (!is_null($groupCode)) {
            $this->groupCode = $groupCode;
        }
    }

    public function save(): void
    {
        if (empty($this->id)) {
            $this->saveWithAutoID();
        } else {
            $this->saveWithID();
        }
    }

    private function saveWithAutoID(): void
    {
        $this->id = $this->db->insert(
            'INSERT INTO ' . self::TABLE_NAME .
                ' (' .
                self::FIELD_POSITIONS_ID . ', ' .
                self::FIELD_INITIALS . ', ' .
                self::FIELD_COLOR . ', ' .
                self::FIELD_GROUP_CODE .
                ') VALUES (?, ?, ?, ?)',
            [
                ["isss"],
                [$this->positionsId, $this->initials, $this->color, $this->groupCode]
            ]
        );
    }

    private function saveWithID(): void
    {
        $this->id = $this->db->insert(
            'INSERT INTO ' . self::TABLE_NAME .
                ' (' .
                self::FIELD_ID . ', ' .
                self::FIELD_POSITIONS_ID . ', ' .
                self::FIELD_INITIALS . ', ' .
                self::FIELD_COLOR . ', ' .
                self::FIELD_GROUP_CODE .
                ') VALUES (?, ?, ?, ?, ?)',
            [
                ["iisss"],
                [$this->id, $this->positionsId, $this->initials, $this->color, $this->groupCode]
            ]
        );
    }

    public function delete(): void
    {
        $this->db->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE id = ?', [['i'], [$this->id]]);
    }

    public function removeWithGroupCode(): void
    {
        $this->db->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    public function setPosition(PositionModel $position): void
    {
        $position->id = $this->positionsId;
        $position->set();
    }

    /** @return UserModel */
    public function loadPosition(): UserModel
    {
        $this->position = new PositionModel($this->db, $this->positionsId);
        $this->position->load();
        return $this;
    }

    /** @return $this */
    public function load()
    {
        $records = $this->db->select(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = ?',
            [["i"], [$this->id]]
        );
        $record = array_pop($records);
        return $this->mapFromDbRecord($record);
    }

    /**
     * @param mixed[] $record Associative array of one db record
     * @return $this
     */
    public function mapFromDbRecord($record)
    {
        $this->id = $record[self::FIELD_ID];
        $this->color = $record[self::FIELD_COLOR];
        $this->groupCode = $record[self::FIELD_GROUP_CODE];
        $this->initials = $record[self::FIELD_INITIALS];
        $this->positionsId = $record[self::FIELD_POSITIONS_ID];
        return $this;
    }
}
