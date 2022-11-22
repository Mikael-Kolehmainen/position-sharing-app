<?php
    require './../../required-files/constants.php';
    require './../../autoloader.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST[MESSAGE])) {
        $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        sendMessage($groupCode);
        header("LOCATION: ../active.php?".GROUPCODE."=$groupCode");
    }

    function sendMessage($groupCode)
    {
        session_start();
        $message = filter_input(INPUT_POST, MESSAGE, FILTER_SANITIZE_SPECIAL_CHARS);

        $messageSenderID = $_SESSION[USER_ROW_ID];
        $fallbackInitials = $_SESSION[INITIALS];
        $fallbackColor = $_SESSION[COLOR];

        insertMessageToDatabase($message, $fallbackInitials, $fallbackColor, $messageSenderID, $groupCode);
    }

    function insertMessageToDatabase($messageText, $fallbackInitials, $fallbackColor, $messageSenderID, $groupCode)
    {
        $message = new Message($groupCode);
        $message->message = $messageText;
        $message->fallbackInitials = $fallbackInitials;
        $message->fallbackColor = $fallbackColor;
        $message->userID = $messageSenderID;
        $message->save();
    }