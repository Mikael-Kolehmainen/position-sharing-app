<?php
    if (isset($_GET['groupcode'])) {
        // REMOVE ACTIVE GOAL
        require './../required-files/connection.php';
        $groupCode = $_GET['groupcode'];
        $sql = "DELETE FROM goals WHERE groups_groupcode='$groupCode'";
        mysqli_query($conn, $sql);
    }
?>