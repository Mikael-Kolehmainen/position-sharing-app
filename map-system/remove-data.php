<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';

    if (isset($_GET['groupcode'])) {
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
        removeGoal($groupCode);
    }

    function removeGoal($groupCode)
    {
        dbHandler::query("DELETE FROM goals WHERE ".GROUPS_GROUPCODE."='$groupCode'");
    }