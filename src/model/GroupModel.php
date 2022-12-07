<?php

namespace model;

use misc;
 
class GroupModel extends Database
{
    private const TABLE_NAME = 'groups';
    private const FIELD_GROUP_CODE = 'groupcode';

    /** @var int */
    public $id;

    /** @var string */
    public $groupCode;

    public function save(): void
    {
        $this->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_GROUP_CODE . ') VALUES (?)', [['s'], [$this->groupCode]]);
    }

    public function getRowCount()
    {
        return $this->select('SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    public function removeWithGroupCode(): void
    {
        $this->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::FIELD_GROUP_CODE . ' = ?', [['s'], [$this->groupCode]]);
    }

    public function createGroupCode(): void
    {
        $this->groupCode = misc\RandomString::getRandomString(3);

        if (count($this->getRowCount())) {
            $this->createGroupCode();
        }
    }
}