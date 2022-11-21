<?php
require './../../autoloader.php';
require './../../required-files/constants.php';

$goal = new Goal("01A");

session_start();
echo $goal->getGoalCookie()[0]["goalcookie"] == $_SESSION[GOALSESSION];