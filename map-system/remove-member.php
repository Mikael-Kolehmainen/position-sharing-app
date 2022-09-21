<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';
    require './../db/User.php';
    require './../db/Position.php';

    if (isset($_GET[GROUPCODE])) {
        session_start();

        $uniqueID = $_SESSION[UNIQUEID];

        unset($_SESSION[UNIQUEID]);

        removePosition(getPositionRowID($uniqueID));
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

    function getPositionRowID($uniqueID)
    {
        $user = new User();
        $user->uniqueId = $uniqueID;
        $positionRowId = $user->getPositionRowID();

        return $positionRowId;
    }