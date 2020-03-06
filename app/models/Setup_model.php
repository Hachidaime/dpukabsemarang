<?php

class Setup_model extends Database
{
    private $my_tables = ['setup' => 'tsetup'];

    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    public function getSetupForm()
    {
        $jenis_opt = $this->options('jenis_opt');
        $simbol_opt = $this->options('simbol_opt');
        $kepemilikan_opt = $this->options('kepemilikan_opt');
        $perkerasan_opt = $this->options('perkerasan_opt');
        $kondisi_opt = $this->options('kondisi_opt');

        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['select', 'jenis', 'jenis', 'Jenis', $jenis_opt, true, false]);
        Functions::setDataSession('form', ['select', 'simbol', 'simbol', 'Simbol', $simbol_opt, true, false]);
        Functions::setDataSession('form', ['select', 'kepemilikan', 'kepemilikan', 'Kepemilikan', $kepemilikan_opt, true, false]);
        Functions::setDataSession('form', ['color', 'warna', 'warna', 'Warna', [], true, false]);
        Functions::setDataSession('form', ['range', 'opacity', 'opacity', 'Opacity (%)', [], false, false]);
        Functions::setDataSession('form', ['range10', 'line_width', 'line_width', 'Line Width (px)', [], false, false]);
        Functions::setDataSession('form', ['select', 'perkerasan', 'perkerasan', 'Perkerasan', $perkerasan_opt, false, false]);
        Functions::setDataSession('form', ['select', 'kondisi', 'kondisi', 'kondisi', $kondisi_opt, false, false]);
        Functions::setDataSession('form', ['tag', 'tag', 'tag', 'Tags', [], true, false]);

        return Functions::getDataSession('form');
    }

    public function getSetupThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'jenis', 'Jenis', 'data-halign="center" data-align="left" data-width="150"']);
        Functions::setDataSession('thead', ['0', 'simbol', 'Simbol', 'data-halign="center" data-align="center" data-width="100"']);
        Functions::setDataSession('thead', ['0', 'kepemilikan', 'Kepemilikan', 'data-halign="center" data-align="left" data-width="180"']);
        Functions::setDataSession('thead', ['0', 'perkerasan', 'Perkerasan', 'data-halign="center" data-align="left" data-width="180"']);
        Functions::setDataSession('thead', ['0', 'kondisi', 'Kondisi', 'data-halign="center" data-align="left" data-width="150"']);
        Functions::setDataSession('thead', ['0', 'keterangan', 'Keterangan', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getSetup(array $cond = [])
    {
        $params = [];
        $search = Functions::getSearch();
        $filter = [];
        if (!empty($search['search'])) $filter[] = "tag LIKE '%{$search['search']}%'";
        if (isset($search['limit'])) $params['limit'] = $search['limit'];
        if (isset($search['offset'])) $params['offset'] = $search['offset'];

        if (!empty($cond)) {
            foreach ($cond as $value) {
                $filter[] = $value;
            }
        }

        $params['filter'] = implode(' AND ', $filter);

        $params['sort'] = "{$this->my_tables['setup']}.jenis ASC, {$this->my_tables['setup']}.kepemilikan ASC, {$this->my_tables['setup']}.perkerasan ASC, {$this->my_tables['setup']}.kondisi ASC";

        $query = $this->getSelectQuery($this->my_tables['setup'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    public function totalSetup()
    {
        return $this->totalRows($this->my_tables['setup']);
    }

    public function getSetupDetail($id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables['setup'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }

    public function prepareSaveSetup()
    {
        $values = [];
        $bindVar = [];
        foreach ($_POST as $key => $value) {
            if ($key == 'id') continue;
            if (in_array($key, ['show_website', 'show_admin'])) {
                $value = ($value == 'on') ? 1 : 0;
            }
            array_push($values, "{$key}=?");
            array_push($bindVar, $value);
        }
        $values = implode(", ", $values);

        array_push($bindVar, Auth::User('id'), $_SERVER['REMOTE_ADDR']);

        return [$values, $bindVar];
    }

    public function createSetup()
    {
        list($values, $bindVar) = $this->prepareSaveSetup();

        $query = "INSERT INTO {$this->my_tables['setup']} SET {$values}, update_dt = NOW(), login_id = ?, remote_ip = ?";

        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function updateSetup()
    {
        list($values, $bindVar) = $this->prepareSaveSetup();
        array_push($bindVar, $_POST['id']);

        $query = "UPDATE {$this->my_tables['setup']} SET {$values}, update_dt = NOW(), login_id = ?, remote_ip = ? WHERE id=?";
        $this->execute($query, $bindVar);

        return $this->affected_rows();
    }

    public function deleteSetup($id)
    {
        $query = "DELETE FROM {$this->my_tables['setup']} WHERE id = ?";
        $bindVar = [$id];
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }
}
