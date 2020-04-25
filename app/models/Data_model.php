<?php

/**
 * * app/model/Data_model.php
 */
class Data_model extends Database
{
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
                "{$foto_table}.row_id",
                "{$foto_table}.foto"
            ],
            'join' => [
                "LEFT JOIN {$jalan_table} ON {$jalan_table}.no_jalan = {$detail_table}.no_jalan",
                "LEFT JOIN {$foto_table} ON ({$foto_table}.latitude = {$detail_table}.latitude AND {$foto_table}.longitude = {$detail_table}.longitude)"
            ],
            'sort' => [
                "{$detail_table}.no_jalan ASC",
                "{$detail_table}.no_detail ASC",
                "{$detail_table}.perkerasan ASC",
                "{$detail_table}.kondisi ASC",
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
        $filename = "JalanSemua";

        $laporan['dd1'] = $this->generateDataLaporanDd1($list['jalan']);
        $laporan['dd2'] = $this->generateDataLaporanDd2($list['jembatan']);

        $this->GenerateDataSave($filename, $style, $jalan, $segment, $complete, $perkerasan, $kondisi, $awal, $akhir, $jembatan,  $laporan);

        // TODO: Save Segment & Jalan with perkerasan kondisi by kepemilikan as JSON
        $kepemilikan_opt = $this->options('kepemilikan_opt'); // TODO: Get Kepemilikan Options
        foreach ($kepemilikan_opt as $kepemilikan => $value) {
            $filename = preg_replace("/[^A-Za-z0-9]/", '', $value);

            $jalan = Functions::getLineFromJalan($list['jalan'], $lineStyle, $kepemilikan);
            $jembatan = Functions::getPointFromJembatan($list['jembatan'], $iconStyle, $kepemilikan);
            list($segment, $complete, $perkerasan, $kondisi, $awal, $akhir) = Functions::getLineFromDetail($list['detail'], $lineStyle, $iconStyle, $kepemilikan);
            $this->GenerateDataSave($filename, $style, $jalan, $segment, $complete, $perkerasan, $kondisi, $awal, $akhir, $jembatan);
        }
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
                $row["perkerasan_{$key}"] = number_format($value, 2);
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

    public function generateDataSave(string $filename, $style, $jalan = [], $segment = [], $complete = [], $perkerasan = [], $kondisi = [], $awal = [], $akhir = [], $jembatan = [], $laporan = [])
    {
        // TODO: Save Segment & Jalan with perkerasan kondisi as JSON
        if (!empty($jalan)) Functions::saveGeoJSON("{$filename}.json", $style, $jalan, 1); // TODO: Save Jalan without attribute as GeoJSON
        if (!empty($complete)) Functions::saveGeoJSON("{$filename}Complete.json", $style, $complete, 1);
        if (!empty($perkerasan)) Functions::saveGeoJSON("{$filename}Perkerasan.json", $style, $perkerasan, 1);
        if (!empty($kondisi)) Functions::saveGeoJSON("{$filename}Kondisi.json", $style, $kondisi, 1);
        if (!empty($segment)) Functions::saveGeoJSON("{$filename}Segment.json", $style, $segment, 2);
        if (!empty($awal)) Functions::saveGeoJSON("{$filename}Awal.json", $style, $awal, 2);
        if (!empty($akhir)) Functions::saveGeoJSON("{$filename}Akhir.json", $style, $akhir, 2);
        if (!empty($jembatan)) Functions::saveGeoJSON("{$filename}Jembatan.json", $style, $jembatan, 2);
        foreach ($laporan as $key => $value) {
            $key = strtoupper($key);
            if (!empty($value)) Functions::saveJSON("Laporan{$key}.json", $value);
        }
    }
}
