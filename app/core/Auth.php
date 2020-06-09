<?php

/**
 * * app/core/Auth.php
 */
class Auth
{
    /**
     * * Auth::User(param)
     * ? Showing Login User information
     * @param string param
     * ? User property
     */
    public static function User(string $param)
    {
        return $_SESSION['USER'][$param];
    }
}
