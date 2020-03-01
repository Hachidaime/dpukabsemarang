<?php

/**
 * * app/controllers/Login.php
 */
class Login extends Controller
{
    /**
     * * Login::index()
     * ? Menampilkan halaman Log In
     */
    function index()
    {
        // TODO: Mengarahkan ke halaman Admin jika sudah login
        if (isset($_SESSION['USER'])) Header("Location: " . SERVER_BASE_ADMIN . "/Home");

        Functions::setTitle('Login');
        $this->view('Login/index');
    }
}
