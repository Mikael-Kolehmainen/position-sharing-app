<?php
    require './../../required-files/constants.php';
    require './../../autoloader.php';

    if (isset($_GET[GROUPCODE])) {
        session_start();

        $id = $_SESSION[USER_ROW_ID];

        removePosition(getPositionsRowID($id));
        removeUser($id);
        removeCookie();
    }

    function removeUser($id)
    {
        $user = new User();
        $user->id = $id;
        $user->remove();
    }

    function removePosition($id)
    {
        $position = new Position();
        $position->id = $id;
        $position->remove();
    }

    function getPositionsRowID($id)
    {
        $user = new User();
        $user->id = $id;
        $positionRowId = $user->getPositionsRowID();

        return $positionRowId;
    }

    function removeCookie()
    {
        unset($_COOKIE["goalCookie"]);
        setcookie("goalCookie", null, -1, "/");
        setcookie("goalCookieRemoved", 1, time() + (86400 * 30), "/");
    }