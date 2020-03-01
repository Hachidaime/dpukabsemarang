<?php
class System_model extends Database{
    private $system_table = "tsystem";
    
    /* Start System */
    public function getSystemForm(){
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['text', 'name', 'name', 'Name', [], true, true]);
        Functions::setDataSession('form', ['textarea', 'description', 'description', 'Description', [], false, false]);
        Functions::setDataSession('form', ['text', 'sort', 'sort', 'Sort', [], true, false]);

        return Functions::getDataSession('form');
    }

    public function getSystem(){
        $params = [];
        $search = Functions::getSearch();
        if(isset($search['search'])) $params['filter'] = "name LIKE '%{$search['search']}%'";
        if(isset($search['limit'])) $params['limit'] = $search['limit'];
        if(isset($search['offset'])) $params['offset'] = $search['offset'];
        $params['sort'] = "sort ASC";

        $query = $this->getSelectQuery($this->system_table, $params);

        $this->execute($query);
        return $this->multiarray();
    }

    public function totalSystem(){
        return $this->totalRows($this->system_table);
    }

    public function getSystemDetail($id){
        $params = [];
        $params['filter'] = "id = '{$id}'";
        $query = $this->getSelectQuery($this->system_table, $params);

        $this->execute($query);
        return $this->singlearray();
    }

    public function checkUniqueSystem($id, $name){
        $params = [];
        $params['select'] = "COUNT(*)";
        $params['filter'] = "id != '{$id}' AND name = '{$name}'";
        $query = $this->getSelectQuery($this->system_table, $params);
        
        $this->execute($query);
        return $this->field();
    }

    public function createSystem(){
        $values = [];
        $bindVar = [];
        foreach ($_POST as $key => $value) {
            if($key == 'id') continue;
            $values[] = "{$key}=?";
            $bindVar[] = $value;
        }
        
        $values = implode(", ", $values);

        $query = "INSERT INTO {$this->system_table} SET {$values}, update_dt = NOW(), login_id = '" . Auth::User('id') . "', remote_ip = '{$_SERVER['REMOTE_ADDR']}'";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function updateSystem(){
        $values = [];
        $bindVar = [];
        foreach ($_POST as $key => $value) {
            if($key == 'id') continue;
            $values[] = "{$key}=?";
            $bindVar[] = $value;
        }
        $values = implode(", ", $values);
        $bindVar[] = $_POST['id'];
        
        $query = "UPDATE {$this->system_table} SET {$values}, update_dt = NOW(), login_id = '" . Auth::User('id') . "', remote_ip = '{$_SERVER['REMOTE_ADDR']}' WHERE id=?";
        $this->execute($query, $bindVar);

        return $this->affected_rows();
    }

    public function deleteSystem($id){
        $bindVar[] = $id;
        $query = "DELETE FROM {$this->system_table} WHERE id = ?";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function getSystemOptions(){
        list($system, $count) = $this->getSystem();
        $system_options = [];
        foreach ($system as $idx => $row) {
            $system_options[$row['id']] = $row['name'];
        }

        return $system_options;
    }
    /* End System */
}
