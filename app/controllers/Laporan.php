<?php
class Laporan extends Controller
{
    private $my_model;

    public function dd1(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Laporan_model');

        switch ($param1) {
            case 'search':
                $this->Dd1Search();
                break;

            default:
                $this->Dd1Default();
                break;
        }
    }

    private function Dd1Default()
    {
        Functions::setTitle("Laporan DD1");

        // TODO: Menampilkan Table
        $data['thead'] = $this->my_model->getDd1Thead();
        $data['data'] = Functions::makeTableData(['show-export' => 'true']);
        $data['search'] = false;
        // $data['url'] = BASE_URL . "/Laporan/dd1/search";
        $data['url'] = SERVER_BASE . "/data/LaporanDD1.json";
        $data['main'][] = $this->dofetch('Layout/Table', $data);
        $this->view('Layout/Default', $data);
    }
}
