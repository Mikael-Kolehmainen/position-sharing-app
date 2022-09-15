<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';

    if (isset($_GET[GROUPCODE])) {
        session_start();

        $uniqueID = $_SESSION['uniqueID'];

        removePosition($uniqueID);
        
        unset($_SESSION['uniqueID']);
    }

    function removePosition($id)
    {
        dbHandler::query("DELETE FROM ".POSITIONS." WHERE uniqueID = '$id'");
    }