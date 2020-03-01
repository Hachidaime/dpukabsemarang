<?php

/**
 * * app/controller/Gallery.php
 */
class Gallery extends Controller
{
    /**
     * * Mendefinisikan variable
     * @var object $my_model
     */
    private $my_model;

    /**
     * * Gallery::index($param, $param2)
     * ? Method yang dapat diakses dari browser
     * @param string param1
     * ? submethod
     * @param int param2
     * ? id
     */
    function index(string $param1 = null, int $param2 = null)
    {
        // TODO: Set model
        $this->my_model = $this->model('Gallery_model');

        // TODO: Select submethod
        switch ($param1) {
            case 'search': // ? Search Galley dari database
                // TODO: Cek session Admin & User
                if (isset($_SESSION['admin']) && isset($_SESSION['USER'])) { // ? Session Admin & User exist
                    // TODO: Menampilkan search untuk session Admin (Login)
                    $this->GalleryAdminSearch();
                } else {
                    // TODO: Menampilkan search untuk Public (tanpa Login)
                    $this->GalleryPublicSearch();
                }
                break;
            case 'add': // ? Menampilkan halaman tambah Gallery
                $this->GalleryAdd();
                break;
            case 'edit': // ? Menampilkan halaman edit Gallery
                // TODO: Cek parameter id Gallery
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404"); // ! Id Gallery kosong, Redirect ke Error 404

                $this->GalleryEdit($param2);
                break;
            case 'submit': // ? Sumbit Form Menu
                $this->GallerySubmit();
                break;
            default: // ? Menampilkan halaman Gallery
                // TODO: Cek session Admin & User
                if (isset($_SESSION['admin']) && isset($_SESSION['USER'])) { // ? Session Admin & User exist
                    // TODO: Menampilkan halaman Gallery pada Session Admin & User
                    $this->GalleryAdmin();
                } else {
                    // TODO: Menampilkan halaman Gallery pada Public
                    $this->GalleryPublic();
                }
                break;
        }
    }

    /**
     * * Gallery::GalleryPublic()
     * ? Menampilkan halaman Gallery tanpa login
     */
    private function GalleryPublic()
    {
        // TODO: Proses data Pagination
        $limit = $this->my_model->limit;
        $total = $this->my_model->totalGallery();
        $total_page = ceil($total / $limit);

        // TODO: Menampilkan Pagination
        $data['total'] = $total;
        $data['total_page'] = $total_page;
        $data['selector'] = "btn-gallery-page";
        $pager = $this->dofetch('Component/Pagination', $data);
        $data['pager'] = $pager;

        // TODO: Menampilkan Tamplate
        Functions::setTitle('Gallery');
        $this->view('Gallery/Public', $data);
    }

    /**
     * * Gallery::GalleryPublicSearch()
     * ? Mencari Menu dari database untuk public
     */
    private function GalleryPublicSearch()
    {
        // TODO: Mencari Gallery di database
        list($list, $count) = $this->my_model->getGallery(true);

        // TODO: Menampilkan hasil pencarian
        $data['item'] = $list;
        $item = $this->dofetch('Gallery/Item', $data);

        // TODO: Mengembalikan result
        $result = [];
        $result['item'] = $item;
        echo json_encode($result);
        exit;
    }

    /**
     * * Gallery::GalleryAdmin()
     * ? Menampilkan halaman Gallery untuk session Admin (Login)
     */
    private function GalleryAdmin()
    {
        Functions::setTitle("Gallery");

        // TODO: Menampilkan Table
        $data['thead'] = $this->my_model->getGalleryThead();
        $data['url'] = BASE_URL . "/Gallery/index/search";

        // TODO: Menampilkan Toolbar
        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        // TODO: Menampilkan Template
        $data['main'][] = $this->dofetch('Layout/Table', $data);
        $this->view('Layout/Default', $data);
    }

    /**
     * * Gallery::GalleryAdminSearch()
     * ? Mencari Menu darbase untuk Admin
     */
    private function GalleryAdminSearch()
    {
        // TODO: Mencari Gallery di database
        list($list, $count) = $this->my_model->getGallery();
        // TODO: Mencari total Gallery
        $total = $this->my_model->totalGallery();

        // TODO: Modify listing Gallery
        $rows = [];
        foreach ($list as $idx => $row) {
            $row['tanggal'] = Functions::formatDatetime($row['tanggal'], 'd/m/Y');
            $row['upload_gallery'] = Functions::getPopupLink("img/gallery/{$row['id']}", $row['upload_gallery'], $row['judul'], $row['tanggal']);
            $row['row'] = Functions::getSearch()['offset'] + $idx + 1;
            array_push($rows, $row);
        }

        // TODO: Mengembalikan result
        Functions::setDataTable($rows, $count, $total);
        exit;
    }

    /**
     * * Gallery::GalleryAdd()
     * ? Menampilkan Halaman Add Gallery
     */
    private function GalleryAdd()
    {
        Functions::setTitle("Tambah Data Gallery");

        // TODO: Get form Gallery
        $data['form'] = $this->my_model->getGalleryForm();

        $this->form($data);
    }

    /**
     * * Gallery::GalleryEdit($id)
     * @param int $id
     * ? Id Gallery
     */
    private function GalleryEdit(int $id)
    {
        Functions::setTitle("Edit Gallery");
        // TODO: Get Gallery dari Database
        list($detail, $count) = $this->GalleryDetail($id);

        // TODO: Cek Gallery exist
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");

        // TODO: Set detail Gallery
        $data['detail'] = $detail;

        // TODO: Get form Gallery
        $data['form'] = $this->my_model->getGalleryForm();

        $this->form($data);
    }

    /**
     * * Gallery::GallerySubmit()
     * ? Submit Form Menu
     */
    private function GallerySubmit()
    {
        // TODO: Get Validasi form Menu
        $error = $this->GalleryValidate();

        // TODO: Cek error
        if (!$error) { // ? No error
            echo json_encode($this->GalleryProcess());
        } else { // ! Error
            echo json_encode($error);
        }
        exit;
    }

    /**
     * * Gallery::GalleryValidate()
     * ? Validasi form Gallery
     */
    private function GalleryValidate()
    {
        // TODO: Get form Menu
        $form = $this->my_model->getGalleryForm();

        foreach ($form as $idx => $row) {
            // TODO: Validasi form Menu
            $this->validate($_POST, $row, 'Gallery_model', 'gallery');
        }

        // TODO: Mengembalikan hasil validasi
        return Functions::getDataSession('alert');
    }

    /**
     * * Gallery::GalleryProcess()
     * ? Proses form input
     */
    private function GalleryProcess()
    {
        // TODO: Get form Pengaduan
        $form = $this->my_model->getGalleryForm();

        // TODO: Cek input id
        if ($_POST['id'] > 0) { // ? Id Gallery exist
            // TODO: Proses edit Gallery
            $result = $this->my_model->updateGallery();
            $id = $_POST['id'];
            $tag = "Edit";
        } else { // ! Id Gallery not exist
            // TODO: Proses add Gallery
            $result = $this->my_model->createGallery();
            $id = $this->my_model->insert_id();
            $tag = "Add";
        }

        // TODO: Cek hasil proses
        if ($result) { // ? Process success
            Functions::setDataSession('alert', ["{$tag} Gallery success.", 'success']);

            // TODO: Pindah foto dari temporary directory ke direktory Gallery
            foreach ($form as $row) {
                if ($row['type'] == 'img') {
                    if (!empty($_POST[$row['name']])) {
                        FileHandler::MoveFromTemp("img/gallery/{$id}", $_POST[$row['name']]);
                    }
                }
            }
        } else { // ! Process gagal
            Functions::setDataSession('alert', ["{$tag} Gallery failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    /**
     * * Gallery::GalleryDetail($id)
     * ? Get Gallry detail by id
     * @param int $id
     * ? Id Gallery
     */
    private function GalleryDetail(int $id)
    {
        return $this->my_model->getGalleryDetail($id);
    }

    /**
     * * Gallery::GalleryRemove($id)
     * ? Menghapus Gallery by id
     * @param int $id
     * ? Id Gallery
     */
    public function GalleryRemove(int $id)
    {
        // TODO: Proses hapus data
        $result = $this->my_model->deleteGallery($id);
        $tag = 'Remove';

        // TODO: Cek hasil proses hapus
        if ($result) { // ? Hapus Gallery success
            Functions::setDataSession('alert', ["{$tag} Menu success.", 'success']);
        } else { // ! Hapus Gallery gagal
            Functions::setDataSession('alert', ["{$tag} Menu failed.", 'danger']);
        }

        // TODO: Mengembalikan hasil proses
        return Functions::getDataSession('alert');
    }
}
