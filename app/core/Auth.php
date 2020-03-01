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
    public function User(string $param)
    {
        return $_SESSION['USER'][$param];
    }
}
