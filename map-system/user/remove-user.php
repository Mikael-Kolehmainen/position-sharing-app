<?php
    require './../../required-files/constants.php';
    require './../../autoloader.php';

    if (isset($_GET[GROUPCODE])) {
        session_start();

        $id = $_SESSION[USER_ROW_ID];

        removePosition(getPositionsRowID($id));
        removeUser($id);
        removeSession();
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

    function removeSession()
    {
        session_start();
        unset($_SESSION[GOALSESSION]);
    }