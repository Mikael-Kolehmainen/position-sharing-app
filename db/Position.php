<?php

class Position
{

    /** @var number */
    private $longitude;

    /** @var number */
    private $latitude;

    public function __construct($longitude, $latitude)
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }

    public function __toString()
    {
        return 'string eqvivalent...';
    }
}