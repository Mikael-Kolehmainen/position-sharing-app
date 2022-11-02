<?php
    require './../../required-files/constants.php';
    require './../../autoloader.php';

    if (isset($_GET[GROUPCODE])) {
        session_start();

        $uniqueID = $_SESSION[UNIQUEID];

        unset($_SESSION[UNIQUEID]);

        removePosition(getPositionsRowID($uniqueID));
        removeUser($uniqueID);
        removeCookie();
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

    function removeCookie()
    {
        unset($_COOKIE["goalCookie"]);
        setcookie("goalCookie", null, -1, "/");
        setcookie("goalCookieRemoved", 1, time() + (86400 * 30), "/");
    }