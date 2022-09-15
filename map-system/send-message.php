<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['message'])) {
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        if (sendMessage($groupCode)) {
            header("LOCATION: active.php?groupcode=$groupCode");
        } else {
            redirectUserToGroupMap($groupCode);
        }
    }

    function sendMessage($groupCode)
    {
        session_start();
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
        $initials = $_SESSION['initials'];
        $color = $_SESSION['color'];

        return insertMessageToDatabase($message, $initials, $color, $groupCode);
    }

    function insertMessageToDatabase($message, $initials, $color, $groupCode)
    {
        return dbHandler::query("INSERT INTO messages (message, initials, color, ".GROUPS_GROUPCODE.") VALUES ('$message', '$initials', '$color', '$groupCode')");
    }

    function redirectUserToGroupMap($groupCode)
    {
        echo "
                <script>
                    alert('Couldn\'t send the message, try again.');
                    window.location.href = './active.php?groupcode=$groupCode';
                </script>
            ";
    }