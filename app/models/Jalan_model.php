<?php
class Jalan_model extends Database
{
    private $my_tables = ['jalan' => 'tjalan', 'detail' => 'tdetail_jalan', 'koordinat' => 'tkoordinat_jalan', 'foto' => 'tfoto_jalan'];

    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    public function getJalanForm()
    {
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['hidden', 'panjang', 'panjang', '', []]);
        Functions::setDataSession('form', ['text', 'no_jalan', 'no_jalan', 'Nomor Ruas Jalan', [], true, true]);
        Functions::setDataSession('form', ['text', 'nama_jalan', 'nama_jalan', 'Nama Ruas Jalan', [], true, false]);
        Functions::setDataSession('form', ['select', 'kepemilikan', 'kepemilikan', 'Kepemilikan', $this->options('kepemilikan_opt'), true, false]);
        Functions::setDataSession('form', ['plain-text', 'panjang_text', 'pajang_text', 'Panjang (m)']);
        Functions::setDataSession('form', ['number', 'lebar_rata', 'lebar_rata', 'Lebar Rata-Rata (m)', [], true, false]);
        Functions::setDataSession('form', ['video', 'video', 'video', 'Video', [], false, false]);
        Functions::setDataSession('form', ['date', 'video_date', 'video_date', 'Tanggal Video', [], false, false]);
        Functions::setDataSession('form', ['pdf', 'survei', 'survei', 'Dokumen Survei', [], false, false]);
        Functions::setDataSession('form', ['date', 'survei_date', 'survei_date', 'Tanggal Survei', [], false, false]);
        Functions::setDataSession('form', ['kml', 'upload_koordinat', 'upload_koordinat', 'Upload Koordinat', [], false, false, 'Format file yang diperbolehkan KML.']);
        Functions::setDataSession('form', ['number', 'segmentasi', 'segmentasi', 'Segmentasi (m)', [], false, false]);

        return Functions::getDataSession('form');
    }

    public function getJalanThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'no_jalan', 'Nomor Ruas Jalan', 'data-halign="center" data-align="center" data-width="150"']);
        Functions::setDataSession('thead', ['0', 'nama_jalan', 'Nama Ruas Jalan', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'kepemilikan', 'Status Kepemilikan', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getKoordinatThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'latitude', 'Latitude', 'data-halign="center" data-align="center" data-width="100"']);
        Functions::setDataSession('thead', ['0', 'longitude', 'Longitude', 'data-halign="center" data-align="center" data-width="100"']);
        Functions::setDataSession('thead', ['0', 'lebar', 'Lebar (m)', 'data-halign="center" data-align="right" data-width="100"']);
        Functions::setDataSession('thead', ['0', 'perkerasan_text', 'Perkerasan', 'data-halign="center" data-align="left" data-width="150"']);
        Functions::setDataSession('thead', ['0', 'kondisi_text', 'Kondisi', 'data-halign="center" data-align="left" data-width="150"']);
        Functions::setDataSession('thead', ['0', 'foto_file', 'Foto', 'data-halign="center" data-align="left" data-width="100"']);
        Functions::setDataSession('thead', ['0', 'segment', 'Seg', 'data-halign="center" data-align="center" data-width="70"']);
        Functions::setDataSession('thead', ['0', 'iri', 'IRI', 'data-halign="center" data-align="left" data-width="150"']);
        Functions::setDataSession('thead', ['0', 'coord']);

        return Functions::getDataSession('thead');
    }

    public function getKoordinatForm()
    {
        Functions::setDataSession('form', ['hidden', 'index', 'index', '']);
        Functions::setDataSession('form', ['hidden', 'tag', 'tag', '']);
        Functions::setDataSession('form', ['text', 'latitude', 'latitude', 'Latitude', [], true, false]);
        Functions::setDataSession('form', ['text', 'longitude', 'longitude', 'Longitude', [], true, false]);
        Functions::setDataSession('form', ['number', 'lebar', 'lebar', 'Lebar', [], false, false]);
        Functions::setDataSession('form', ['select', 'perkerasan', 'perkerasan', 'Perkerasan', $this->options('perkerasan_opt'), false, false]);
        Functions::setDataSession('form', ['select', 'kondisi', 'kondisi', 'Kondisi', $this->options('kondisi_opt'), false, false]);
        Functions::setDataSession('form', ['number', 'segment', 'segment', 'segment', [], false, false]);
        Functions::setDataSession('form', ['img', 'foto', 'foto', 'Foto', [], false, false]);
        Functions::setDataSession('form', ['text', 'iri', 'iri', 'IRI', [], false, false]);
        return Functions::getDataSession('form');
    }

    public function getJalan(array $cond = [])
    {
        // $params = [];
        $search = Functions::getSearch();
        $filter = [];
        if (!empty($search['search'])) $filter[] = "name_jalan LIKE '%{$search['search']}%'";
        if (isset($search['limit'])) $params['limit'] = $search['limit'];
        if (isset($search['offset'])) $params['offset'] = $search['offset'];

        if (!empty($cond)) {
            foreach ($cond as $value) {
                $filter[] = $value;
            }
        }

        $params['filter'] = implode(' AND ', $filter);

        $params['sort'] = "{$this->my_tables['jalan']}.no_jalan ASC";

        $query = $this->getSelectQuery($this->my_tables['jalan'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    public function totalJalan()
    {
        return $this->totalRows($this->my_tables['jalan']);
    }

    public function prepareSaveJalan()
    {
        $values = [];
        $bindVar = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, ['id', 'upload_koordinat'])) continue;
            $value = (!empty($value)) ? $value : null;
            array_push($values, "{$key}=?");
            array_push($bindVar, $value);
        }
        $values = implode(", ", $values);
        $values .= ", login_id = ?, remote_ip = ?";
        array_push($bindVar, Auth::User('id'), $_SERVER['REMOTE_ADDR']);

        return [$values, $bindVar];
    }

    public function createJalan()
    {
        list($values, $bindVar) = $this->prepareSaveJalan();

        $query = "INSERT INTO {$this->my_tables['jalan']} SET {$values}, update_dt = NOW()";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function updateJalan()
    {
        list($values, $bindVar) = $this->prepareSaveJalan();
        array_push($bindVar, $_POST['id']);

        $query = "UPDATE {$this->my_tables['jalan']} SET {$values}, update_dt = NOW() WHERE id=?";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function getJalanOptions(array $cond = [])
    {
        list($list) = $this->getJalan($cond);
        $options = [];
        foreach ($list as $row) {
            $options[$row['no_jalan']] = "{$row['no_jalan']} - {$row['nama_jalan']}";
        }

        return $options;
    }

    public function getJalanDetail($id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables['jalan'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }

    public function deleteJalan($id)
    {
        $query = "DELETE FROM {$this->my_tables['jalan']} WHERE id = ?";
        $bindVar = [$id];
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    /**
     * * Koordinat Jalan
     */
    public function getKoordinatJalan($no_jalan = null)
    {
        $params = [];

        $params['filter'] = "no_jalan = ?";
        $bindVar = [$no_jalan];

        $query = $this->getSelectQuery($this->my_tables['koordinat'], $params);

        $this->execute($query, $bindVar);

        return $this->singlearray();
    }

    public function makeKoordinatDetail($data)
    {
        // var_dump($data);
        $n = 0;
        foreach ($this->getKoordinatForm() as $row) {
            if (in_array($row['name'], ['index', 'tag'])) continue;
            $data[$n] = (!empty($data[$n]) && $data[$n] != 0) ? $data[$n] : null;
            $detail[$row['name']] = $data[$n];
            $n++;
        }
        // var_dump($detail);
        return $detail;
    }

    public function populateKoordinatDetail($data)
    {
        $count = count($data);

        for ($i = $count; $i < 8; $i++) {
            array_push($data, "");
        }

        return $data;
    }

    public function prepareSaveKoordinatDetail()
    {
        $data = Functions::getDataSession('coordinates', false);

        $awal = [];
        $final = [];
        foreach ($data['final'] as $row) {
            $row = $this->makeKoordinatDetail($row);
            if ($row['segment'] > 0) {
                array_push($final, [$row['latitude'], $row['longitude']]);
            } else {
                array_push($awal, [$row['latitude'], $row['longitude']]);
                array_push($final, [$row['latitude'], $row['longitude']]);
            }
        }

        $values = "no_jalan = ?, koordinat_awal = ?, koordinat_final = ?, update_dt = NOW(), login_id = ?, remote_ip = ?";
        $bindVar = [$_POST['no_jalan'], json_encode($awal), json_encode($final), Auth::User('id'), $_SERVER['REMOTE_ADDR']];

        return [$bindVar, $values];
    }

    public function createKoordinat()
    {
        list($bindVar, $values) = $this->prepareSaveKoordinatDetail();

        $query = "INSERT INTO {$this->my_tables['koordinat']} SET {$values}";

        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function updateKoordinat($id)
    {
        list($bindVar, $values) = $this->prepareSaveKoordinatDetail();
        array_push($bindVar, $id);

        $query = "UPDATE {$this->my_tables['koordinat']} SET {$values} WHERE id = ?";

        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function getDetailJalan(string $no_jalan)
    {
        $params = [];

        $params['filter'] = "no_jalan = ?";
        $bindVar = [$no_jalan];

        $query = $this->getSelectQuery($this->my_tables['detail'], $params);

        $this->execute($query, $bindVar);

        return $this->multiarray();
    }

    public function getOldDetail()
    {
        $query = "SELECT GROUP_CONCAT(id) from {$this->my_tables['detail']} WHERE no_jalan = ?";
        $bindVar = [$_POST['no_jalan']];

        $this->execute($query, $bindVar);
        return $this->field();
    }

    public function clearDetail($old_detail)
    {
        $values = [];
        $bindVar = [];
        foreach (explode(",", $old_detail) as $value) {
            array_push($values, "?");
            array_push($bindVar, $value);
        }
        $values = implode(",", $values);

        $query = "DELETE FROM {$this->my_tables['detail']} WHERE id IN ($values)";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function createDetail()
    {
        $no_jalan = $_POST['no_jalan'];
        $coord = Functions::getDataSession('coordinates', false);
        $koordinat = $coord['final'];

        $rows = [];
        $data = [];
        $foto = [];
        foreach ($koordinat as $idx => $row) {
            $perkerasan[$idx] = 0;
            $kondisi[$idx] = 0;
            $segment[$idx] = 0;

            $rows[$idx] = $this->makeKoordinatDetail($row);
            if ($idx == 0) {
                $perkerasan[$idx] = (!empty($rows[$idx]['perkerasan'])) ? $rows[$idx]['perkerasan'] : '0';
                $kondisi[$idx] = (!empty($rows[$idx]['kondisi'])) ? $rows[$idx]['kondisi'] : '0';
                $segment[$idx] = 0;
            } else {
                $perkerasan[$idx] = (empty($rows[$idx]['perkerasan'])) ? $perkerasan[$idx - 1] : $rows[$idx]['perkerasan'];
                $kondisi[$idx] = (empty($rows[$idx]['kondisi'])) ? $kondisi[$idx - 1] : $rows[$idx]['kondisi'];
                $segment[$idx] = (empty($rows[$idx]['segment'])) ? $segment[$idx - 1] : $rows[$idx]['segment'];
            }

            $data[$perkerasan[$idx]][$kondisi[$idx]][$segment[$idx]][] = $rows[$idx];

            if (!empty(trim($rows[$idx]['foto']))) {
                $foto[$idx + 1]['row'] = $idx + 1;
                $foto[$idx + 1]['no_jalan'] = $no_jalan;
                $foto[$idx + 1]['latitude'] = $rows[$idx]['latitude'];
                $foto[$idx + 1]['longitude'] = $rows[$idx]['longitude'];
                $foto[$idx + 1]['foto'] = $rows[$idx]['foto'];
            }
        }
        $this->clearFoto($no_jalan);
        $this->createFoto($foto);

        $values = [];
        $n = 0;
        $latitude = [];
        $longitude = [];
        $field = ['no_detail', 'no_jalan', 'latitude', 'longitude', 'perkerasan', 'kondisi', 'segment', 'koordinat', 'koordinat_final', 'update_dt', 'login_id', 'remote_ip'];
        foreach ($data as $perkerasan => $x) {
            foreach ($x as $kondisi => $y) {
                foreach ($y as $segment => $z) {
                    $value[$n] = [];
                    $val = [];
                    $c = [];
                    $f = [];
                    foreach ($z as $idx => $row) {
                        array_push($c, [$row['latitude'], $row['longitude']]);
                        $g = [];
                        foreach ($row as $k => $v) {
                            if ($idx > 0 && $k == 'segment') $v = null;
                            array_push($g, $v);
                        }
                        array_push($f, $g);
                    }
                    $latitude[$n] = $z[0]['latitude'];
                    $longitude[$n] = $z[0]['longitude'];
                    // ? no_detail, no_jalan, latitude, longitude, perkerasan, kondisi, segment, koordinat, koordinat_final, update_dt, login_id, remote_ip
                    array_push($val, $n, $no_jalan, $latitude[$n], $longitude[$n], $perkerasan, $kondisi, $segment, $c, $f, "NOW()", Auth::User('id'), $_SERVER['REMOTE_ADDR']);
                    foreach ($field as $k => $v) {
                        $value[$n][$v] = $val[$k];
                    }
                    array_push($values, $value[$n]);
                    $n++;
                }
            }
        }

        foreach ($values as $idx => $row) {
            if (isset($latitude[$idx + 1]) && isset($longitude[$idx + 1])) {
                array_push($row['koordinat'], [$latitude[$idx + 1], $longitude[$idx + 1]]);
            }

            $row['koordinat'] = json_encode($row['koordinat']);
            $row['koordinat_final'] = json_encode($row['koordinat_final']);

            foreach ($row as $key => $value) {
                if ($key == 'update_dt') continue;
                $row[$key] = "'" . $value . "'";
            }

            $values[$idx] = '(' . implode(',', $row) . ')';
        }

        $field = implode(',', $field);
        $values = implode(',', $values);

        $query = "INSERT INTO {$this->my_tables['detail']} ({$field}) VALUES {$values}";
        $this->execute($query);

        return $this->affected_rows();
    }

    public function createFoto(array $foto)
    {
        $field = ['row_id', 'no_jalan', 'latitude', 'longitude', 'foto', 'update_dt', 'login_id', 'remote_ip'];
        $values = [];
        foreach ($foto as $row) {
            $value = [];
            foreach ($row as $val) {
                $value[] = "'{$val}'";
            }
            array_push($value, "NOW()", Auth::User('id'), "'{$_SERVER['REMOTE_ADDR']}'");

            $values[] = '(' . implode(',', $value) . ')';
        }

        $field = implode(',', $field);
        $values = implode(',', $values);

        $query = "INSERT INTO {$this->my_tables['foto']} ({$field}) VALUES {$values}";
        $this->execute($query);
    }

    public function clearFoto(string $no_jalan)
    {
        $query = "DELETE FROM {$this->my_tables['foto']} WHERE no_jalan = ?";
        $bindVar = [$no_jalan];
        $this->execute($query, $bindVar);
    }
}
