<?php

/**
 * * app/controllers/Otentifikasi.php
 */
class Otentifikasi extends Controller
{
    /**
     * * Mendefinisikan variable
     * @var class generator
     * @var class my_model
     */
    var $generator;
    private $my_model;

    /**
     * * Otentifikasi::__construct()
     * ? Constructor Function
     */
    public function __construct()
    {
        // TODO: Inisisasi class generator
        $generator = new RandomStringGenerator;
        $this->generator = $generator;

        // TODO: Set model
        $this->my_model = $this->model('Otentifikasi_model');
    }

    /**
     * * Otentifikasi::getKey()
     * ? Mendapatkan token
     */
    public function getKey()
    {
        // TODO: Call method to generate random string.
        $token = $this->generator->generate(TOKEN_LENGTH);

        // TODO: Simpan token ke database
        $this->my_model->createToken($token);

        echo json_encode($token);
        exit;
    }

    public function checkKey(string $param1)
    {
        echo $this->my_model->checkToken($param1);
    }
}
