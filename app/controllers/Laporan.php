<?php

use Dompdf\Options;

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
            case 'pdf';
                $this->Dd1DownloadPdf();
                break;
            default:
                $this->Dd1Default();
                break;
        }
    }

    private function Dd1DefaultOld()
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

    private function Dd1Default()
    {
        Functions::setTitle("Laporan DD1");
        $searchData = $this->Dd1SearchData();
        // TODO: Menampilkan Table
        $data = [
            'thead' => $this->my_model->getDd1Thead(),
            'data' => $searchData['data'],
            'panjang' => $searchData['panjang']
        ];
        // var_dump($this->Dd1SearchData());
        // var_dump($this->my_model->getDd1Thead());

        $this->view('Laporan/DD1', $data);
    }

    private function Dd1DownloadPdf()
    {
        $searchData = $this->Dd1SearchData();
        // TODO: Menampilkan Table
        $data = [
            'thead' => $this->my_model->getDd1Thead(),
            'data' => $searchData['data'],
            'panjang' => $searchData['panjang'],
            'download' => true,
            'format' => 'pdf'
        ];

        $options = new Options();
        $options->set('defaultFont', 'Serif');
        $options->set('defaultPaperSize', 'A4');
        $options->set('defaultPaperOrientation', 'landscape');

        $content = $this->pdfContent('Laporan/DD1', $data);

        $this->downloadPdf($content, "Laporan-DD1.pdf", $options);
    }

    private function Dd1Search()
    {
        $data = $this->Dd1SearchData();
        echo json_encode($data);
        exit;
    }

    private function Dd1SearchData()
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

        $panjang = [
            'jalan' => 0,
        ];

        $n = [];
        for ($i = 1; $i <= 4; $i++) {
            $n[$i] = 0;
        }
        $panjang = array_merge($panjang, ["perkerasan" => $n]);
        $panjang = array_merge($panjang, ["kondisi" => $n]);
        $panjang = array_merge($panjang, ["kondisi_percent" => $n]);

        foreach ($data as $row) {
            $panjang['jalan'] += $row['panjang_km'];
            foreach ($panjang as $key => $value) {
                foreach ($value as $k => $v) {
                    if ($key != 'kondisi_percent') {
                        $panjang[$key][$k] += number_format($row["{$key}_{$k}"], 2);
                    }
                }
            }
        }

        foreach ($panjang['kondisi_percent'] as $key => $value) {
            $panjang['kondisi_percent'][$key] = number_format($panjang['kondisi'][$key] / $panjang['jalan'] * 100, 2);

            if (in_array($key, [1, 2])) {
                $panjang['mantap'] += $panjang['kondisi_percent'][$key];
            }

            if (in_array($key, [3, 4])) {
                $panjang['tidak_mantap'] += $panjang['kondisi_percent'][$key];
            }
        }

        return [
            'data' => $data,
            'panjang' => $panjang
        ];
    }

    public function dd2(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Laporan_model');

        switch ($param1) {
            case 'search':
                $this->Dd2Search();
                exit;
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
        $data['url'] = BASE_URL . "/Laporan/dd2/search";
        $data['main'][] = $this->dofetch('Layout/Table', $data);
        $this->view('Layout/Default', $data);
    }

    private function Dd2Search()
    {
        $kepemilikan_opt = $this->options('kepemilikan_opt'); // TODO: Get Kepemilikan Options
        $list =  $this->my_model->getLaporanDd2();
        $data = $list;

        $alphabet = range('A', 'Z');

        $field = [];
        foreach ($this->model('Laporan_model')->getDd2Thead()[3] as $row) {
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
}
