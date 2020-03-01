<?php

class Application extends Controller
{
    var $application_model;

    /* Start System */
    public function System(string $param1 = null, int $param2 = null)
    {
        $this->my_model = $this->model('Application_model');
        switch ($param1) {
            case 'search':
                $this->SystemSearch();
                break;
            case 'add':
                $this->SystemAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->SystemEdit($param2);
                break;
            case 'submit':
                $this->SystemSubmit();
                break;
            case 'remove':
                $this->SystemRemove();
                break;
            default:
                $this->SystemDefault();
        }
    }

    private function SystemDefault()
    {
        Functions::setTitle("System");

        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        $data['thead'] = $this->my_model->getSystemThead();
        $data['url'] = BASE_URL . "/Application/System/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);

        $this->view('Layout/Default', $data);
    }

    private function SystemSearch()
    {
        list($list, $count) = $this->my_model->getSystem();
        $total = $this->my_model->totalSystem();

        $rows = [];
        foreach ($list as $idx => $row) {
            $rows[$idx] = $row;
            $rows[$idx]['row'] = $idx + 1;
        }

        $result = [];
        $result['total'] = $count;
        $result['totalNotFiltered'] = $total;
        $result['rows'] = $rows;
        echo json_encode($result);
        exit;
    }

    private function SystemAdd()
    {
        Functions::setTitle("Add System");

        $data['form'] = $this->my_model->getSystemForm();

        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_back);
        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_submit);
        $data['main'][] = $this->dofetch('Layout/Form', $data);

        $this->view('Layout/Default', $data);
    }

    private function SystemEdit($id)
    {
        Functions::setTitle("Edit System");

        list($detail, $count) = $this->SystemDetail($id);
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");
        $data['detail'] = $detail;

        $data['form'] = $this->my_model->getSystemForm();

        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_back);
        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_submit);
        $data['main'][] = $this->dofetch('Layout/Form', $data);

        $this->view('Layout/Default', $data);
    }

    private function SystemSubmit()
    {
        $error = $this->SystemValidate();

        if (!$error) {
            echo json_encode($this->SystemProcess());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function SystemValidate()
    {
        $form = $this->my_model->getSystemForm();
        foreach ($form as $row) {
            $this->validate($_POST, $row, 'Application_model', 'system');
        }

        return Functions::getDataSession('alert');
    }

    private function SystemProcess()
    {
        if ($_POST['id'] > 0) {
            $result = $this->my_model->updateSystem();
            $tag = "Edit";
        } else {
            $result = $this->my_model->createSystem();
            $tag = "Add";
        }

        if ($result) {
            Functions::setDataSession('alert', ["{$tag} System success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} System failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    private function SystemDetail($id)
    {
        return $this->my_model->getSystemDetail($id);
    }

    private function SystemRemove()
    {
        // print_r($_POST);
        $id = $_POST['id'];
        $result = $this->my_model->deleteSystem($id);
        $tag = 'Remove';
        if ($result) {
            Functions::setDataSession('alert', ["{$tag} System success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} System failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }
    /* End System */

    /* Start Module */
    public function Module($param1 = null, $param2 = null)
    {
        $this->my_model = $this->model('Application_model');

        switch ($param1) {
            case 'search':
                $this->ModuleSearch();
                break;
            case 'add':
                $this->ModuleAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->ModuleEdit($param2);
                break;
            case 'submit':
                $this->ModuleSubmit();
                break;
            case 'remove':
                $this->ModuleRemove();
                break;
            default:
                $this->ModuleDefault();
        }
    }

    private function ModuleDefault()
    {
        Functions::setTitle("Module");

        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        $data['thead'] = $this->my_model->getModuleThead();
        $data['url'] = BASE_URL . "/Application/Module/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);

        $this->view('Layout/Default', $data);
    }

    private function ModuleSearch()
    {
        list($list, $count) = $this->my_model->getModule();
        $total = $this->my_model->totalModule();

        $rows = [];
        foreach ($list as $idx => $row) {
            $rows[$idx] = $row;
            $rows[$idx]['row'] = $idx + 1;
        }

        $result = [];
        $result['total'] = $count;
        $result['totalNotFiltered'] = $total;
        $result['rows'] = $rows;
        echo json_encode($result);
        exit;
    }

    private function ModuleAdd()
    {
        Functions::setTitle("Add Module");

        $data['form'] = $this->my_model->getModuleForm();

        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_back);
        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_submit);
        $data['main'][] = $this->dofetch('Layout/Form', $data);

        $this->view('Layout/Default', $data);
    }

    private function ModuleEdit($id)
    {
        Functions::setTitle("Edit Module");

        list($detail, $count) = $this->ModuleDetail($id);
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");

        $data['detail'] = $detail;
        $data['form'] = $this->my_model->getModuleForm();

        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_back);
        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_submit);
        $data['main'][] = $this->dofetch('Layout/Form', $data);

        $this->view('Layout/Default', $data);
    }

    private function ModuleSubmit()
    {
        $error = $this->ModuleValidate();

        if (!$error) {
            echo json_encode($this->ModuleProcess());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function ModuleValidate()
    {
        $form = $this->my_model->getModuleForm();
        foreach ($form as $row) {
            $this->validate($_POST, $row, 'Application_model', 'module');
        }

        return Functions::getDataSession('alert');
    }

    private function ModuleProcess()
    {
        if ($_POST['id'] > 0) {
            $result = $this->my_model->updateModule();
            $tag = "Edit";
        } else {
            $result = $this->my_model->createModule();
            $tag = "Add";
        }

        if ($result) {
            Functions::setDataSession('alert', ["{$tag} Module success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} Module failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    private function ModuleDetail($id)
    {
        return $this->my_model->getModuleDetail($id);
    }

    private function ModuleRemove()
    {
        // print_r($_POST);
        $id = $_POST['id'];
        $result = $this->my_model->deleteModule($id);
        $tag = 'Remove';
        if ($result) {
            Functions::setDataSession('alert', ["{$tag} Module success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} Module failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }
    /* End Module */

    /* Start Action */
    public function Action($param1 = null, $param2 = null)
    {
        $this->my_model = $this->model('Application_model');
        switch ($param1) {
            case 'search':
                $this->ActionSearch();
                break;
            case 'add':
                $this->ActionAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->ActionEdit($param2);
                break;
            case 'submit':
                $this->ActionSubmit();
                break;
            case 'remove':
                $this->ActionRemove();
                break;
            default:
                $this->ActionDefault();
        }
    }

    private function ActionDefault()
    {
        Functions::setTitle("Action");

        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        $data['thead'] = $this->my_model->getActionThead();
        $data['url'] = BASE_URL . "/Application/Action/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);

        $this->view('Layout/Default', $data);
    }

    private function ActionSearch()
    {
        list($list, $count) = $this->my_model->getAction();
        $total = $this->my_model->totalAction();

        $rows = [];
        foreach ($list as $idx => $row) {
            $rows[$idx] = $row;
            $rows[$idx]['row'] = $idx + 1;
        }

        $result = [];
        $result['total'] = $count;
        $result['totalNotFiltered'] = $total;
        $result['rows'] = $rows;
        echo json_encode($result);
        exit;
    }

    private function ActionAdd()
    {
        Functions::setTitle("Add Action");

        $data['form'] = $this->my_model->getActionForm();

        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_back);
        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_submit);
        $data['main'][] = $this->dofetch('Layout/Form', $data);

        $this->view('Layout/Default', $data);
    }

    private function ActionEdit($id)
    {
        Functions::setTitle("Edit Action");

        list($detail, $count) = $this->ActionDetail($id);
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");
        $data['detail'] = $detail;

        $data['form'] = $this->my_model->getActionForm();

        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_back);
        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_submit);
        $data['main'][] = $this->dofetch('Layout/Form', $data);

        $this->view('Layout/Default', $data);
    }

    private function ActionSubmit()
    {
        $error = $this->ActionValidate();

        if (!$error) {
            echo json_encode($this->ActionProcess());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function ActionValidate()
    {
        $form = $this->my_model->getActionForm();
        foreach ($form as $row) {
            $this->validate($_POST, $row, 'Application_model', 'action');
        }

        return Functions::getDataSession('alert');
    }

    private function ActionProcess()
    {
        if ($_POST['id'] > 0) {
            $result = $this->my_model->updateAction();
            $tag = "Edit";
        } else {
            $result = $this->my_model->createAction();
            $tag = "Add";
        }

        if ($result) {
            Functions::setDataSession('alert', ["{$tag} Action success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} Action failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    private function ActionDetail($id)
    {
        return $this->my_model->getActionDetail($id);
    }

    private function ActionRemove()
    {
        // print_r($_POST);
        $id = $_POST['id'];
        $result = $this->my_model->deleteAction($id);
        $tag = 'Remove';
        if ($result) {
            Functions::setDataSession('alert', ["{$tag} Action success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} Action failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }
    /* End Action */
}
