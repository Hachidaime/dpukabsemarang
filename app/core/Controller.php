<?php

/**
 * * app/core/Controller.php
 */
class Controller
{
    /**
     * * Mendefinisikan variable
     * @var object $smarty
     */
    var $smarty;

    /**
     * * Controller::__construct
     * ? Contructor Function
     */
    public function __construct()
    {
        // * Call Global variable
        global $smarty;

        // TODO: Set Global Method
        $this->smarty = &$smarty;

        // TODO: Check Admin & User Session
        if (isset($_SESSION['admin']) && isset($_SESSION['USER'])) { // ? Admin & User Session Exist
            // TODO: Set SERVER_BASE_ADMIN as BASE_URL
            define('BASE_URL', SERVER_BASE_ADMIN);
        } else { // ! Admin & User Session NOT exist
            // TODO: Set SERVER_BASE as BASE_URL
            define('BASE_URL', SERVER_BASE);
        }

        // TODO: SET Global Properties
        $this->remote_ip = $_SERVER['REMOTE_ADDR'];
        $this->login_id = Auth::User('id');
        $this->user_group_id = Auth::User('user_group_id');

        $this->btn_add      = Functions::makeButton("button", "add", "Tambah Data", "success", "btn-add");
        $this->btn_back     = Functions::makeButton("button", "back", "Back", "danger", "btn-back");
        $this->btn_submit   = Functions::makeButton("button", "submit", "Submit", "success", "btn-submit");
    }

    /**
     * * Controller::view
     * ? Load full web page
     * @param string $view 
     * ? Template file's path on app/view/ folder
     * @param array $data
     * ? Value that will be assigned on template
     */
    public function view(string $view, array $data = [])
    {
        // TODO: Check user permission, will be redirected to Error page if user no have permission
        if (!$this->permission()) Header("Location: " . BASE_URL . "/StaticPage/Error403");

        // TODO: Set active Controller & Method into data
        list($data['controller'], $data['method']) = $this->currentPage();

        // TODO: Assign data into template
        $this->smarty->assign('data', $data);

        /**
         * * Navigation
         */
        // TODO: Call system, module, & menu on Navbar
        $system = $this->model('Layout_model')->system();
        $module = $this->model('Layout_model')->module();
        $menu = $this->model('Layout_model')->menu();

        // TODO: Assign system, module, & menu on Navbar
        $this->smarty->assign('system', $system);
        $this->smarty->assign('module', $module);
        $this->smarty->assign('menu', $menu);

        // TODO: Fetch Navigation template
        $header = $this->smarty->fetch('Layout/Header.php');

        // TODO: Fetch Content template
        $content = $this->smarty->fetch($view . '.php');

        // TODO: Assign Navigation & Content
        $this->smarty->assign('header', $header);
        $this->smarty->assign('content', $content);

        // TODO: Display template (Layout/Mainlayout.php)
        return $this->smarty->display('Layout/Mainlayout.php');
    }

    /**
     * * Controller::dofetch
     * ? Display part of web page
     * @param string $fetch
     * ? Template file's path on app/view/ folder
     * @param array $data
     * ? Value that will be assigned on template
     */
    public function dofetch(string $fetch, array $data = [])
    {
        // TODO: Set active Controller & Method into data
        list($data['controller'], $data['method']) = $this->currentPage();

        // TODO: Assign data into template
        $this->smarty->assign('data', $data);

        // TODO: Fetch template file
        return $this->smarty->fetch($fetch . '.php');
    }

    /**
     * * Controller::model
     * ? Call Model Class
     * @param string $model
     * ? Model Class name
     */
    public function model(string $model)
    {
        // TODO: Call Model Class
        require_once 'app/models/' . $model . '.php';

        // TODO: Model class initiation
        return new $model;
    }

    /**
     * * Controller::currentPage
     * ? Get active Controller & Method
     */
    public function currentPage()
    {
        // TODO: Get Controller & Method from URL
        list($controller, $method) = Functions::parseUrl();

        // TODO: Check & Set Controller & Method from URL. If empty, use default.
        $controller = (!empty($controller)) ? $controller : DEFAULT_CONTROLLER;
        $method = (!empty($method)) ? $method : DEFAULT_METHOD;

        // TODO: Return Controller & Method value
        return array($controller, $method);
    }

    /**
     * * Controller::permission
     * ? Check User Permission
     */
    private function permission()
    {
        // TODO: Get active Controller & Method
        list($controller, $method) = $this->currentPage();

        // TODO: Get active Menu
        $menu = $this->model('Layout_model')->menu();
        $menu = Functions::genOptions($menu, 'class_name');
        array_push($menu, 'StaticPage', 'Login');

        // TODO: Check non-menu Controller
        if (!in_array($controller, $menu)) { // ? Controller is non-menu
            // TODO: Check Admin & User Session
            if (isset($_SESSION['admin']) && isset($_SESSION['USER'])) { // ? Admin & User session exist
                // TODO: Check allowed method
                $ok = $this->model('Session_model')->checkPermission($this->user_group_id, $method);

                // TODO: Check result
                if ($ok) return 1; // ? Return TRUE
                else return 0; // ! Return FALSE
            } else { // ! Admin & User Session NOT exist
                return 0; // ! Return FALSE
            }
        } else { // ? Controller Menu
            return 1; // ? Return TRUE
        }
    }

    /**
     * * Controller::options
     * ? Get Select Options
     * @param string $select_code
     * ? Select options Code
     */
    protected function options(string $select_code, bool $is_all = false)
    {
        // TODO: Get select options
        $select_options = $this->model('Select_model')->getOptions($select_code);
        $options = [];
        if ($is_all === true) {
            $options['all'] = "Semua";
        }

        foreach (explode(PHP_EOL, trim($select_options)) as $val) {
            list($value, $label, $display) = explode(",", $val);
            if (trim($display) == 'show') $options[$value] = $label;
        }
        return $options;
    }

    /**
     * * Controller::validate
     * ? Validate form input
     * @param array $data
     * ? Input value
     * @param array $input
     * ? Input properties
     * @param string $model
     * ? Model name
     * @param string $checker_type
     * ? Type Checker
     */
    protected function validate(array $data, array $input, string $model, string $checker_type = null)
    {
        // TODO: Call model
        $my_model = $this->model($model);

        // TODO: Check input required
        if ($input['required']) { // ? Input is required
            // TODO: Check empty input
            if (empty($data[$input['name']])) { // ! Input is empty
                // TODO: Display message based on input type
                switch ($input['type']) {
                    case 'select': // ? SELECT Input type
                        Functions::setDataSession('alert', ["<strong>{$input['label']}</strong> must be selected.", 'danger']);
                        break;
                    case 'password': // ? PASSWORD Input type
                        if ($data['id'] <= 0) Functions::setDataSession('alert', ["<strong>{$input['label']}</strong> cannot be blank.", 'danger']);
                        break;
                    default: // ? Input default
                        Functions::setDataSession('alert', ["<strong>{$input['label']}</strong> cannot be blank.", 'danger']);
                }
            } else { // ? Input not empty
                // TODO: Check input is unique
                if ($input['unique']) { // ? Input is unique
                    // TODO: Check unique value from database
                    if ($my_model->checkUnique($my_model->getTable($checker_type), (int) $data['id'], $input['name'], $data[$input['name']])) { // ! Value is already exist in database
                        Functions::setDataSession('alert', ["<strong>{$input['label']}</strong> already exist.", 'danger']);
                    }
                }
            }
        }

        // TODO: Check input type
        switch ($input['type']) {
            case 'number': // ? Input value is number
                // TODO: Check input is not empty
                if (!empty($data[$input['name']])) { // ? Input is not empty
                    // TODO: Check value is numeric
                    if (!is_numeric($data[$input['name']])) Functions::setDataSession('alert', ["<strong>{$input['label']}</strong> must be in numeric.", 'danger']);
                } else {
                    $_POST[$input['name']] = 0;
                }
                break;
        }
    }

    /**
     * * Controller::form
     * ? Load form default input
     * @param array $data
     * ? Data to load on form input
     */
    public function form(array $data = [])
    {
        // TODO: Set form element
        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_back);
        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_submit);

        // TODO: Check main content
        if (!isset($data['main'])) { // ! Main content NOT found
            // TODO: Menampilkan Form
            $data['main'][] = $this->dofetch('Layout/Form', $data);
        }

        // TODO: Menampilkan Template
        $this->view('Layout/Default', $data);
    }
}
