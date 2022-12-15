<?php

namespace model;

use misc;

class GroupModel
{
    private const TABLE_NAME = 'groups';
    private const FIELD_ID = 'id';
    private const FIELD_GROUP_CODE = 'groupcode';

    /** @var int */
    public $id;

    /** @var string */
    public $groupCode;

    /** @var Database */
    private $db;

    public function __construct($database, $groupCode)
    {
        $this->db = $database;
        $this->groupCode = $groupCode;
    }

    public function save(): void
    {
        $this->db->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_GROUP_CODE . ') VALUES (?)', [['s'], [$this->groupCode]]);
    }

    public function getRowCount()
    {
        return $this->db->select('SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    public function removeWithGroupCode(): void
    {
        $this->db->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    /** @return $this */
    public function load()
    {
        $records = $this->db->select(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?',
            [["i"], [$this->groupCode]]
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
        $this->groupCode = $record[self::FIELD_GROUP_CODE];
        return $this;
    }
}
