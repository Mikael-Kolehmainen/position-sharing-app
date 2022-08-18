<?php
    if (isset($_GET['groupcode'])) {
        $groupCode = $_GET['groupcode'];
        require './../required-files/connection.php';
    
        $sql = "DELETE FROM groups WHERE groupcode = '$groupCode'";
        $sql2 = "DELETE FROM goals WHERE groups_groupcode = '$groupCode'";
        $sql3 = "DELETE FROM messages WHERE groups_groupcode = '$groupCode'";
        $sql4 = "DELETE FROM positions WHERE groups_groupcode = '$groupCode'";

        if (mysqli_query($conn, $sql)) {
            mysqli_query($conn, $sql2);
            mysqli_query($conn, $sql3);
            mysqli_query($conn, $sql4);
            header("LOCATION: ./../index.php");
        } else {
            echo "
                <script>
                    alert('Something went wrong with group removal, try again.');
                    window.location.href = './../map-system/active.php?groupcode=$groupCode';
                </script>
            ";
        }
    }
?>
