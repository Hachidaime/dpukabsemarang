<?php
class User extends Controller
{
    private $my_model;
    /* Start User */
    public function LoadUser($param1 = null, $param2 = null)
    {
        $this->my_model = $this->model('User_model');

        switch ($param1) {
            case 'search':
                $this->UserSearch();
                break;
            case 'add':
                $this->UserAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->UserEdit($param2);
                break;
            case 'submit':
                $this->UserSubmit();
                break;
            case 'remove':
                $this->UserRemove();
                break;
            default:
                $this->UserDefault();
        }
    }

    private function UserDefault()
    {
        Functions::setTitle("User");

        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        $data['thead'] = $this->my_model->getUserThead();
        $data['url'] = BASE_URL . "/User/LoadUser/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);

        $this->view('Layout/Default', $data);
    }

    private function UserSearch()
    {
        list($list, $count) = $this->my_model->getUser();
        $total = $this->my_model->totalUser();

        $rows = [];
        foreach ($list as $idx => $row) {
            $rows[$idx] = $row;
            $rows[$idx]['row'] = $idx + 1;
        }

        Functions::setDataTable($rows, $count, $total);
        exit;
    }

    private function UserAdd()
    {
        Functions::setTitle("Add User");

        $data['form'] = $this->my_model->getUserForm();

        $this->form($data);
    }

    private function UserEdit($id)
    {
        Functions::setTitle("Edit User");

        list($detail, $count) = $this->UserDetail($id);
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");
        $data['detail'] = $detail;

        $data['form'] = $this->my_model->getUserForm();

        $this->form($data);
    }

    private function UserSubmit()
    {
        $error = $this->UserValidate();

        if (!$error) {
            echo json_encode($this->UserProcess());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function UserValidate()
    {
        $form = $this->my_model->getUserForm();
        foreach ($form as $row) {
            $this->validate($_POST, $row, 'User_model', 'user');
        }

        return Functions::getDataSession('alert');
    }

    private function UserProcess()
    {
        if ($_POST['id'] > 0) {
            $result = $this->my_model->updateUser();
            $tag = "Edit";
        } else {
            $result = $this->my_model->createUser();
            $tag = "Add";
        }

        if ($result) {
            if ($_POST['id'] == Auth::User('id')) {
                list($detail, $count) = $this->UserDetail($_POST['id']);
                foreach ($detail as $key => $value) {
                    if ($key == 'password') continue;
                    $_SESSION['USER'][$key] = $value;
                }
            }
            Functions::setDataSession('alert', ["{$tag} User success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} User failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    private function UserDetail($id)
    {
        return $this->my_model->getUserDetail($id);
    }

    public function UserRemove()
    {
        $id = $_POST['id'];

        if ($id == Auth::User('id')) {
            Functions::setDataSession('alert', ["You are not authorized to delete this user.", 'danger']);
        } else {
            $result = $this->my_model->deleteUser($id);
            $tag = 'Remove';
            if ($result) {
                Functions::setDataSession('alert', ["{$tag} User success.", 'success']);
            } else {
                Functions::setDataSession('alert', ["{$tag} User failed.", 'danger']);
            }
        }

        return Functions::getDataSession('alert');
    }
    /* End User */

    /* Start User Group */
    public function LoadUserGroup($param1 = null, $param2 = null)
    {
        $this->my_model = $this->model('User_model');

        switch ($param1) {
            case 'search':
                $this->UserGroupSearch();
                break;
            case 'add':
                $this->UserGroupAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->UserGroupEdit($param2);
                break;
            case 'submit':
                $this->UserGroupSubmit();
                break;
            case 'remove':
                $this->UserGroupRemove();
                break;
            default:
                $this->UserGroupDefault();
        }
    }

    private function UserGroupDefault()
    {
        Functions::setTitle("User Group");

        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        $data['thead'] = $this->my_model->getUserGroupThead();
        $data['url'] = BASE_URL . "/User/LoadUserGroup/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);

        $this->view('Layout/Default', $data);
    }

    private function UserGroupSearch()
    {
        list($list, $count) = $this->my_model->getUserGroup();
        $total = $this->my_model->totalUserGroup();

        $rows = [];
        foreach ($list as $idx => $row) {
            $rows[$idx] = $row;
            $rows[$idx]['row'] = $idx + 1;
        }

        Functions::setDataTable($rows, $count, $total);
        exit;
    }

    private function UserGroupAdd()
    {
        Functions::setTitle("Add User Group");

        $data['form'] = $this->my_model->getUserGroupForm();

        $this->form($data);
    }

    private function UserGroupEdit($id)
    {
        Functions::setTitle("Edit User Group");

        $application_model = $this->model('Application_model');

        list($detail, $count) = $this->UserGroupDetail($id);
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");
        $data['detail'] = $detail;

        $data['form'] = $this->my_model->getUserGroupForm();

        $data['main'][] = $this->dofetch('Layout/Form', $data);

        $permission = [];
        list($my_permission,) = $this->my_model->getPermission($id);
        foreach ($my_permission as $idx => $row) {
            $permission[] = $row['action_id'];
        }

        list($action, $action_c) = $application_model->getAction();
        $access = [];
        if ($action_c) {
            foreach ($action as $idx => $row) {
                $obj = (object) $row;
                $access[$obj->system_id]['name'] = $obj->system;
                $access[$obj->system_id]['module'][$obj->module_id]['name'] = $obj->module;
                $access[$obj->system_id]['module'][$obj->module_id]['action'][$obj->id]['name'] = $obj->name;
                $access[$obj->system_id]['module'][$obj->module_id]['action'][$obj->id]['checked'] = (in_array($obj->id, $permission)) ? 'checked' : '';
            }
        }
        $data['access'] = $access;

        $data['main'][] = $this->dofetch('User/Permission', $data);

        $this->form($data);
    }

    private function UserGroupSubmit()
    {
        $error = $this->UserGroupValidate();

        if (!$error) {
            echo json_encode($this->UserGroupProcess());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function UserGroupValidate()
    {
        $form = $this->my_model->getUserGroupForm();
        foreach ($form as $row) {
            $this->validate($_POST, $row, 'User_model', 'user_group');
        }

        return Functions::getDataSession('alert');
    }

    private function UserGroupProcess()
    {
        if ($_POST['id'] > 0) {
            $result = $this->my_model->updateUserGroup();
            $tag = "Edit";
        } else {
            $result = $this->my_model->createUserGroup();
            $tag = "Add";
        }

        if ($result) {
            Functions::setDataSession('alert', ["{$tag} User Group success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} User Group failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    private function UserGroupDetail($id)
    {
        return $this->my_model->getUserGroupDetail($id);
    }

    private function UserGroupRemove()
    {
        $id = $_POST['id'];
        if ($id == Auth::User('user_group_id')) {
            Functions::setDataSession('alert', ["You are not authorized to delete this user.", 'danger']);
        } else {
            $result = $this->my_model->deleteUserGroup($id);
            $tag = 'Remove';
            if ($result) {
                Functions::setDataSession('alert', ["{$tag} User Group success.", 'success']);
            } else {
                Functions::setDataSession('alert', ["{$tag} User Group failed.", 'danger']);
            }
        }

        return Functions::getDataSession('alert');
    }
    /* End User Group */
}
