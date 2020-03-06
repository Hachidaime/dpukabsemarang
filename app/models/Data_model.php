<?php

/**
 * * app/model/Data_model.php
 */
class Data_model extends Database
{
    /**
     * * Define variable
     */
    private $my_tables = ['data' => 'tdata'];

    /**
     * * Data_model::getTable
     * ? Get table name
     * @param string $type
     * ? Type
     */
    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    /**
     * * Data_model::getDataForm
     * ? Data form
     */
    public function getDataForm()
    {
        Functions::setDataSession('form', ['hidden', 'id', 'id', '']);
        Functions::setDataSession('form', ['hidden', 'name', 'name', '']);
        Functions::setDataSession('form', ['plain-text', 'name_text', 'name_text', 'Name']);
        Functions::setDataSession('form', ['textarea', 'description', 'description', 'Description', [], false, false]);
        return Functions::getDataSession('form');
    }

    /**
     * * Data_model::getDataThead
     * ? Data table column list
     */
    public function getDataThead()
    {
        // TODO: Set column table
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'name', 'Name', 'data-halign="center" data-align="left" data-width="100"']);
        Functions::setDataSession('thead', ['0', 'description', 'Description', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'view']);
        Functions::setDataSession('thead', ['0', 'operate']);

        return Functions::getDataSession('thead');
    }

    /**
     * * Data_model::getData
     * ? Get data from database
     */
    public function getData()
    {
        $params = [];
        $search = Functions::getSearch();

        if (!empty($search['search'])) $params['filter'] = "name LIKE '%{$search['search']}%'";
        if (isset($search['limit'])) $params['limit'] = $search['limit'];
        if (isset($search['offset'])) $params['offset'] = $search['offset'];

        $params['sort'] = "{$this->my_tables['data']}.name ASC";

        $query = $this->getSelectQuery($this->my_tables['data'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    /**
     * * Data_model::totalData
     * ? Get total rows in database
     */
    public function totalData()
    {
        return $this->totalRows($this->my_tables['data']);
    }

    /**
     * * Data_model::getDataDetail
     * ? Get Data detail
     * @param int $id
     * ? Data ID
     */
    public function getDataDetail(int $id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables['data'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }

    /**
     * * Data_model::prepareSaveData
     * ? Preparing data to save into database
     */
    public function prepareSaveData()
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

    /**
     * * Data_model::createData
     * ? Insert new Data
     */
    public function createData()
    {
        list($values, $bindVar) = $this->prepareSaveData();

        $query = "INSERT INTO {$this->my_tables['data']} SET {$values}, update_dt = NOW()";

        $this->execute($query, $bindVar);
        // var_dump($this->db);
        return $this->affected_rows();
    }

    /**
     * * Data_model::updateData
     * ? Update existing Data
     */
    public function updateData()
    {
        list($values, $bindVar) = $this->prepareSaveData();
        array_push($bindVar, $_POST['id']);

        $query = "UPDATE {$this->my_tables['data']} SET {$values}, update_dt = NOW() WHERE id=?";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    /**
     * * Data_model::deleteData
     * ? Remove Data from database
     */
    public function deleteData(int $id)
    {
        $query = "DELETE FROM {$this->my_tables['data']} WHERE id = ?";
        $bindVar = [$id];
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function getAllDataJalan()
    {
        $data = [];

        $jalan_model = $this->model('Jalan_model');
        return $data;
    }
}
