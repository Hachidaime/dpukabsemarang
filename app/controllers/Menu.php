<?php

/**
 * * app/controllers/Menu.php
 */
class Menu extends Controller
{
    /**
     * * Mendefinisikan variable
     */
    private $my_model;

    /**
     * * Menu::index($param, $param2)
     * @param string param1
     * ? submethod
     * @param string param2
     * ? id
     */
    public function index($param1 = null, $param2 = null)
    {
        // TODO: Set model
        $this->my_model = $this->model('Menu_model');

        // TODO: Select submethod
        switch ($param1) {
            case 'search': // ? Search Menu
                $this->MenuSearch();
                break;
            case 'add': // ? Menampilkan halaman Add Menu
                $this->MenuAdd();
                break;
            case 'edit': // ? Menampilkan halaman Edit Menu
                // TODO: Cek parameter id Menu
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404"); // ! Id Gallery kosong, Redirect ke Error 404

                $this->MenuEdit($param2);
                break;
            case 'submit': // ? Sumbit Form Menu
                $this->MenuSubmit();
                break;
            case 'remove': // ? Menghapus Menu
                $this->MenuRemove($_POST['id']);
                break;
            default: // ? Menampilkan halaman Menu
                $this->MenuDefault();
                break;
        }
    }

    /**
     * * Menu::MenuDefault()
     * ? Menampilkan halaman Menu
     */
    private function MenuDefault()
    {
        Functions::setTitle("Menu");

        // TODO: Menampilkan Toolbar
        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        // TODO: Menampilkan Table
        $data['thead'] = $this->my_model->getMenuThead();
        $data['url'] = BASE_URL . "/Menu/index/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);

        // TODO: Menampilkan Template
        $this->view('Layout/Default', $data);
    }

    /**
     * * Menu::MenuSearch()
     * ? Mencari Menu dari Database
     */
    private function MenuSearch()
    {
        // TODO: Get listing Menu
        list($list, $count) = $this->my_model->getMenu();
        // TODO: Get total Menu
        $total = $this->my_model->totalMenu();

        // TODO: Modify listing Menu
        $rows = [];
        foreach ($list as $idx => $row) {
            $row['row'] = $idx + 1;
            $row['website'] = ($row['show_website']) ? 'YES' : 'NO';
            $row['admin'] = ($row['show_admin']) ? 'YES' : 'NO';
            array_push($rows, $row);
        }

        // TODO: Mengembalikan result
        Functions::setDataTable($rows, $count, $total);
        exit;
    }

    /**
     * * Menu::MenuAdd()
     * ? Menampilkan Halaman Add Menu
     */
    private function MenuAdd()
    {
        Functions::setTitle("Add Menu");

        $data['form'] = $this->my_model->getMenuForm();
        $this->form($data);
    }

    /**
     * * Menu::MenuEdit($id)
     * @param id
     * ? id menu
     */
    private function MenuEdit($id)
    {
        Functions::setTitle("Edit Menu");

        // TODO: Get Menu dari Database
        list($detail, $count) = $this->MenuDetail($id);

        // TODO: Set detail Menu
        $data['detail'] = $detail;

        // TODO: Cek Menu exist
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");

        $data['form'] = $this->my_model->getMenuForm();
        $this->form($data);
    }

    /**
     * * Menu::MenuSubmit()
     * ? Submit Form Menu
     */
    private function MenuSubmit()
    {
        // TODO: Get Validasi form Menu
        $error = $this->MenuValidate();

        // TODO: Cek error
        if (!$error) { // ? No Error
            echo json_encode($this->MenuProcess());
        } else { // ! Error
            echo json_encode($error);
        }
        exit;
    }

    /**
     * * Menu::MenuValidate()
     * ? Validasi form Menu
     */
    private function MenuValidate()
    {
        // TODO: Get form Menu
        $form = $this->my_model->getMenuForm();

        foreach ($form as $row) {
            // TODO: Validasi form Menu
            $this->validate($_POST, $row, 'Menu_model', 'menu');
        }

        // TODO: Mengembalikan hasil validasi
        return Functions::getDataSession('alert');
    }

    /**
     * * Menu::MenuProcess()
     * ? Proses form input
     */
    private function MenuProcess()
    {
        // TODO: Cek input id
        if ($_POST['id'] > 0) { // ? Id Menu exist
            // TODO: Proses edit Menu
            $result = $this->my_model->updateMenu();
            $tag = "Edit";
        } else { // ! Id Menu not exist
            // TODO: Proses add Menu
            $result = $this->my_model->createMenu();
            $tag = "Add";
        }

        // TODO: Cek hasil proses
        if ($result) { // ? Proses success
            Functions::setDataSession('alert', ["{$tag} Menu success.", 'success']);
        } else { // ! Proses gagal
            Functions::setDataSession('alert', ["{$tag} Menu failed.", 'danger']);
        }

        // TODO: Mengembalikan hasil proses
        return Functions::getDataSession('alert');
    }

    /**
     * * Menu::MenuDetail($id)
     * ? Get menu detail by id
     * @param id
     * ? Id Menu
     */
    private function MenuDetail($id)
    {
        return $this->my_model->getMenuDetail($id);
    }

    /**
     * * Menu::MenuRemove($id)
     * ? Menghapus Menu by id
     * @param id
     * ? Id Menu
     */
    public function MenuRemove($id)
    {
        // TODO: Proses hapus data
        $result = $this->my_model->deleteMenu($id);
        $tag = 'Remove';

        // TODO: Cek hasil proses hapus
        if ($result) { // ? Hapus Menu success
            Functions::setDataSession('alert', ["{$tag} Menu success.", 'success']);
        } else { // ! Hapus Menu gagal
            Functions::setDataSession('alert', ["{$tag} Menu failed.", 'danger']);
        }

        // TODO: Mengembalikan hasil proses
        return Functions::getDataSession('alert');
    }
}
