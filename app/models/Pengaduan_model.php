<?php
class Pengaduan_model extends Database
{
    private $my_tables = ['pengaduan' => 'tpengaduan'];

    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    public function getPengaduanForm()
    {
        Functions::setDataSession('form', ['text', 'nama', 'nama', 'Nama', [], true, false]);
        Functions::setDataSession('form', ['textarea', 'alamat', 'alamat', 'Alamat', [], true, false]);
        Functions::setDataSession('form', ['select', 'jenis', 'jenis', 'Jenis', $this->options('jenis_opt'), true, false]);
        Functions::setDataSession('form', ['select', 'no_jalan', 'no_jalan', 'Ruas Jalan', $this->model('Jalan_model')->getJalanOptions(), true, false]);
        Functions::setDataSession('form', ['textarea', 'keterangan', 'keterangan', 'Keterangan Pengaduan', [], true, false]);
        Functions::setDataSession('form', ['img', 'foto1', 'foto1', 'Foto 1', [], true, false]);
        Functions::setDataSession('form', ['img', 'foto2', 'foto2', 'Foto 2', [], false, false]);
        Functions::setDataSession('form', ['img', 'foto3', 'foto3', 'Foto 3', [], false, false]);
        Functions::setDataSession('form', ['text', 'jarak', 'jarak', 'Jarak (km)', [], true, false, 'Jarak lokasi dari ujung/awal ruas jalan.']);
        Functions::setDataSession('form', ['switch', 'on_site', 'on_site', 'Apakah saat ini Anda berada di lokasi pengaduan?', [], false, false]);
        Functions::setDataSession('form', ['token', 'my_token', 'my_token', 'Kode Keamanan']);
        Functions::setDataSession('form', ['text', 'token', 'token', 'Token', [], true, true]);

        return Functions::getDataSession('form');
    }

    public function getPengaduanViewForm()
    {
        Functions::setDataSession('form', ['plain-text', 'nama', 'nama', 'Nama']);
        Functions::setDataSession('form', ['plain-textarea', 'alamat', 'alamat', 'Alamat']);
        Functions::setDataSession('form', ['plain-text', 'jenis', 'jenis', 'Jenis']);
        Functions::setDataSession('form', ['plain-text', 'nama_jalan', 'nama_jalan', 'Ruas Jalan']);
        Functions::setDataSession('form', ['plain-textarea', 'keterangan', 'keterangan', 'Keterangan Pengaduan']);
        Functions::setDataSession('form', ['plain-img', 'foto1', 'foto1', 'Foto 1']);
        Functions::setDataSession('form', ['plain-img', 'foto2', 'foto2', 'Foto 2']);
        Functions::setDataSession('form', ['plain-img', 'foto3', 'foto3', 'Foto 3']);
        Functions::setDataSession('form', ['plain-text', 'jarak', 'jarak', 'Jarak lokasi dari ujung/awal ruas jalan (km)']);
        Functions::setDataSession('form', ['plain-text', 'koordinat', 'koordinat', 'Koordinat']);

        return Functions::getDataSession('form');
    }

    public function getPengaduanThead()
    {
        // TODO: Set column table
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'tanggal', 'Tanggal', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'nama', 'Nama', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'alamat', 'Alamat', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'view']);
        return Functions::getDataSession('thead');
    }

    public function getPengaduan()
    {
        $params = [];
        $search = Functions::getSearch();
        if (!empty($search['search'])) $params['filter'] = "nama LIKE '%{$search['search']}%'";
        if (isset($search['limit'])) $params['limit'] = $search['limit'];
        if (isset($search['offset'])) $params['offset'] = $search['offset'];

        $params['sort'] = "{$this->my_tables['pengaduan']}.insert_dt DESC";

        $query = $this->getSelectQuery($this->my_tables['pengaduan'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    public function totalPengaduan()
    {
        return $this->totalRows($this->my_tables['pengaduan']);
    }

    public function createPengaduan()
    {
        $values = [];
        $bindVar = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, ['id', 'my_token'])) continue;

            if (in_array($key, ['on_site'])) {
                $value = ($value == 'on') ? 1 : 0;
            }
            array_push($values, "{$key}=?");
            array_push($bindVar, $value);
        }

        $remote_ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_POST['on_site'])) {
            $remote_ip = ($remote_ip != '127.0.0.1') ? $remote_ip : "36.72.219.132";

            $geo = Functions::getGeo($remote_ip);
            if ($geo['geoplugin_status'] == '200') {
                array_push($values, "latitude=?", "longitude=?");
                array_push($bindVar, $geo['geoplugin_latitude'], $geo['geoplugin_longitude']);
            }
        }
        $values = implode(", ", $values);
        $values .= ", insert_dt = NOW(), remote_ip = ?";

        array_push($bindVar, $remote_ip);

        $query = "INSERT INTO {$this->my_tables['pengaduan']} SET {$values}";

        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function getPengaduanDetail($id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables['pengaduan'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }
}
