<?php

/**
 * * app/model/Menu_model.php
 */
class Menu_model extends Database
{
    /**
     * * Define variable
     */
    private $my_tables = ['menu' => 'tmenu'];

    /**
     * * Menu_model::getTable
     * ? Get table name
     * @param string $type
     * ? Type
     */
    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    /**
     * * Menu_model::getMenuForm
     * ? Menu form
     */
    public function getMenuForm(int $id = null)
    {

        $menu = $this->getMenuOptions();
        // print '<pre>';
        // print_r($_GET);
        // print_r($menu);
        if (!is_null($id)) unset($menu[$id]);
        // print_r($menu);
        // print '</pre>';
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['text', 'name', 'name', 'Name', [], true, true]);
        Functions::setDataSession('form', ['select', 'parent', 'parent', 'Parent Menu', $menu, false, false]);
        Functions::setDataSession('form', ['text', 'class_name', 'class_name', 'Class Name', [], true, false]);
        Functions::setDataSession('form', ['text', 'method_name', 'method_name', 'Method Name', [], false, false]);
        Functions::setDataSession('form', ['text', 'sort', 'sort', 'Sort', [], true, false]);
        Functions::setDataSession('form', ['switch', 'show_website', 'show_website', 'Show on Website', [], false, false]);
        Functions::setDataSession('form', ['switch', 'show_admin', 'show_admin', 'Show on Admin', [], false, false]);

        return Functions::getDataSession('form');
    }

    public function getMenuOptions()
    {
        list($system) = $this->getMenu();
        $system_options = [];
        foreach ($system as $row) {
            $system_options[$row['id']] = $row['name'];
        }

        return $system_options;
    }

    /**
     * * Menu_model::getMenuThead
     * ? Menu table column list
     */
    public function getMenuThead()
    {
        // TODO: Set column table
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'name', 'Name', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'website', 'Show on Website', 'data-halign="center" data-align="center" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'admin', 'Show on Admin', 'data-halign="center" data-align="center" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    /**
     * * Menu_model::getMenu
     * ? Get data from database
     */
    public function getMenu()
    {
        $params = [];
        $search = Functions::getSearch();

        if (!empty($search['search'])) $params['filter'] = "name LIKE '%{$search['search']}%'";
        if (isset($search['limit'])) $params['limit'] = $search['limit'];
        if (isset($search['offset'])) $params['offset'] = $search['offset'];

        $params['sort'] = "{$this->my_tables['menu']}.sort ASC";

        $query = $this->getSelectQuery($this->my_tables['menu'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    /**
     * * Menu_model::totalMenu
     * ? Get total rows in database
     */
    public function totalMenu()
    {
        return $this->totalRows($this->my_tables['menu']);
    }

    /**
     * * Menu_model::getMenuDetail
     * ? Get menu detail
     * @param int $id
     * ? Menu ID
     */
    public function getMenuDetail(int $id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables['menu'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }

    /**
     * * Menu_model::prepareSaveMenu
     * ? Preparing data to save into database
     */
    public function prepareSaveMenu()
    {
        $values = [];
        $bindVar = [];

        $show = ['show_website', 'show_admin'];

        foreach ($show as $value) {
            if (!isset($_POST[$value])) $_POST[$value] = 0;
        }

        foreach ($_POST as $key => $value) {
            if ($key == 'id') continue;
            if (in_array($key, $show)) {
                $value = ($value === 'on') ? 1 : 0;
            }
            array_push($values, "{$key}=?");
            array_push($bindVar, $value);
        }

        $values = implode(", ", $values);
        $values .= ", login_id = ?, remote_ip = ?";

        array_push($bindVar, Auth::User('id'), $_SERVER['REMOTE_ADDR']);

        return [$values, $bindVar];
    }

    /**
     * * Menu_model::createMenu
     * ? Insert new menu
     */
    public function createMenu()
    {
        list($values, $bindVar) = $this->prepareSaveMenu();

        $query = "INSERT INTO {$this->my_tables['menu']} SET {$values}, update_dt = NOW()";

        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    /**
     * * Menu_model::updateMenu
     * ? Update existing menu
     */
    public function updateMenu()
    {
        list($values, $bindVar) = $this->prepareSaveMenu();
        array_push($bindVar, $_POST['id']);

        $query = "UPDATE {$this->my_tables['menu']} SET {$values}, update_dt = NOW() WHERE id=?";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    /**
     * * Menu_model::deleteMenu
     * ? Remove menu from database
     */
    public function deleteMenu(int $id)
    {
        $query = "DELETE FROM {$this->my_tables['menu']} WHERE id = ?";
        $bindVar = [$id];
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }
}
