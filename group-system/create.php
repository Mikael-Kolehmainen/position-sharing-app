<!-- Creates a group and checks if the groupcode is unique then directs the user to the groupmap with the groupcode. -->
<?php
    require './../required-files/dbHandler.php';

    createGroup();

    function createGroup() 
    {
        require './../required-files/random-string.php';
        
        $groupCode = getRandomString(3);

        $result = selectGroups();
        if (mysqli_num_rows($result) > 0) 
        {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) 
            {
                $row = mysqli_fetch_assoc($result);
                if ($groupCode == $row['groupcode'])
                {
                    createGroup();
                }
            }
        }

        insertGroup($groupCode);
    }

    function insertGroup($groupCode) 
    {
        $result = addGroup($groupCode);

        if ($result) 
        {
            header("LOCATION: ./../map-system/active.php?groupcode=$groupCode");
        } 
        else 
        {
            echo "
                <script>
                    alert('Something went wrong with group creation.');
                    window.location.href = './../index.php';
                </script>
            ";
        }
    }

    function selectGroups()
    {
        return dbHandler::query("SELECT groupcode FROM groups");
    }

    function addGroup($groupCode)
    {
        return dbHandler::query("INSERT INTO groups (groupcode) VALUES ('$groupCode')");
    }
?>