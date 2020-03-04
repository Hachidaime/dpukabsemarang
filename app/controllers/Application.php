<?php
class Application extends Controller
{
    var $application_model;
    private $type;
    private $method;

    /* Start System */
    public function System(string $param1 = null, int $param2 = null)
    {
        $this->my_model = $this->model('Application_model');
        $this->type = 'system';
        $this->method = __FUNCTION__;

        switch ($param1) {
            case 'search':
                $this->ApplicationSearch();
                break;
            case 'add':
                $this->ApplicationAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->ApplicationEdit($param2);
                break;
            case 'submit':
                $this->ApplicationSubmit();
                break;
            case 'remove':
                $this->ApplicationRemove();
                break;
            default:
                $this->ApplicationDefault();
                break;
        }
    }
    /* End System */

    /* Start Module */
    public function Module($param1 = null, $param2 = null)
    {
        $this->my_model = $this->model('Application_model');
        $this->type = 'module';
        $this->method = __FUNCTION__;

        switch ($param1) {
            case 'search':
                $this->ApplicationSearch();
                break;
            case 'add':
                $this->ApplicationAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->ApplicationEdit($param2);
                break;
            case 'submit':
                $this->ApplicationSubmit();
                break;
            case 'remove':
                $this->ApplicationRemove();
                break;
            default:
                $this->ApplicationDefault();
                break;
        }
    }
    /* End Module */

    /* Start Action */
    public function Action($param1 = null, $param2 = null)
    {
        $this->my_model = $this->model('Application_model');
        $this->type = 'action';
        $this->method = __FUNCTION__;

        switch ($param1) {
            case 'search':
                $this->ApplicationSearch();
                break;
            case 'add':
                $this->ApplicationAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->ApplicationEdit($param2);
                break;
            case 'submit':
                $this->ApplicationSubmit();
                break;
            case 'remove':
                $this->ApplicationRemove();
                break;
            default:
                $this->ApplicationDefault();
                break;
        }
    }
    /* End Action */

    private function ApplicationDefault()
    {
        Functions::setTitle($this->method);

        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        $data['data'] = Functions::defaultTableData();
        switch ($this->type) {
            case 'system':
                $data['thead'] = $this->my_model->getSystemThead();
                break;
            case 'module':
                $data['thead'] = $this->my_model->getModuleThead();
                break;
            case 'action':
                $data['thead'] = $this->my_model->getActionThead();
                break;
        }
        $data['url'] = BASE_URL . "/Application/{$this->method}/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);

        $this->view('Layout/Default', $data);
    }

    private function ApplicationSearch()
    {
        switch ($this->type) {
            case 'system':
                list($list, $count) = $this->my_model->getSystem();
                break;
            case 'module':
                list($list, $count) = $this->my_model->getModule();
                break;
            case 'action':
                list($list, $count) = $this->my_model->getAction();
                break;
        }

        $total = $this->my_model->totalApplication($this->type);

        $rows = [];
        foreach ($list as $idx => $row) {
            $row['row'] = $idx + 1;
            array_push($rows, $row);
        }

        Functions::setDataTable($rows, $count, $total);
        exit;
    }

    private function ApplicationAdd()
    {
        Functions::setTitle("Add $this->method");

        switch ($this->type) {
            case 'system':
                $data['form'] = $this->my_model->getSystemForm();
                break;
            case 'module':
                $data['form'] = $this->my_model->getModuleForm();
                break;
            case 'action':
                $data['form'] = $this->my_model->getActionForm();
                break;
        }

        $this->form($data);
    }

    private function ApplicationEdit($id)
    {
        Functions::setTitle("Edit $this->method");

        switch ($this->type) {
            case 'system':
                $data['form'] = $this->my_model->getSystemForm();
                break;
            case 'module':
                $data['form'] = $this->my_model->getModuleForm();
                break;
            case 'action':
                $data['form'] = $this->my_model->getActionForm();
                break;
        }

        list($detail, $count) = $this->ApplicationDetail($id);
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");
        $data['detail'] = $detail;

        $this->form($data);
    }

    private function ApplicationDetail($id)
    {
        return $this->my_model->getApplicationDetail($this->type, $id);
    }

    private function ApplicationSubmit()
    {
        $error = $this->ApplicationValidate();

        if (!$error) {
            echo json_encode($this->ApplicationProcess());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function ApplicationValidate()
    {
        switch ($this->type) {
            case 'system':
                $form = $this->my_model->getSystemForm();
                break;
            case 'module':
                $form = $this->my_model->getModuleForm();
                break;
            case 'action':
                $form = $this->my_model->getActionForm();
                break;
        }

        foreach ($form as $row) {
            $this->validate($_POST, $row, "Application_model", $this->type);
        }

        return Functions::getDataSession('alert');
    }

    private function ApplicationProcess()
    {
        if ($_POST['id'] > 0) {
            $result = $this->my_model->updateApplication($this->type);
            $tag = "Edit";
        } else {
            $result = $this->my_model->createApplication($this->type);
            $tag = "Add";
        }

        if ($result) {
            Functions::setDataSession('alert', ["{$tag} {$this->method} success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} {$this->method} failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    private function ApplicationRemove()
    {
        $id = $_POST['id'];

        $result = $this->my_model->deleteApplication($this->type, $id);

        $tag = 'Remove';
        if ($result) {
            Functions::setDataSession('alert', ["{$tag} {$this->method} success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} {$this->method} failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }
}
