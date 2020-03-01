<?php

/**
 * * app/core/App.php
 */
class App
{
    /**
     * * Mendefinisikan default variable untuk Contoller, Method, & Parameter
     * @var string $controller
     * @var string $method
     * @var array $params
     */
    protected $controller = DEFAULT_CONTROLLER;
    protected $method = DEFAULT_METHOD;
    protected $params = [];

    /**
     * * App::__construct
     * ? Constructor Function
     */
    public function __construct()
    {
        // TODO: Parsing URL
        $url = Functions::parseURL();

        // TODO: Check Admin & User Session
        if (isset($_SESSION['admin']) && !isset($_SESSION['USER'])) { // ! Admin & User Session NOT found
            // TODO: Redirect to Login Page
            if ($url[0] != 'Login') Header("Location: " . SERVER_BASE . "/Login");
        }

        // TODO: Check Controller from URL
        if (isset($url[0]) && !empty($url[0])) {
            // TODO: Check Controller file location
            if (file_exists('app/controllers/' . $url[0] . ".php")) { // ? Controller file exist
                // TODO: Set Controller from URL
                $this->controller = $url[0];

                // TODO: Unset Controller in URL
                unset($url[0]);
            } else { // ! Controller file NOT FOUND
                // TODO: Redirect to Error Page
                Header("Location: " . BASE_URL . "/StaticPage/Error404");
            }
        }

        // * Call Controller Class
        require_once 'app/controllers/' . $this->controller . '.php';

        // TODO: Controller Initioation
        $this->controller = new $this->controller;

        // TODO: Check Method from URL
        if (isset($url[1])) { // ? Method found in URL
            // TODO: Check Methon on Controller
            if (method_exists($this->controller, $url[1])) { // ? Method exist in Controller
                // TODO: Set Method from URL
                $this->method = $url[1];

                // TODO: Unset Method in URL
                unset($url[1]);
            } else { // ! Method NOT found
                // TODO: Redirect to Error page
                Header("Location: " . BASE_URL . "/StaticPage/Error404");
            }
        }
        // * Use Default Method if Method NOT FOUND in URL

        // TODO: Check $url element
        if (!empty($url)) { // ? $url element not empty
            // TODO: Set $url as parameter/arguments
            $this->params = array_values($url);
        }

        // TODO: Call Controller & Method
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
}
