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

        insertMessageToDatabase($message, $messageSenderID, $groupCode);
    }

    function insertMessageToDatabase($messageText, $messageSenderID, $groupCode)
    {
        $message = new Message($groupCode);
        $message->message = $messageText;
        $message->userID = $messageSenderID;
        $message->save();
    }