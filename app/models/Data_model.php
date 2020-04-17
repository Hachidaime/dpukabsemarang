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

        $data = [];

        $plain = [
            'select' => [
                "{$jalan_table}.no_jalan",
                "{$jalan_table}.nama_jalan",
                "{$jalan_table}.kepemilikan",
                "{$jalan_table}.panjang",
                "{$jalan_table}.lebar_rata",
                "{$koordinat_table}.ori",
                "{$koordinat_table}.segmented"
            ],
            'join' => [
                "LEFT JOIN {$koordinat_table} ON {$koordinat_table}.no_jalan = {$jalan_table}.no_jalan"
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
        // echo $query . "<br>";
        $this->execute($query);
        list($data['detail'],) = $this->multiarray();

        return $data;
    }

    /**
     * * Start Generate Data Jalan
     */
    public function generateData()
    {
        // * Setup from database
        $cond[] = "jenis = 1"; // ? Setup for jalan
        // TODO: Get setup from database
        list($setup_jalan,) = $this->model('Setup_model')->getSetup($cond);

        // TODO: Formating setup as JSON
        list($style, $lineStyle, $iconStyle) = Functions::getStyle($setup_jalan);

        // TODO: Get All Jalan Data
        $list = $this->getAllDataJalan();

        $jalan = Functions::getLineFromJalan($list['jalan'], $lineStyle);

        list($segment, $complete, $perkerasan, $kondisi, $awal, $akhir) = Functions::getLineFromDetail($list['detail'], $lineStyle, $iconStyle);
        $filename = "JalanSemua";

        $laporan = $this->generateDataLaporan($jalan, $perkerasan, $kondisi);

        $this->GenerateDataSave($filename, $style, $jalan, $segment, $complete, $perkerasan, $kondisi, $awal, $akhir, $laporan);

        // TODO: Save Segment & Jalan with perkerasan kondisi by kepemilikan as JSON
        $kepemilikan_opt = $this->options('kepemilikan_opt'); // TODO: Get Kepemilikan Options
        foreach ($kepemilikan_opt as $kepemilikan => $value) {
            $filename = preg_replace("/[^A-Za-z0-9]/", '', $value);

            $jalan = Functions::getLineFromJalan($list['jalan'], $lineStyle, $kepemilikan);
            list($segment, $complete, $perkerasan, $kondisi, $awal, $akhir) = Functions::getLineFromDetail($list['detail'], $lineStyle, $iconStyle, $kepemilikan);
            $this->GenerateDataSave($filename, $style, $jalan, $segment, $complete, $perkerasan, $kondisi, $awal, $akhir);
        }
    }

    public function generateDataLaporan($jalan, $perkerasan, $kondisi)
    {
        $laporan = [];
        $laporan_data = ['kepemilikan', 'no_jalan', 'nama_jalan', 'panjang', 'lebar_rata'];
        foreach ($jalan as $row) {
            $no_jalan = $row['no_jalan'];
            foreach ($row as $key => $value) {
                if (in_array($key, $laporan_data))
                    $laporan[$no_jalan][$key] = $value;
            }
            $laporan[$no_jalan]['perkerasan'] = [];
            $laporan[$no_jalan]['kondisi'] = [];
        }

        foreach ($perkerasan as $row) {
            $laporan[$row['no_jalan']]['perkerasan'][$row['perkerasan']] = $row['koordinat'];
        }

        foreach ($kondisi as $row) {
            $laporan[$row['no_jalan']]['kondisi'][$row['kondisi']] = $row['koordinat'];
        }

        return $laporan;
    }

    public function generateDataSave(string $filename, $style, $jalan = [], $segment = [], $complete = [], $perkerasan = [], $kondisi = [], $awal = [], $akhir = [], $laporan = [])
    {
        // TODO: Save Segment & Jalan with perkerasan kondisi as JSON
        if (!empty($jalan)) Functions::saveGeoJSON("{$filename}.json", $style, $jalan, 1); // TODO: Save Jalan without attribute as GeoJSON
        if (!empty($complete)) Functions::saveGeoJSON("{$filename}Complete.json", $style, $complete, 1);
        if (!empty($perkerasan)) Functions::saveGeoJSON("{$filename}Perkerasan.json", $style, $perkerasan, 1);
        if (!empty($kondisi)) Functions::saveGeoJSON("{$filename}Kondisi.json", $style, $kondisi, 1);
        if (!empty($segment)) Functions::saveGeoJSON("{$filename}Segment.json", $style, $segment, 2);
        if (!empty($awal)) Functions::saveGeoJSON("{$filename}Awal.json", $style, $awal, 2);
        if (!empty($akhir)) Functions::saveGeoJSON("{$filename}Akhir.json", $style, $akhir, 2);
        if (!empty($laporan)) Functions::saveJSON('Laporan.json', $laporan);
    }
}
