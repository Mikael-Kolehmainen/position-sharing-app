<?php
class Redirect
{
    public static function redirect($message, $url)
    {
        echo "
            <script>
                alert('$message');
                window.location.replace('$url');
            </script>
        ";
    }
}