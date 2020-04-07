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
                "{$koordinat_table}.koordinat_final AS koordinat"
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
}
