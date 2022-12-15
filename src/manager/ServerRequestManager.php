<?php

namespace manager;

class ServerRequestManager
{
    private const REQUEST_METHOD = "REQUEST_METHOD";
    private const POST = "POST";
    private const GET = "GET";
    private const REQUEST_URI = "REQUEST_URI";

    private const CREATE_GROUP = "create-group";
    private const SEARCH_GROUP = "search-group";
    private const GROUP_CODE = "groupcode";
    private const COLOR = "color";
    private const INITIALS = "initials";
    private const MESSAGE = "message";
    private const WEB_IMAGE_TYPE = "webimagetype";

    public static function isPost()
    {
        return $_SERVER[self::REQUEST_METHOD] == self::POST;
    }

    public static function isGet()
    {
        return $_SERVER[self::REQUEST_METHOD] == self::GET;
    }

    public static function issetCreateGroup()
    {
        return isset($_POST[self::CREATE_GROUP]);
    }

    public static function issetSearchGroup()
    {
        return isset($_POST[self::SEARCH_GROUP]);
    }

    public static function getUriParts()
    {
        $uri = parse_url($_SERVER[self::REQUEST_URI], PHP_URL_PATH);
        return explode('/', $uri);
    }

    public static function postGroupCode()
    {
        return filter_input(INPUT_POST, self::GROUP_CODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
    }

    public static function postUserColor()
    {
        return filter_input(INPUT_POST, self::COLOR, FILTER_DEFAULT);
    }

    public static function postUserInitials()
    {
        return filter_input(INPUT_POST, self::INITIALS, FILTER_DEFAULT);
    }

    public static function postMessage()
    {
        return filter_input(INPUT_POST, self::MESSAGE, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public static function postWebimageType()
    {
        return filter_input(INPUT_POST, self::WEB_IMAGE_TYPE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
    }
}
