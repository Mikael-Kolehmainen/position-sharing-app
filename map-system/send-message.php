<?php
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['message'])) {
        require './../required-files/connection.php';

        session_start();
        $message = $_POST['message'];
        $initials = $_SESSION['initials'];
        $color = $_SESSION['color'];
        $groupCode = $_GET['groupcode'];

        $sql = "INSERT INTO messages (message, initials, color, groups_groupcode)
                VALUES ('$message', '$initials', '$color', '$groupCode')";

        if (mysqli_query($conn, $sql)) {
            header("LOCATION: active.php?groupcode=".$_GET['groupcode']);
        } else {
            echo "
                <script>
                    alert('Couldn\'t send the message, try again.');
                    window.location.href = './active.php?groupcode=$groupCode';
                </script>
            ";
        }
    }
?>