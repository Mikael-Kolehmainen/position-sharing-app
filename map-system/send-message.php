<?php
    require './../required-files/dbHandler.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['message'])) 
    {
        session_start();
        $message = $_POST['message'];
        $initials = $_SESSION['initials'];
        $color = $_SESSION['color'];
        $groupCode = $_GET['groupcode'];
        
        $result = addMessage($message, $initials, $color, $groupCode);

        if ($result) 
        {
            header("LOCATION: active.php?groupcode=".$_GET['groupcode']);
        } 
        else 
        {
            echo "
                <script>
                    alert('Couldn\'t send the message, try again.');
                    window.location.href = './active.php?groupcode=$groupCode';
                </script>
            ";
        }
    }

    function addMessage($message, $initials, $color, $groupCode)
    {
        return dbHandler::query("INSERT INTO messages (message, initials, color, groups_groupcode) VALUES ('$message', '$initials', '$color', '$groupCode')");
    }
?>