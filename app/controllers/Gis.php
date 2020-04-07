<?php

/**
 * * app/controllers/Gis.php
 */
class Gis extends Controller
{
    private $my_model;
    private $jalan_options;

    /**
     * * Gis::index()
     * ? Menampilkan Halaman Gis
     */
    public function index(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Gis_model');

        switch ($param1) {
            case 'jalan':
                $this->SearchJalan();
                break;

            default:
                $this->GisDefault();
                break;
        }
    }

    private function GisDefault()
    {
        Functions::setTitle('GIS');
        $data = [];
        $data['formClass'] = "searchGisForm";
        $data['form'] = $this->my_model->getGisForm();
        $data['mini'] = true;
        $data['searchform'] = $this->dofetch('Layout/Form', $data);
        // $data['searchbtn'][] = $this->dofetch('Component/Button', Functions::makeButton("button", "search-gis", "Cari", "success", "btn-search-gis"));

        $this->view('Gis/index', $data);
    }

    private function SearchJalan()
    {
        $cond = [];
        if (!empty($_POST['kepemilikan'])) {
            if ($_POST['kepemilikan'] != 'all') {
                $cond[] = "kepemilikan = {$_POST['kepemilikan']}";
            }
            $jalan_options = $this->model('Jalan_model')->getJalanOptions($cond);

            echo json_encode($jalan_options);
            exit;
        }
    }
}
