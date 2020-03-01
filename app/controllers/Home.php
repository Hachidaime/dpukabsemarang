<?php

/**
 * * app/controllers/Home.php
 */
class Home extends Controller
{
    /**
     * * Home::index()
     * ? Memuat Home
     */
    function index()
    {
        $data = [];
        Functions::setTitle('Home');
        $this->view('Home/index', $data);
    }
}
