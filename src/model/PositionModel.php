<?php

namespace model;

use Exception;

class PositionModel extends Database
{
    private const TABLE_NAME = 'positions';
    private const FIELD_ID = 'id';
    private const FIELD_LATITUDE = 'lat';
    private const FIELD_LONGITUDE = 'lng';

    /** @var int */
    public $id;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    /** @var Database */
    private $db;

    public function __construct(Database $database, int $id = null)
    {
        $this->db = $database;
        if (!is_null($id)) {
            $this->id = $id;
        }
    }

    public function set(): int
    {
        if (is_null($this->id)) {
            return $this->save();
        } else {
            return $this->update();
        }
    }

    public function save(): int
    {
        $this->id = $this->db->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_LATITUDE . ', ' . self::FIELD_LONGITUDE . ') VALUES (?, ?)', [["dd"], [$this->latitude, $this->longitude]]);
        return $this->id;
    }

    public function update(): int
    {
        $this->id = $this->db->insert('UPDATE ' . self::TABLE_NAME . ' SET ' . self::FIELD_LATITUDE . ' = ?, ' . self::FIELD_LONGITUDE . ' = ? WHERE id = ?', [["ddi"], [$this->latitude, $this->longitude, $this->id]]);
        return $this->id;
    }

    public function delete(): void
    {
        $this->db->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE id = ?', [['i'], [$this->id]]);
    }

    /** @return $this */
    public function load()
    {
        $records = $this->db->select('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = ?', [["i"], [$this->id]]);
        $record = array_pop($records);
        return $this->mapFromDbRecord($record);
    }

    /**
     * @param mixed[] $record Associative array of one db record
     * @return $this
     */
    private function mapFromDbRecord($record)
    {
        $this->id = $record[self::FIELD_ID];
        $this->latitude = $record[self::FIELD_LATITUDE];
        $this->longitude = $record[self::FIELD_LONGITUDE];
        return $this;
    }
}
