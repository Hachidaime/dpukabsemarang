<?php

/**
 * * app/controllers/StaticPage.php
 */
class StaticPage extends Controller
{
    /**
     * * StaticPage::Error404()
     * ? Menampilkan halaman error 404
     */
    public function Error404()
    {
        Functions::setTitle('Oops! Something Went Wrong.');
        $this->view('StaticPage/notfound');
    }

    /**
     * * StaticPage::Error403()
     * ? Menampilkan halaman error 403
     */
    public function Error403()
    {
        Functions::setTitle('Oops! Something Went wrong.');
        $this->view('StaticPage/forbidden');
    }
}
