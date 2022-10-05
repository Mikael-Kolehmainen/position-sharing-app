<?php
    require './../required-files/constants.php';
    require './../autoloader.php';

    if (isset($_GET[GROUPCODE])) {
        session_start();

        $uniqueID = $_SESSION[UNIQUEID];

        unset($_SESSION[UNIQUEID]);

        removePosition(getPositionsRowID($uniqueID));
        removeUser($uniqueID);
    }

    function removeUser($uniqueID)
    {
        $user = new User();
        $user->uniqueId = $uniqueID;
        $user->remove();
    }

    function removePosition($id)
    {
        $position = new Position();
        $position->id = $id;
        $position->remove();
    }

    function getPositionsRowID($uniqueID)
    {
        $user = new User();
        $user->uniqueId = $uniqueID;
        $positionRowId = $user->getPositionsRowID();

        return $positionRowId;
    }