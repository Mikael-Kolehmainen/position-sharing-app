<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST[MESSAGE])) {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        if (sendMessage($groupCode)) {
            header("LOCATION: active.php?".GROUPCODE."=$groupCode");
        } else {
            redirectUserToGroupMap($groupCode);
        }
    }

    function sendMessage($groupCode)
    {
        session_start();
        $message = filter_input(INPUT_POST, MESSAGE, FILTER_SANITIZE_SPECIAL_CHARS);
        $initials = $_SESSION[INITIALS];
        $color = $_SESSION[COLOR];

        return insertMessageToDatabase($message, $initials, $color, $groupCode);
    }

    function insertMessageToDatabase($message, $initials, $color, $groupCode)
    {
        return dbHandler::query("INSERT INTO ".MESSAGES." (".MESSAGE.", ".INITIALS.", ".COLOR.", ".GROUPS_GROUPCODE.") VALUES ('$message', '$initials', '$color', '$groupCode')");
    }

    function redirectUserToGroupMap($groupCode)
    {
        echo "
                <script>
                    alert('Couldn\'t send the message, try again.');
                    window.location.href = './active.php?".GROUPCODE."=$groupCode';
                </script>
            ";
    }