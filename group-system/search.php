<!-- Brings the user to the group map if there's a group with given groupcode. -->
<?php
    require './../required-files/dbHandler.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['groupcode'])) 
    {
        $result = selectGroups();
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

        if (isset($groupCode)) 
        {
            $initials = filterPost('initials');
            $color = filterPost('color');

            saveMarker($initials, $color);
            header("LOCATION: ./../map-system/active.php?groupcode=$groupCode");
        } 
        else 
        {
            echo "
                <script>
                    alert('Couldn\'t find a group with the given code, try again.');
                    window.location.href = './search-form.php';
                </script>
            ";
        }
    }
    else if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['initials'])) 
    {
        $initials = filterPost('initials');
        $color = filterPost('color');

        saveMarker($initials, $color);
        header("LOCATION: ./create.php");
    }

    function saveMarker($initials, $color) 
    {
        session_start();
        if ($color == "") {
            $color = "#FF0000";
        }
        $initials = strtoupper($initials);
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