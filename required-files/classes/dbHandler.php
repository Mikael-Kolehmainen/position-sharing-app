<?php

class dbHandler
{
    public static function getPdbConnection(): PDO
    {
        return new PDO('mysql:host=localhost; dbname=positionappDB', 'root');
    }
}