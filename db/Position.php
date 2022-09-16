<?php

class Position
{
    private const TABLE_NAME = 'positions';
    private const FIELD_LAT = 'lat';
    private const FIELD_LNG = 'lng';

    /** @var id */
    private $id;

    /** @var decimal */
    private $latitude;

    /** @var decimal */
    private $longitude;

    // Varför är det understryckningar före funktion namnet?
    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function __toString()
    {
        return 'string eqvivalent...';
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
        $stmt = $pdo->prepare('UPDATE ' . self::TABLE_NAME . ' SET ' . self::FIELD_LAT . ' =  ?, ' . self::FIELD_LNG . ' = ? WHERE ID = ?');
        $stmt->bindParam(1, $this->startPosition);
        $stmt->bindParam(2, $this->goalPosition);
        $stmt->bindParam(3, $this->id);
        $stmt->execute();
    }
}