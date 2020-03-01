<?php
class Setup extends Controller
{
    public function index(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Setup_model');
        switch ($param1) {
            case 'search':
                $this->SetupSearch();
                break;
            case 'add':
                $this->SetupAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->SetupEdit($param2);
                break;
            case 'submit':
                $this->SetupSubmit();
                break;
            case 'remove':
                $this->SetupRemove();
                break;
            default:
                $this->SetupDefault();
                break;
        }
    }

    private function SetupDefault()
    {
        Functions::setTitle("Setup");

        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        $data['thead'] = $this->my_model->getSetupThead();
        $data['url'] = BASE_URL . "/Setup/index/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);

        $this->view('Layout/Default', $data);
    }

    private function SetupSearch()
    {
        list($list, $count) = $this->my_model->getSetup();
        $total = $this->my_model->totalSetup();

        $jenis_opt = $this->options('jenis_opt');
        $simbol_opt = $this->options('simbol_opt');
        $kepemilikan_opt = $this->options('kepemilikan_opt');
        $perkerasan_opt = $this->options('perkerasan_opt');
        $kondisi_opt = $this->options('kondisi_opt');

        $rows = [];
        foreach ($list as $idx => $row) {
            $row['jenis'] = $jenis_opt[$row['jenis']];
            $row['simbol'] = $simbol_opt[$row['simbol']];
            $row['kepemilikan'] = $kepemilikan_opt[$row['kepemilikan']];
            $row['perkerasan'] = $perkerasan_opt[$row['perkerasan']];
            $row['kondisi'] = $kondisi_opt[$row['kondisi']];

            $html = [
                '<ul class="list-unstyled mb-0">',
                '<li>Warna: <i class="fas fa-circle" style="color:' . $row['warna'] . '"></i> ' . $row['warna'] . '</li>',
                '<li>Opacity: ' . $row['opacity'] . '%</li>',
                '<li>Line Width: ' . $row['line_width'] . 'px</li>',
                '</ul>'
            ];
            $row['keterangan'] = implode("", $html);

            $row['row'] = Functions::getSearch()['offset'] + $idx + 1;
            array_push($rows, $row);
        }

        Functions::setDataTable($rows, $count, $total);
        exit;
    }

    private function SetupAdd()
    {
        Functions::setTitle("Add Setup");

        $data['form'] = $this->my_model->getSetupForm();

        $this->form($data);
    }

    private function SetupEdit($id)
    {
        Functions::setTitle("Edit Setup");

        list($detail, $count) = $this->SetupDetail($id);
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");
        $data['detail'] = $detail;

        $data['form'] = $this->my_model->getSetupForm();

        $this->form($data);
    }

    private function SetupSubmit()
    {
        $error = $this->SetupValidate();

        if (!$error) {
            echo json_encode($this->SetupProcess());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function SetupValidate()
    {
        $form = $this->my_model->getSetupForm();
        foreach ($form as $row) {
            $this->validate($_POST, $row, 'Setup_model', 'setup');
        }

        return Functions::getDataSession('alert');
    }

    private function SetupProcess()
    {
        if ($_POST['id'] > 0) {
            $result = $this->my_model->updateSetup();
            $tag = "Edit";
        } else {
            $result = $this->my_model->createSetup();
            $tag = "Add";
        }

        if ($result) {
            Functions::setDataSession('alert', ["{$tag} Setup success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} Setup failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    private function SetupDetail($id)
    {
        return $this->my_model->getSetupDetail($id);
    }

    public function SetupRemove()
    {
        $id = $_POST['id'];

        $result = $this->my_model->deleteSetup($id);
        $tag = 'Remove';
        if ($result) {
            Functions::setDataSession('alert', ["{$tag} Setup success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} Setup failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }
}
