<?php
    require './../required-files/dbHandler.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['message'])) 
    {
        session_start();
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
        $initials = $_SESSION['initials'];
        $color = $_SESSION['color'];
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_DEFAULT);
        
        $result = addMessage($message, $initials, $color, $groupCode);

        if ($result) 
        {
            header("LOCATION: active.php?groupcode=$groupCode");
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