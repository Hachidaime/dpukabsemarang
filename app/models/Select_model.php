<?php
class Select_model extends Database
{
    private $my_tables = ['select' => 'tselect'];

    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    /* Start Select */
    public function getSelectForm()
    {
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['text', 'code', 'code', 'Code', [], true, true]);
        Functions::setDataSession('form', ['text', 'name', 'name', 'Name', [], true, false]);
        Functions::setDataSession('form', ['textarea', 'description', 'description', 'Description', [], false, false]);
        Functions::setDataSession('form', ['textarea', 'options', 'options', 'Options', [], true, false, 'ex: value,label,display(show/hide)<br>Separate each options with ENTER.']);

        return Functions::getDataSession('form');
    }

    public function getSelectThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'code', 'Code', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'name', 'Name', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'description', 'Description', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'options', 'Options', 'data-halign="center" data-align="left" data-width="250"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getSelect()
    {
        $params = [];
        $search = Functions::getSearch();

        if (isset($search['search'])) $params['filter'] = "name LIKE '%{$search['search']}%'";
        if (isset($search['limit'])) $params['limit'] = $search['limit'];
        if (isset($search['offset'])) $params['offset'] = $search['offset'];

        $params['sort'] = "{$this->my_tables['select']}.name ASC";

        $query = $this->getSelectQuery($this->my_tables['select'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    public function totalSelect()
    {
        return $this->totalRows($this->my_tables['select']);
    }

    public function getSelectDetail($id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables['select'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }

    private function prepareSaveSelect()
    {
        $values = [];
        $bindVar = [];

        foreach ($_POST as $key => $value) {
            if ($key == 'id') continue;
            array_push($values, "{$key}=?");
            array_push($bindVar, $value);
        }

        $values = implode(", ", $values);
        $values .= ", login_id = ?, remote_ip = ?";

        array_push($bindVar, Auth::User('id'), $_SERVER['REMOTE_ADDR']);

        return [$values, $bindVar];
    }

    public function createSelect()
    {
        list($values, $bindVar) = $this->prepareSaveSelect();

        $query = "INSERT INTO {$this->my_tables['select']} SET {$values}, update_dt = NOW()";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function updateSelect()
    {
        list($values, $bindVar) = $this->prepareSaveSelect();
        array_push($bindVar, $_POST['id']);

        $query = "UPDATE {$this->my_tables['select']} SET {$values}, update_dt = NOW() WHERE id=?";
        $this->execute($query, $bindVar);

        return $this->affected_rows();
    }

    public function deleteSelect($id)
    {
        $query = "DELETE FROM {$this->my_tables['select']} WHERE id = ?";
        $bindVar = [$id];
        $rs = $this->execute($query, $bindVar);
        return $rs;
    }

    public function getOptions($select_code)
    {
        $query = "SELECT options FROM {$this->my_tables['select']} WHERE code=?";
        $bindVar = [$select_code];

        $this->execute($query, $bindVar);
        $select_options = $this->field();

        return $select_options;
    }
    /* End Select */
}
