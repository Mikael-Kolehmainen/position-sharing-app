<?php
require './../required-files/constants.php';
require './../required-files/random-string.php';
require './../autoloader.php';

session_start();
if (isset($_GET[GROUPCODE]) && isset($_SESSION[USER_ROW_ID]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $groupCode = filter_input(INPUT_GET, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

    $message = new Message($groupCode);
    $message->webImagePath = $_FILES["webimagepath"];
    $message->webImageType = filter_input(INPUT_POST, "webimagetype", FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
    $message->userID = $_SESSION[USER_ROW_ID];
    $message->createImagePath();
    if ($message->saveImageToServer()) {
        $message->saveImagePath();
    }
}