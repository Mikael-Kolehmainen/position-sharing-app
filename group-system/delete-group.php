<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';

    if (isset($_GET['groupcode'])) {
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        if (deleteGroupFromDatabase($groupCode)) {
            header("LOCATION: ./../index.php");
        } else {
            redirectUserToGroupMap($groupCode);
        }
    }

    function deleteGroupFromDatabase($groupCode)
    {
        $result = deleteEntry("groups", "groupcode", $groupCode);
        $result2 = deleteEntry("goals", GROUPS_GROUPCODE, $groupCode);
        $result3 = deleteEntry("messages", GROUPS_GROUPCODE, $groupCode);
        $result4 = deleteEntry("positions", GROUPS_GROUPCODE, $groupCode);

        $groupGotDeleted = false;

        if ($result && $result2 && $result3 && $result4) {
            $groupGotDeleted = true;
        } 

        return $groupGotDeleted;
    }

    function redirectUserToGroupMap($groupCode)
    {
        echo "
                <script>
                    alert('Something went wrong with group removal, try again.');
                    window.location.href = './../map-system/active.php?groupcode=$groupCode';
                </script>
            ";
    }

    function deleteEntry($tableName, $rowName, $rowValue)
    {
        return dbHandler::query("DELETE FROM $tableName WHERE $rowName = '$rowValue'");
    }
?>
