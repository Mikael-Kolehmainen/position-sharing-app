<?php

class Position
{
    private const TABLE_NAME = 'positions';
    private const FIELD_LAT = 'lat';
    private const FIELD_LNG = 'lng';

    /** @var id */
    public $id;

    /** @var decimal */
    public $latitude;

    /** @var decimal */
    public $longitude;

    public function __construct($latitude = 0, $longitude = 0)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function save(): void
    {
        if (empty($this->id)) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    private function insert(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE_NAME . ' (' . self::FIELD_LAT . ', ' . self::FIELD_LNG . ') VALUES (?, ?)');
        $stmt->bindParam(1, $this->latitude);
        $stmt->bindParam(2, $this->longitude);
        $stmt->execute();
        $this->id = $pdo->lastInsertId();
    }

    private function update(): void
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('UPDATE ' . self::TABLE_NAME . ' SET ' . self::FIELD_LAT . ' =  ?, ' . self::FIELD_LNG . ' = ? WHERE id = ?');
        $stmt->bindParam(1, $this->latitude);
        $stmt->bindParam(2, $this->longitude);
        $stmt->bindParam(3, $this->id);
        $stmt->execute();
    }

    public function getPosition()
    {
        $pdo = dbHandler::getPdbConnection();
        $stmt = $pdo->prepare('SELECT ' . self::FIELD_LAT . ', ' . self::FIELD_LNG . ' FROM ' . self::TABLE_NAME . ' WHERE id = ?');
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return [$row['lat'], $row['lng']];
        } 
    }
}