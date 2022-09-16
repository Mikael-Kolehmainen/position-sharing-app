<?php

class User
{
    private const TABLE_NAME = 'users';
    private const FIELD_LAT = 'lat';
    private const FIELD_LNG = 'lng';
    private const FIELD_UNIQUE_ID = 'uniqueID';
    private const FIELD_INITIALS = 'initials';
    private const FIELD_COLOR = 'color';
    private const FIELD_GROUPCODE = 'groups_groupcode';

    /** @var int */
    private $id;

    /** @var decimal */
    public $position;

    // TODO: Create a positions table in database so that we can use Position class properly
}