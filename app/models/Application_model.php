<?php

class Application_model extends Database
{
    private $my_tables = ['system' => 'tsystem', 'module' => 'tmodule', 'action' => 'taction'];

    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    /* Start System */
    public function getSystemForm()
    {
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['text', 'name', 'name', 'Name', [], true, true]);
        Functions::setDataSession('form', ['textarea', 'description', 'description', 'Description', [], false, false]);
        Functions::setDataSession('form', ['number', 'sort', 'sort', 'Sort', [], true, false]);

        return Functions::getDataSession('form');
    }

    public function getSystemThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'name', 'Name', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'description', 'Description', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getSystem()
    {
        $params = [];
        $search = Functions::getSearch();

        if (!empty($search['search'])) $params['filter'] = "name LIKE '%{$search['search']}%'";
        if (isset($search['limit'])) $params['limit'] = $search['limit'];
        if (isset($search['offset'])) $params['offset'] = $search['offset'];

        $params['sort'] = "sort ASC";

        $query = $this->getSelectQuery($this->my_tables['system'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    public function getSystemOptions()
    {
        list($system, $count) = $this->getSystem();
        $system_options = [];
        foreach ($system as $idx => $row) {
            $system_options[$row['id']] = $row['name'];
        }

        return $system_options;
    }
    /* End System */

    /* Start Module */
    public function getModuleForm()
    {
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['text', 'name', 'name', 'Name', [], true, true]);
        Functions::setDataSession('form', ['text', 'class_name', 'class_name', 'Class Name', [], true, false]);
        Functions::setDataSession('form', ['textarea', 'description', 'description', 'Description', [], false, false]);
        Functions::setDataSession('form', ['number', 'sort', 'sort', 'Sort', [], true, false]);
        Functions::setDataSession('form', ['select', 'system_id', 'system_id', 'System', $this->getSystemOptions(), true, false]);

        return Functions::getDataSession('form');
    }

    public function getModuleThead()
    {
        Functions::setTitle("Module");

        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'system', 'System', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'name', 'Name', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'description', 'Description', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getModule()
    {
        $params = [];
        $search = Functions::getSearch();

        if (!empty($search['search'])) $params['filter'] = "{$this->my_tables['module']}.name LIKE '%{$search['search']}%'";
        if (!empty($search['limit'])) $params['limit'] = $search['limit'];
        if (!empty($search['offset'])) $params['offset'] = $search['offset'];

        $params['select'] = "{$this->my_tables['module']}.*, {$this->my_tables['system']}.name as system";
        $params['join'] = "LEFT JOIN {$this->my_tables['system']} ON {$this->my_tables['system']}.id = {$this->my_tables['module']}.system_id";
        $params['sort'] = "{$this->my_tables['system']}.sort ASC, {$this->my_tables['module']}.sort ASC";

        $query = $this->getSelectQuery($this->my_tables['module'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    public function getModuleOptions()
    {
        list($module, $count) = $this->getModule();
        foreach ($module as $row) {
            $module_options[$row['id']] = $row['system'] . " -> " . $row['name'];
        }

        return $module_options;
    }
    /* End Module */

    /* Start Action */
    public function getActionForm()
    {
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['text', 'name', 'name', 'Name', [], true, false]);
        Functions::setDataSession('form', ['text', 'function_name', 'function_name', 'Function Name', [], true, false]);
        Functions::setDataSession('form', ['textarea', 'description', 'description', 'Description', [], false, false]);
        Functions::setDataSession('form', ['text', 'sort', 'sort', 'Sort', [], true, false]);
        Functions::setDataSession('form', ['select', 'module_id', 'module_id', 'Module', $this->getModuleOptions(), true, false]);

        return Functions::getDataSession('form');
    }

    public function getActionThead()
    {
        Functions::setTitle("Action");

        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'system', 'System', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'module', 'Module', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'name', 'Name', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'description', 'Description', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getAction()
    {
        $params = [];
        $search = Functions::getSearch();

        if (!empty($search['search'])) $params['filter'] = "{$this->my_tables['action']}.name LIKE '%{$search['search']}%'";
        if (!empty($search['limit'])) $params['limit'] = $search['limit'];
        if (!empty($search['offset'])) $params['offset'] = $search['offset'];

        $params['select'] = "{$this->my_tables['action']}.*, {$this->my_tables['system']}.name as system, {$this->my_tables['system']}.id as system_id, {$this->my_tables['module']}.name as module";
        $params['join'] = "LEFT JOIN {$this->my_tables['module']} ON {$this->my_tables['module']}.id = {$this->my_tables['action']}.module_id LEFT JOIN {$this->my_tables['system']} ON {$this->my_tables['system']}.id = {$this->my_tables['module']}.system_id";
        $params['sort'] = "{$this->my_tables['system']}.sort ASC, {$this->my_tables['module']}.sort ASC, {$this->my_tables['action']}.sort ASC";

        $query = $this->getSelectQuery($this->my_tables['action'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    /* End Action */

    public function totalApplication($type)
    {
        return $this->totalRows($this->my_tables[$type]);
    }

    public function getApplicationDetail($type, $id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables[$type], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }

    public function prepareSaveApplication()
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

    public function createApplication($type)
    {
        list($values, $bindVar) = $this->prepareSaveApplication();

        $query = "INSERT INTO {$this->my_tables[$type]} SET {$values}, update_dt = NOW()";
        $this->execute($query, $bindVar);

        return $this->affected_rows();
    }

    public function updateApplication($type)
    {
        list($values, $bindVar) = $this->prepareSaveApplication();
        array_push($bindVar, $_POST['id']);

        $query = "UPDATE {$this->my_tables[$type]} SET {$values}, update_dt = NOW() WHERE id=?";
        $this->execute($query, $bindVar);

        return $this->affected_rows();
    }

    public function deleteApplication($type, $id)
    {
        $query = "DELETE FROM {$this->my_tables[$type]} WHERE id = ?";
        $bindVar = [$id];
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }
}
