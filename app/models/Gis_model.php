<?php
class Gis_model extends Database
{
    public function getGisForm()
    {
        Functions::setDataSession('form', ['select', 'kepemilikan', 'kepemilikan', 'Status Kepemilikan', $this->options('kepemilikan_opt2', true), true, true]);
        Functions::setDataSession('form', ['select', 'no_jalan', 'no_jalan', 'Ruas Jalan', [], true, true]);
        Functions::setDataSession('form', ['switch', 'jalan_provinsi', 'jalan_provinsi', 'Jalan Provinsi']);
        Functions::setDataSession('form', ['switch', 'perkerasan', 'perkerasan', 'Perkerasan']);
        Functions::setDataSession('form', ['switch', 'kondisi', 'kondisi', 'Kondisi']);
        Functions::setDataSession('form', ['switch', 'segmentasi', 'segmentasi', 'Segmentasi']);
        Functions::setDataSession('form', ['switch', 'awal', 'awal', 'Awal Ruas Jalan']);
        Functions::setDataSession('form', ['switch', 'jembatan', 'jembatan', 'Jembatan']);

        return Functions::getDataSession('form');
    }

    public function getJalanOptions()
    {
    }
}
