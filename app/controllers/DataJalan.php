<?php

/**
 * * app/controllers/Data.php
 */
class DataJalan extends Controller
{
    public function index(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Data_model');

        switch ($param1) {
            case 'search':
                $this->DataSearch();
                break;

            default:
                $this->DataDefault();
                break;
        }
    }

    private function DataDefault()
    {
        Functions::setTitle("Data Jalan");

        $data['thead'] = $this->my_model->getDataThead();
        $data['data'] = Functions::defaultTableData();
        $data['search'] = false;
        $data['url'] = BASE_URL . "/DataJalan/index/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);
        $this->view('Layout/Default', $data);
    }

    private function DataSearch()
    {
        $list = $this->my_model->getAllDataJalan();

        $jalan = [];
        foreach ($list['jalan'] as $idx => $row) {
            $row['koordinat'] = (!empty($row['ori'])) ? $row['ori'] : $row['segmented'];
            $row['koordinat'] = json_decode($row['koordinat'], true);
            $jalan[] = $row;
        }
        print '<pre>';
        print_r($jalan);
        print '</pre>';
    }
}
