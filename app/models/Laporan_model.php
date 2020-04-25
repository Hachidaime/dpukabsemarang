<?php
class Laporan_model extends Database
{
    public function getTable(string $tag = null)
    {
        return Functions::getTable($this->my_tables, $tag);
    }

    public function getDd1Thead()
    {
        // TODO: Set column table
        Functions::setDataSession('thead', ['0', null, 'No', 'data-halign="center" data-rowspan="3"']);
        Functions::setDataSession('thead', ['0', null, 'No<br>Ruas', 'data-halign="center" data-rowspan="3"']);
        Functions::setDataSession('thead', ['0', null, 'Nama<br>Ruas', 'data-halign="center" data-rowspan="3"']);
        Functions::setDataSession('thead', ['0', null, 'Kecamatan<br>yang<br>dilalui', 'data-halign="center"  data-rowspan="3"']);
        Functions::setDataSession('thead', ['0', null, 'Panjang<br>Ruas<br>(km)', 'data-halign="center" data-rowspan="3"']);
        Functions::setDataSession('thead', ['0', null, 'Lebar<br>(m)', 'data-halign="center" data-rowspan="3"']);
        Functions::setDataSession('thead', ['0', null, 'Panjang Tiap Jenis Permukaan (km)', 'data-halign="center" data-colspan="4"']);
        Functions::setDataSession('thead', ['0', null, 'Panjang Tiap Kondisi (km)', 'data-halign="center" data-colspan="8"']);
        Functions::setDataSession('thead', ['0', null, 'LHR', 'data-halign="center" data-rowspan="3"']);
        Functions::setDataSession('thead', ['0', null, 'Akses<br>ke<br>N/P/K', 'data-halign="center" data-rowspan="3"']);
        Functions::setDataSession('thead', ['0', null, 'Keterangan', 'data-halign="center" data-rowspan="3"']);

        Functions::setDataSession('thead', ['1', null, 'Aspal/<br>Penetrasi/<br>Macadam', 'data-halign="center" data-rowspan="2"']);
        Functions::setDataSession('thead', ['1', null, 'Pekerasan<br>Beton', 'data-halign="center" data-rowspan="2"']);
        Functions::setDataSession('thead', ['1', null, 'Telford/<br>Kerikil', 'data-halign="center" data-rowspan="2"']);
        Functions::setDataSession('thead', ['1', null, 'Tanah/<br>Belum<br>Tembus', 'data-halign="center" data-rowspan="2"']);
        Functions::setDataSession('thead', ['1', null, 'Baik', 'data-halign="center" data-colspan="2"']);
        Functions::setDataSession('thead', ['1', null, 'Sedang', 'data-halign="center" data-colspan="2"']);
        Functions::setDataSession('thead', ['1', null, 'Rusak Ringan', 'data-halign="center" data-colspan="2"']);
        Functions::setDataSession('thead', ['1', null, 'Rusak Berat', 'data-halign="center" data-colspan="2"']);

        Functions::setDataSession('thead', ['2', null, 'km', 'data-halign="center"']);
        Functions::setDataSession('thead', ['2', null, '%', 'data-halign="center"']);
        Functions::setDataSession('thead', ['2', null, 'km', 'data-halign="center"']);
        Functions::setDataSession('thead', ['2', null, '%', 'data-halign="center"']);
        Functions::setDataSession('thead', ['2', null, 'km', 'data-halign="center"']);
        Functions::setDataSession('thead', ['2', null, '%', 'data-halign="center"']);
        Functions::setDataSession('thead', ['2', null, 'km', 'data-halign="center"']);
        Functions::setDataSession('thead', ['2', null, '%', 'data-halign="center"']);

        Functions::setDataSession('thead', ['3', 'row', '1', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'no_jalan', '2', 'data-halign="center" data-align="center"']);
        Functions::setDataSession('thead', ['3', 'nama_jalan', '3', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['3', 'kecamatan', '4', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['3', 'panjang_km', '5', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'lebar_rata', '6', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'perkerasan_2', '7', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'perkerasan_1', '8', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'perkerasan_3', '9', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'perkerasan_4', '10', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'kondisi_1', '11', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'kondisi_1_percent', '12', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'kondisi_2', '13', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'kondisi_2_percent', '14', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'kondisi_3', '15', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'kondisi_3_percent', '16', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'kondisi_4', '17', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'kondisi_4_percent', '18', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'lhr', '19', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'npk', '20', 'data-halign="center" data-align="right"']);
        Functions::setDataSession('thead', ['3', 'keterangan', '21', 'data-halign="center" data-align="left"']);

        return Functions::getDataSession('thead');
    }

    public function getLaporanDd1()
    {
        $filepath = DOC_ROOT . "data/LaporanDD1.json";
        $data = Functions::readJSON($filepath);

        return $data;
    }
}
