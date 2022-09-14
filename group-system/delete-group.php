<?php
    require './../required-files/dbHandler.php';

    if (isset($_GET['groupcode']))
    {
        deleteGroup();
    }

    function deleteGroup()
    {
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_DEFAULT);
    
        $result = deleteEntry("groups", "groupcode", $groupCode);
        $result2 = deleteEntry("goals", "groups_groupcode", $groupCode);
        $result3 = deleteEntry("messages", "groups_groupcode", $groupCode);
        $result4 = deleteEntry("positions", "groups_groupcode", $groupCode);

        if ($result && $result2 && $result3 && $result4)
        {
            header("LOCATION: ./../index.php");
        } 
        else 
        {
            echo "
                <script>
                    alert('Something went wrong with group removal, try again.');
                    window.location.href = './../map-system/active.php?groupcode=$groupCode';
                </script>
            ";
        }
    }

    function deleteEntry($tableName, $rowName, $rowValue)
    {
        return dbHandler::query("DELETE FROM $tableName WHERE $rowName = '$rowValue'");
    }
?>
