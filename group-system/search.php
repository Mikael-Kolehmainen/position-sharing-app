<!-- Brings the user to the group map if there's a group with given groupcode. -->
<?php
    require './../required-files/dbHandler.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['groupcode'])) 
    {
        redirectUserToGroup($_POST['groupcode']);
    }
    else if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['initials']))
    {
        createGroup();
    }

/*    function findGroupInDatabase()
    {
        $result = selectGroups();
        $groupCode = "";

        if (mysqli_num_rows($result) >= 0)
        {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) 
            {
                $row = mysqli_fetch_assoc($result);

                if ($_POST['groupcode'] == $row['groupcode']) 
                {
                    $groupCode = $row['groupcode'];
                }
            }
        }
        redirectUserToGroup($groupCode);
    } */

    function createGroup()
    {
        $initials = filterPost('initials');
        $color = filterPost('color');

        saveMarkerToSession($initials, $color);
        header("LOCATION: ./create.php");
    }

    // SLÅ IHOP DESSA TVÅ

    function redirectUserToGroup($groupCode)
    {
        $initials = filterPost('initials');
        $color = filterPost('color');

        saveMarkerToSession($initials, $color);
        header("LOCATION: ./../map-system/active.php?groupcode=$groupCode");
     /*   else 
        {
            echo "
                <script>
                    alert('Couldn\'t find a group with the given code, try again.');
                    window.location.href = './search-form.php';
                </script>
            ";
        } */
    }

    function saveMarkerToSession($initials, $color) 
    {
        if ($color == "")
        {
            $color = "#FF0000";
        }
        
        $initials = strtoupper($initials);

        session_start();

        $_SESSION['initials'] = $initials;
        $_SESSION['color'] = $color;
    }

    function filterPost($postname)
    {
        return filter_input(INPUT_POST, $postname, FILTER_DEFAULT);
    }

    function selectGroups()
    {
        return dbHandler::query("SELECT groupcode FROM groups");
    }
?>