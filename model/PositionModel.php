<?php
require_once PROJECT_ROOT_PATH . "/model/Database.php";
 
class PositionModel extends Database
{
    private const TABLE_NAME = 'positions';
    private const FIELD_LATITUDE = 'lat';
    private const FIELD_LONGITUDE = 'lng';

    /** @var int */
    public $id;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    public function save()
    {
        return $this->insert('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_LATITUDE . ', ' . self::FIELD_LONGITUDE . ') VALUES (?, ?)', [["dd"], [$this->latitude, $this->longitude]]);
    }

    public function update(): void
    {
        $this->id = $this->insert('UPDATE ' . self::TABLE_NAME . ' SET ' . self::FIELD_LATITUDE . ' = ?, ' . self::FIELD_LONGITUDE . ' = ? WHERE id = ?', [["ddi"], [$this->latitude, $this->longitude, $this->id]]);
    }

    public function removeWithId(): void
    {
        $this->remove('DELETE FROM ' . self::TABLE_NAME . ' WHERE id = ?', [['i'], [$this->id]]);
    }

    public function getLatLng()
    {
        return $this->select('SELECT ' . self::FIELD_LATITUDE . ', ' . self::FIELD_LONGITUDE . ' FROM ' . self::TABLE_NAME . ' WHERE id = ?', [["i"], [$this->id]]);
    }
}