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
        $data['url'] = BASE_URL . "/Laporan/dd1/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);
        $this->view('Layout/Default', $data);
    }

    private function Dd1Search()
    {
        list($list, $count) = $this->my_model->getJalan();
        // $total = $this->my_model->totalJalan();

        // TODO: Load Kepemilikan Options
        $kepemilikan_opt = $this->options('kepemilikan_opt');

        // TODO: Prepare data to load on template
        $data = [];
        foreach ($list as $idx => $row) {
            $row['kepemilikan_text'] = $kepemilikan_opt[$row['kepemilikan']];
            $row['panjang_km'] = number_format($row['panjang'] / 1000, 2);
            $row['list'][] =  $row['koordinat'];
            $rows['no_jalan'] = $row['no_jalan'];
            $rows['nama_jalan'] = $row['nama_jalan'];
            $rows['kepemilikan_text'] = $row['kepemilikan_text'];
            $rows['panjang_km'] = $row['panjang_km'];
            $rows['lebar_rata'] = $row['lebar_rata'];
            if ($row['perkerasan'] > 0) {
                $rows['perkerasan'][$row['perkerasan']][] = json_decode($row['koordinat'], true);
            } else {
                $rows['perkerasan'] = [];
            }
            if ($row['kondisi'] > 0) {
                $rows['kondisi'][$row['kondisi']][] = json_decode($row['koordinat'], true);
            } else {
                $rows['kondisi'] = [];
            }
            $data[$row['no_jalan']] = $rows;
            // var_dump($koordinat);
        }

        $n = 0;
        foreach ($data as $no_jalan => $row) {
            $row['row'] = $n + 1;
            $data[$n] = $row;
            unset($data[$no_jalan]);
            $n++;
        }

        $count = count($data);
        // TODO: Echoing data as JSON
        Functions::setDataTable($data, $count, $count);
        exit;
    }
}
