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
                exit;
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
        $data['url'] = BASE_URL . "/Laporan/dd1/search";
        // $data['url'] = SERVER_BASE . "/data/LaporanDD1.json";
        $data['main'][] = $this->dofetch('Layout/Table', $data);
        $this->view('Layout/Default', $data);
    }

    private function Dd1Search()
    {
        $kepemilikan_opt = $this->options('kepemilikan_opt'); // TODO: Get Kepemilikan Options
        $list =  $this->my_model->getLaporanDd1();
        $data = $list;

        $alphabet = range('A', 'Z');

        $field = [];
        foreach ($this->model('Laporan_model')->getDd1Thead()[3] as $row) {
            $row['field'] = ($row['field'] == 'perkerasan_1') ? 'perkerasan_2' : (($row['field'] == 'perkerasan_2') ? 'perkerasan_1' : $row['field']);
            if (!empty($row['field'])) $field[$row['field']] = '';
        }

        $start = 0;
        foreach ($list as $idx => $row) {
            if ($row['kepemilikan'] != $list[$idx - 1]['kepemilikan']) {
                $field['nama_jalan'] = "<strong>{$alphabet[$row['kepemilikan'] - 1]}. {$kepemilikan_opt[$row['kepemilikan']]}</strong>";
                array_splice($data, $idx + $start, 0, [$field]);
                $start++;
            }
        }

        echo json_encode($data);
        exit;
    }

    public function dd2(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Laporan_model');

        switch ($param1) {
            default:
                $this->Dd2Default();
                break;
        }
    }

    private function Dd2Default()
    {
        Functions::setTitle("Laporan DD2");

        // TODO: Menampilkan Table
        $data['thead'] = $this->my_model->getDd2Thead();
        $data['data'] = Functions::makeTableData(['show-export' => 'true']);
        $data['search'] = false;
        // $data['url'] = BASE_URL . "/Laporan/dd1/search";
        $data['url'] = SERVER_BASE . "/data/LaporanDD2.json";
        $data['main'][] = $this->dofetch('Layout/Table', $data);
        $this->view('Layout/Default', $data);
    }
}
