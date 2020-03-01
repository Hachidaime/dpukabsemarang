<?php

/**
 *  * app/controller/Session.php
 */
class Session extends Controller
{
    /**
     * * Session::__construct()
     * ? Constructor Function
     */
    public function __construct()
    {
        // TODO: Set Model
        $this->my_model = $this->model('Session_model');
    }

    /**
     * * Session::login()
     * ? Submit form login
     */
    public function login()
    {
        $error = false;

        // TODO: Cek username terisi
        if (empty($_POST['username'])) {
            // ! username kosong
            Functions::setDataSession('alert', ['Username cannot be blank.', 'danger']);
            $error = true;
        }

        // TODO: Cek password terisi
        if (empty($_POST['password'])) {
            // ! password kosong
            Functions::setDataSession('alert', ['Password cannot be blank.', 'danger']);
            $error = true;
        }

        // TODO: Cek ada error
        if (!$error) { // ? Tidak ada error
            // TODO: Mencari Username di Database
            list($user, $count) = $this->my_model->getExistUsername();

            // TODO: Cek Username exist
            if ($count > 0) { // ? Username exist
                // TODO: Decrypt password di database
                $password = Functions::decrypt($user['password']);

                // TODO: Cek password cocok
                if ($password == $_POST['password']) { // ? Login Sucess
                    Functions::setDataSession('alert', ['Login OK!', 'success']);
                    unset($user['password']);

                    // TODO: Set Session User
                    $user['menu_id'] = DEFAULT_MENU_ID;
                    Functions::setDataSession('USER', $user);
                } else { // ! Password tidak cocok
                    Functions::setDataSession('alert', ['Username and Password didn\'t match.', 'danger']);
                }
            } else { // ! Username tidak ditemukan
                Functions::setDataSession('alert', ['Username not found.', 'danger']);
            }
        }

        echo json_encode(Functions::getDataSession('alert'));
        exit;
    }

    /**
     * * Session::logout()
     * ? Log Out User Session
     */
    public function logout()
    {
        // TODO: Clear Session User
        Functions::clearDataSession('USER');
        Functions::setDataSession('alert', ['Good Bye!', 'success']);

        echo json_encode(Functions::getDataSession('alert'));
        exit;
    }

    /**
     * * Session::setMenu()
     * ? Set System
     */
    public function setMenu()
    {
        // TODO: Get Session User
        $user = Functions::getDataSession('USER', false);

        // TODO: Set Session User menu_id
        $user['menu_id'] = $_POST['id'];
        Functions::setDataSession('USER', $user);
        echo 1;
        exit;
    }
}
