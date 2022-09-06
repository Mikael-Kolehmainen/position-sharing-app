<?php
    require './../required-files/dbHandler.php';

    if (isset($_GET['groupcode'])) 
    {
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_DEFAULT);
    
        $result = deleteEntry("DELETE FROM groups WHERE groupcode = '$groupCode'");
        $result2 = deleteEntry("DELETE FROM goals WHERE groups_groupcode = '$groupCode'");
        $result3 = deleteEntry("DELETE FROM messages WHERE groups_groupcode = '$groupCode'");
        $result4 = deleteEntry("DELETE FROM positions WHERE groups_groupcode = '$groupCode'");

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

    function deleteEntry($sql)
    {
        return dbHandler::query($sql);
    }
?>
