<?php
namespace manager;

class SessionManager
{
    private const GROUPCODE = "groupcode";
    private const USER_DB_ROW_ID = "user_row_id";
    private const INITIALS = "initials";
    private const COLOR = "color";
    private const AMOUNT_OF_MESSAGES = "amount_of_messages";
    private const GOAL_SESSION = "goalsession";

    public static function saveGroupCode($groupCode): void
    {
        $_SESSION[self::GROUPCODE] = $groupCode;
    }

    public static function getGroupCode()
    {
        return $_SESSION[self::GROUPCODE];
    }

    public static function issetGroupCode()
    {
        return isset($_SESSION[self::GROUPCODE]);
    }

    public static function saveUserRowId($userRowId): void
    {
        $_SESSION[self::USER_DB_ROW_ID] = $userRowId;
    }

    public static function getUserRowId()
    {
        return $_SESSION[self::USER_DB_ROW_ID];
    }

    public static function saveUserInitials($initials): void
    {
        $_SESSION[self::INITIALS] = $initials;
    }

    public static function getUserInitials()
    {
        return $_SESSION[self::INITIALS];
    }

    public static function saveUserColor($color): void
    {
        $_SESSION[self::COLOR] = $color;
    }

    public static function getUserColor()
    {
        return $_SESSION[self::COLOR];
    }

    public static function saveAmountOfMessages($amountOfMessages): void
    {
        $_SESSION[self::AMOUNT_OF_MESSAGES] = $amountOfMessages;
    }

    public static function getAmountOfMessages()
    {
        return $_SESSION[self::AMOUNT_OF_MESSAGES];
    }

    public static function removeAmountOfMessages()
    {
        unset($_SESSION[self::AMOUNT_OF_MESSAGES]);
    }

    public static function saveGoalSession($goalSession)
    {
        $_SESSION[self::GOAL_SESSION] = $goalSession;
    }

    public static function getGoalSession()
    {
        return $_SESSION[self::GOAL_SESSION];
    }

    public static function removeGoalSession()
    {
        unset($_SESSION[self::GOAL_SESSION]);
    }
}