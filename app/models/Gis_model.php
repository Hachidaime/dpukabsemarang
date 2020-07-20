<?php
class Gis_model extends Database
{
    public function getGisForm()
    {
        $jalan_opt = $this->model('Jalan_model')->getJalanOptions(["nama_jalan NOT LIKE '%test%'"]);
        $jalan_opt['semua'] = 'Semua';

        // Functions::setDataSession('form', ['select', 'kepemilikan', 'kepemilikan', 'Status Kepemilikan', $this->options('kepemilikan_opt2', true), true, true]);
        Functions::setDataSession('form', ['select', 'no_jalan', 'no_jalan', 'Ruas Jalan', $jalan_opt, true, true]);
        // Functions::setDataSession('form', ['switch', 'jalan_provinsi', 'jalan_provinsi', 'Jalan Provinsi']);
        Functions::setDataSession('form', ['switch', 'perkerasan', 'perkerasan', 'Perkerasan']);
        Functions::setDataSession('form', ['switch', 'kondisi', 'kondisi', 'Kondisi']);
        Functions::setDataSession('form', ['switch', 'segmentasi', 'segmentasi', 'Segmentasi']);
        Functions::setDataSession('form', ['switch', 'awal', 'awal', 'Awal Ruas Jalan']);
        Functions::setDataSession('form', ['switch', 'akhir', 'akhir', 'Akhir Ruas Jalan']);
        Functions::setDataSession('form', ['switch', 'jembatan', 'jembatan', 'Jembatan']);

        return Functions::getDataSession('form');
    }

    public function getDetailJalan($no_jalan)
    {
        $bindVar = [$no_jalan];

        $jalan_model = $this->model('Jalan_model');
        $jalan_table = $jalan_model->getTable('jalan');
        $koordinat_table = $jalan_model->getTable('koordinat');
        $detail_table = $jalan_model->getTable('detail');
        $foto_table = $jalan_model->getTable('foto');
        $panjang_table = $jalan_model->getTable('panjang');
        $jembatan_table = 'tjembatan';

        $filter = "{$jalan_table}.no_jalan = ?";

        $data_query = [
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
            'filter' => [$filter]
        ];

        $query = $this->getSelectQuery($jalan_table, Functions::getParams($data_query));
        $this->execute($query, $bindVar);
        list($jalan,) = $this->singlearray();

        $data_query = [
            'select' => [
                "{$jalan_table}.no_jalan",
                "{$jalan_table}.nama_jalan",
                "{$jalan_table}.kepemilikan",
                "{$jalan_table}.lebar_rata",
                "{$jalan_table}.segmentasi",
                "{$detail_table}.no_detail",
                "{$detail_table}.latitude",
                "{$detail_table}.longitude",
                "{$detail_table}.perkerasan",
                "{$detail_table}.kondisi",
                "{$detail_table}.segment",
                "{$detail_table}.koordinat",
                "{$detail_table}.data"
            ],
            'join' => [
                "LEFT JOIN {$jalan_table} ON {$jalan_table}.no_jalan = {$detail_table}.no_jalan",
            ],
            'sort' => [
                "{$detail_table}.no_jalan ASC",
                // "{$detail_table}.no_detail ASC",
                // "{$detail_table}.perkerasan ASC",
                // "{$detail_table}.kondisi ASC",
                "{$detail_table}.segment ASC"
            ],
            'filter' => [$filter]
        ];
        $query = $this->getSelectQuery($detail_table, Functions::getParams($data_query));
        $this->execute($query, $bindVar);
        list($detail,) = $this->multiarray();

        $data_query = [
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
            'filter' => [$filter]
        ];
        $query = $this->getSelectQuery($jembatan_table, Functions::getParams($data_query));
        $this->execute($query, $bindVar);
        list($jembatan,) = $this->multiarray();

        return [$jalan, $detail, $jembatan];
    }
}
