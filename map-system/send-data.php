<!-- TO-DO -->
<!-- Save groups_groupcode to database -->
<!--  -->
<?php
    if (isset($_GET['pos'])) {
        require './../required-files/connection.php';

        $position = $_GET['pos'];
        $groupCode = $_GET['groupcode'];

        $sql = "INSERT INTO positions (position, groups_groupcode) VALUES ('$position', '$groupCode')";

        mysqli_query($conn, $sql);
    }
?>