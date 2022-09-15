<?php
    require './../required-files/dbHandler.php';

    if (isset($_GET['groupcode'])) {
        session_start();

        $uniqueID = $_SESSION['uniqueID'];

        removePosition($uniqueID);
        
        unset($_SESSION['uniqueID']);
    }

    function removePosition($id)
    {
        dbHandler::query("DELETE FROM positions WHERE uniqueID = '$id'");
    }