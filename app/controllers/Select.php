<?php
class Select extends Controller
{
    /* Start Select */
    public function index($param1 = null, $param2 = null)
    {
        $this->my_model = $this->model('Select_model');

        $data = [];
        switch ($param1) {
            case 'search':
                $this->SelectSearch();
                break;
            case 'add':
                $this->SelectAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->SelectEdit($param2);
                break;
            case 'submit':
                $this->SelectSubmit();
                break;
            case 'remove':
                $this->SelectRemove();
                break;
            default:
                $this->SelectDefault();
        }
    }

    private function SelectDefault()
    {
        Functions::setTitle("Select");

        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        $data['thead'] = $this->my_model->getSelectThead();
        $data['url'] = BASE_URL . "/Select/index/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);

        $this->view('Layout/Default', $data);
    }

    private function SelectSearch()
    {
        list($list, $count) = $this->my_model->getSelect();
        $total = $this->my_model->totalSelect();

        $rows = [];
        foreach ($list as $idx => $row) {
            $options = [];
            array_push($options, '<ul class="list-unstyled mb-0">');
            foreach (explode(PHP_EOL, trim($row['options'])) as $key => $val) {
                $color = "";
                list($value, $label, $display) = explode(",", $val);
                switch (trim($display)) {
                    case 'show':
                        $color = "body";
                        break;
                    case 'hide':
                        $color = "secondary";
                        break;
                }
                array_push($options, '<li class="text-' . $color . '">' . $value . ' = ' . $label . '</li>');
            }
            array_push($options, "</ul>");
            $row['options'] = implode("", $options);
            $row['row'] = $idx + 1;
            array_push($rows, $row);
        }

        Functions::setDataTable($rows, $count, $total);
        exit;
    }

    private function SelectAdd()
    {
        Functions::setTitle("Add Select");

        $data['form'] = $this->my_model->getSelectForm();

        $this->form($data);
    }

    private function SelectEdit($id)
    {
        Functions::setTitle("Edit Select");

        list($detail, $count) = $this->SelectDetail($id);
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");
        $data['detail'] = $detail;

        $data['form'] = $this->my_model->getSelectForm();

        $this->form($data);
    }

    private function SelectSubmit()
    {
        $error = $this->SelectValidate();

        if (!$error) {
            echo json_encode($this->SelectProcess());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function SelectValidate()
    {
        $form = $this->my_model->getSelectForm();
        foreach ($form as $idx => $row) {
            $this->validate($_POST, $row, 'Select_model', 'select');
        }

        return Functions::getDataSession('alert');
    }

    private function SelectProcess()
    {
        if ($_POST['id'] > 0) {
            $result = $this->my_model->updateSelect();
            $tag = "Edit";
        } else {
            $result = $this->my_model->createSelect();
            $tag = "Add";
        }

        if ($result) {
            Functions::setDataSession('alert', ["{$tag} Select success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} Select failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    private function SelectDetail($id)
    {
        return $this->my_model->getSelectDetail($id);
    }

    public function SelectRemove()
    {
        $id = $_POST['id'];

        $result = $this->my_model->deleteSelect($id);
        $tag = 'Remove';
        if ($result) {
            Functions::setDataSession('alert', ["{$tag} Select success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} Select failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }
    /* End Select */
}
