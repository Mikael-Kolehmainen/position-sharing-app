<?php
    require './../required-files/dbHandler.php';

    if (isset($_GET['groupcode']))
    {
        // REMOVE ACTIVE GOAL
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_DEFAULT);
        removeGoal($groupCode);
    }

    function removeGoal($groupCode)
    {
        dbHandler::query("DELETE FROM goals WHERE groups_groupcode='$groupCode'");
    }
?>