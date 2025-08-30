<?php
namespace App\Core;

class Response
{
    public static function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    public static function redirectNamed($routeName, $params = [])
    {
        $url = Router::getRoute($routeName, $params);
        self::redirect($url);
    }
}
?>