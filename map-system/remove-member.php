<?php
    if (isset($_GET['groupcode'])) {
        session_start();
        require './../required-files/connection.php';
        $uniqueID = $_SESSION['uniqueID'];
        $sql = "DELETE FROM positions WHERE uniqueID = '$uniqueID'";
        unset($_SESSION['uniqueID']);
        mysqli_query($conn, $sql);
    }
?>