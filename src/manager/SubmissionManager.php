<?php
namespace manager;


class SubmissionManager
{
    public static function isPost()
    {
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }

    public static function isGet()
    {
        return $_SERVER["REQUEST_METHOD"] == "GET";
    }

    public static function issetCreateGroup()
    {
        return isset($_POST[FORM_CREATE_GROUP]);
    }

    public static function issetSearchGroup()
    {
        return isset($_POST[FORM_SEARCH_GROUP]);
    }
}