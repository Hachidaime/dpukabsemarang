<?php

/**
 * * app/model/Data_model.php
 */
class Data_model extends Database
{
    public function getDataThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'no_jalan', 'Nomor Ruas Jalan', 'data-halign="center" data-align="center" data-width="150"']);
        Functions::setDataSession('thead', ['0', 'nama_jalan', 'Nama Ruas Jalan', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'kepemilikan', 'Status Kepemilikan', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'panjang', 'Panjang<br>(m)', 'data-halign="center" data-align="left" data-width="120"']);
        Functions::setDataSession('thead', ['0', 'lebar_rata', 'Lebar<br>Rata-Rata (m)', 'data-halign="center" data-align="left" data-width="120"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getAllDataJalan()
    {
        $jalan_model = $this->model('Jalan_model');
        $jalan_table = $jalan_model->getTable('jalan');
        $koordinat_table = $jalan_model->getTable('koordinat');
        $detail_table = $jalan_model->getTable('detail');
        $foto_table = $jalan_model->getTable('foto');
        $panjang_table = $jalan_model->getTable('panjang');
        $jembatan_table = 'tjembatan';

        $data = [];

        $plain = [
            'select' => [
                "{$jalan_table}.no_jalan",
                "{$jalan_table}.nama_jalan",
                "{$jalan_table}.kepemilikan",
                "{$jalan_table}.panjang",
                "{$jalan_table}.lebar_rata",
                "{$koordinat_table}.ori",
                "{$koordinat_table}.segmented",
                "{$panjang_table}.perkerasan",
                "{$panjang_table}.kondisi",
            ],
            'join' => [
                "LEFT JOIN {$koordinat_table} ON {$koordinat_table}.no_jalan = {$jalan_table}.no_jalan",
                "LEFT JOIN {$panjang_table} ON {$panjang_table}.no_jalan = {$jalan_table}.no_jalan"
            ],
            'sort' => [
                "{$jalan_table}.kepemilikan ASC",
                "{$jalan_table}.no_jalan ASC"
            ],
            'filter' => [
                "{$jalan_table}.nama_jalan NOT LIKE '%test%'"
            ]
        ];

        $query = $this->getSelectQuery($jalan_table, Functions::getParams($plain));
        $this->execute($query);
        list($data['jalan'],) = $this->multiarray();

        $detail = [
            'select' => [
                "{$jalan_table}.no_jalan",
                "{$jalan_table}.nama_jalan",
                "{$jalan_table}.kepemilikan",
                "FORMAT({$jalan_table}.panjang/1000, 2) as panjang",
                "{$jalan_table}.lebar_rata",
                "{$detail_table}.no_detail",
                "{$detail_table}.latitude",
                "{$detail_table}.longitude",
                "{$detail_table}.perkerasan",
                "{$detail_table}.kondisi",
                "{$detail_table}.segment",
                "{$detail_table}.koordinat",
                "{$detail_table}.data",
                "{$koordinat_table}.jml_segmented"
            ],
            'join' => [
                "LEFT JOIN {$jalan_table} ON {$jalan_table}.no_jalan = {$detail_table}.no_jalan",
                "LEFT JOIN {$koordinat_table} ON {$koordinat_table}.no_jalan = {$detail_table}.no_jalan"
                // "LEFT JOIN {$foto_table} ON ({$foto_table}.latitude = {$detail_table}.latitude AND {$foto_table}.longitude = {$detail_table}.longitude)"
            ],
            'sort' => [
                "{$detail_table}.no_jalan ASC",
                // "{$detail_table}.no_detail ASC",
                // "{$detail_table}.perkerasan ASC",
                // "{$detail_table}.kondisi ASC",
                "{$detail_table}.segment ASC"
            ],
            'filter' => [
                "{$jalan_table}.nama_jalan NOT LIKE '%test%'"
            ]
        ];
        $query = $this->getSelectQuery($detail_table, Functions::getParams($detail));
        $this->execute($query);
        list($data['detail'],) = $this->multiarray();

        $jembatan = [
            'select' => [
                "{$jalan_table}.no_jalan",
                "{$jalan_table}.nama_jalan",
                "{$jalan_table}.kepemilikan",
                "{$jembatan_table}.no_jembatan",
                "{$jembatan_table}.nama_jembatan",
                "{$jembatan_table}.latitude",
                "{$jembatan_table}.longitude",
                "{$jembatan_table}.lebar",
                "{$jembatan_table}.panjang",
                "{$jembatan_table}.bentang",
                "{$jembatan_table}.keterangan",
                "{$jembatan_table}.tipe_bangunan_atas",
                "{$jembatan_table}.tipe_bangunan_bawah",
                "{$jembatan_table}.tipe_fondasi",
                "{$jembatan_table}.tipe_lantai",
                "{$jembatan_table}.kondisi_bangunan_atas",
                "{$jembatan_table}.kondisi_bangunan_bawah",
                "{$jembatan_table}.kondisi_fondasi",
                "{$jembatan_table}.kondisi_lantai",
                "{$jembatan_table}.foto_bangunan_atas",
                "{$jembatan_table}.foto_bangunan_bawah",
                "{$jembatan_table}.foto_fondasi",
                "{$jembatan_table}.foto_lantai",
            ],
            'join' => [
                "LEFT JOIN {$jalan_table} ON {$jalan_table}.no_jalan = {$jembatan_table}.no_jalan",
            ],
            'sort' => [
                "{$jembatan_table}.no_jalan ASC",
                "{$jembatan_table}.no_jembatan ASC",
            ],
            'filter' => [
                "{$jalan_table}.nama_jalan NOT LIKE '%test%'"
            ]
        ];
        $query = $this->getSelectQuery($jembatan_table, Functions::getParams($jembatan));
        $this->execute($query);
        list($data['jembatan'],) = $this->multiarray();

        return $data;
    }

    /**
     * * Start Generate Data Jalan
     */
    public function generateData()
    {
        // TODO: Remove old data
        array_map('unlink', glob(DOC_ROOT . "/data/*.json"));

        // * Setup from database
        $cond[] = "jenis = 1"; // ? Setup for jalan
        // TODO: Get setup from database
        list($setup_jalan,) = $this->model('Setup_model')->getSetup($cond);

        // TODO: Formating setup as JSON
        list($style, $lineStyle, $iconStyle) = Functions::getStyle($setup_jalan);

        // TODO: Get All Jalan Data
        $list = $this->getAllDataJalan();

        $jalan = Functions::getLineFromJalan($list['jalan'], $lineStyle);
        $jembatan = Functions::getPointFromJembatan($list['jembatan'], $iconStyle);

        list($segment, $complete, $perkerasan, $kondisi, $awal, $akhir) = Functions::getLineFromDetail($list['detail'], $lineStyle, $iconStyle);

        $dd1 = $this->generateDataLaporanDd1($list['jalan']);
        $dd2 = $this->generateDataLaporanDd2($list['jembatan']);

        $info = $this->generateDataInfo($list);

        $data = [
            'lines' => [["Jalan", $jalan], ["Complete", $complete], ["Perkerasan", $perkerasan], ["Kondisi", $kondisi]],
            'points' => [["Segment", $segment], ["Awal", $awal], ["Akhir", $akhir], ["Jembatan", $jembatan]],
            'other' => [["LaporanDD1", $dd1], ["LaporanDD2", $dd2], ["Info", $info]]
        ];
        $this->generateDataFile($data, $style);

        /* 
        // TODO: Save Segment & Jalan with perkerasan kondisi by kepemilikan as JSON
        $kepemilikan_opt = $this->options('kepemilikan_opt'); // TODO: Get Kepemilikan Options
        foreach ($kepemilikan_opt as $kepemilikan => $value) {
            $filename = preg_replace("/[^A-Za-z0-9]/", '', $value);

            $jalan = Functions::getLineFromJalan($list['jalan'], $lineStyle, $kepemilikan);
            $jembatan = Functions::getPointFromJembatan($list['jembatan'], $iconStyle, $kepemilikan);
            list($segment, $complete, $perkerasan, $kondisi, $awal, $akhir) = Functions::getLineFromDetail($list['detail'], $lineStyle, $iconStyle, $kepemilikan);

            $data = [
                'lines' => [[$filename, $jalan], ["{$filename}Complete", $complete], ["{$filename}Perkerasan", $perkerasan], ["{$filename}Kondisi", $kondisi]],
                'points' => [["{$filename}Segment", $segment], ["{$filename}Awal", $awal], ["{$filename}Akhir", $akhir], ["{$filename}Jembatan", $jembatan]]
            ];
            $this->generateDataFile($data, $style);
        }
         */
    }

    public function generateDataLaporanDd1($jalan)
    {
        $laporan = [];
        $field = [];
        foreach ($this->model('Laporan_model')->getDd1Thead()[3] as $row) {
            $row['field'] = ($row['field'] == 'perkerasan_1') ? 'perkerasan_2' : (($row['field'] == 'perkerasan_2') ? 'perkerasan_1' : $row['field']);
            if (!empty($row['field'])) $field[] = $row['field'];
        }

        foreach ($jalan as $idx => $row) {
            $row['row'] = $idx + 1;
            $row['panjang_km'] = number_format($row['panjang'] / 1000, 2);

            foreach (json_decode($row['perkerasan'], true) as $key => $value) {
                $row["perkerasan_{$key}"] = number_format($value / 1000, 2);
            }

            foreach (json_decode($row['kondisi'], true) as $key => $value) {
                $row["kondisi_{$key}"] = number_format($value / 1000, 2);
                $row["kondisi_{$key}_percent"] = number_format($value / $row['panjang'] * 100, 2);
            }

            $laporan[$idx]['kepemilikan'] = $row['kepemilikan'];
            foreach ($field as $value) {
                $laporan[$idx][$value] = $row[$value];
            }
        }

        return $laporan;
    }

    public function generateDataLaporanDd2($jembatan)
    {
        $kondisi_opt = $this->options('kondisi_opt');

        $laporan = [];
        $field = [];
        foreach ($this->model('Laporan_model')->getDd2Thead()[3] as $row) {
            if (!empty($row['field'])) $field[] = $row['field'];
        }

        foreach ($jembatan as $idx => $row) {
            $row['row'] = $idx + 1;
            $laporan[$idx]['kepemilikan'] = $row['kepemilikan'];

            foreach ($row as $key => $value) {
                if (strpos($value, 'kondisi')) {
                    $value = $kondisi_opt[$value];
                }
                $row[$key] = (!empty($value)) ? $value : null;
            }

            foreach ($field as $value) {
                $laporan[$idx][$value] = $row[$value];
            }
        }

        return $laporan;
    }

    public function generateDataInfo($list)
    {
        $kepemilikan_opt = $this->options('kepemilikan_opt'); // TODO: Get Kepemilikan Options

        $panjang = []; // ? in metres
        foreach ($list['jalan'] as $row) {
            $kepemilikan = 'JalanSemua';
            $panjang[$kepemilikan]['total'] += $row['panjang'];
            foreach (json_decode($row['perkerasan'], true) as $key => $value) {
                $panjang[$kepemilikan]['perkerasan'][$key] += $value;
            }
            foreach (json_decode($row['kondisi'], true) as $key => $value) {
                $panjang[$kepemilikan]['kondisi'][$key] += $value;
            }

            $kepemilikan = preg_replace("/[^A-Za-z0-9]/", '', $kepemilikan_opt[$row['kepemilikan']]);
            $panjang[$kepemilikan]['total'] += $row['panjang'];
            foreach (json_decode($row['perkerasan'], true) as $key => $value) {
                $panjang[$kepemilikan]['perkerasan'][$key] += $value;
            }
            foreach (json_decode($row['kondisi'], true) as $key => $value) {
                $panjang[$kepemilikan]['kondisi'][$key] += $value;
            }
        }

        $jembatan = [];
        foreach ($list['jembatan'] as $row) {
            $kepemilikan = 'JalanSemua';
            $jembatan[$kepemilikan]['total'] += 1;

            $kepemilikan = preg_replace("/[^A-Za-z0-9]/", '', $kepemilikan_opt[$row['kepemilikan']]);
            $jembatan[$kepemilikan]['total'] += 1;
        }

        $info['panjang'] = $panjang;
        $info['jembatan'] = $jembatan;
        return $info;
    }

    public function generateDataFile($data, $style)
    {
        foreach ($data as $key => $values) {
            foreach ($values as $row) {
                list($filename, $value) = $row;
                switch ($key) {
                    case 'lines':
                        if (!empty($value)) Functions::saveGeoJSON("{$filename}.json", $style, $value, 1);
                        else Functions::saveJSON("{$filename}.json", $value);
                        break;
                    case 'points':
                        if (!empty($value)) Functions::saveGeoJSON("{$filename}.json", $style, $value, 2);
                        else Functions::saveJSON("{$filename}.json", $value);
                        break;
                    default:
                        Functions::saveJSON("{$filename}.json", $value);
                        break;
                }
            }
        }
    }
}
