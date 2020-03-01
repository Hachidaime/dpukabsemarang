<?php
class User_model extends Database
{
    private $my_tables = ['user' => 'tuser', 'user_group' => 'tuser_group', 'permission' => 'tpermission'];

    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    /* Start User */
    public function getUserForm()
    {
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['text', 'username', 'username', 'Username', [], true, true]);
        Functions::setDataSession('form', ['password', 'password', 'password', 'Password', [], true, false]);
        Functions::setDataSession('form', ['select', 'user_group_id', 'user_group_id', 'User Group', $this->getUserGroupOptions(), true, false]);

        return Functions::getDataSession('form');
    }

    public function getUserThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'usergroup', 'Usergroup', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'username', 'Username', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getUser()
    {
        $params = [];
        $search = Functions::getSearch();

        if (isset($search['search'])) $params['filter'] = "username LIKE '%{$search['search']}%'";
        if (isset($search['limit'])) $params['limit'] = $search['limit'];
        if (isset($search['offset'])) $params['offset'] = $search['offset'];

        $params['select'] = "{$this->my_tables['user']}.*, {$this->my_tables['user_group']}.name as usergroup";
        $params['join'] = "LEFT JOIN {$this->my_tables['user_group']} ON {$this->my_tables['user_group']}.id = {$this->my_tables['user']}.user_group_id";
        $params['sort'] = "{$this->my_tables['user_group']}.name ASC, {$this->my_tables['user']}.username ASC";

        $query = $this->getSelectQuery($this->my_tables['user'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    public function totalUser()
    {
        return $this->totalRows($this->my_tables['user']);
    }

    public function getUserDetail($id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables['user'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }

    public function prepareSaveUser()
    {
        $values = [];
        $bindVar = [];
        foreach ($_POST as $key => $value) {
            if ($key == 'id') continue;
            if ($key == 'password') {
                if ($_POST['id'] > 0 && !empty($value)) {
                    $value = Functions::encrypt($value);
                } else continue;
            }
            array_push($values, "{$key}=?");
            array_push($bindVar, $value);
        }
        $values = implode(", ", $values);
        $values .= ", login_id = ?, remote_ip = ?";

        array_push($bindVar, Auth::User('id'), $_SERVER['REMOTE_ADDR']);

        return [$values, $bindVar];
    }

    public function createUser()
    {
        list($values, $bindVar) = $this->prepareSaveUser();

        $query = "INSERT INTO {$this->my_tables['user']} SET {$values}, update_dt = NOW()";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function updateUser()
    {
        list($values, $bindVar) = $this->prepareSaveUser();
        array_push($bindVar, $_POST['id']);

        $query = "UPDATE {$this->my_tables['user']} SET {$values}, update_dt = NOW() WHERE id=?";
        $this->execute($query, $bindVar);

        return $this->affected_rows();
    }

    public function deleteUser($id)
    {
        $query = "DELETE FROM {$this->my_tables['user']} WHERE id = ?";
        $bindVar = [$id];
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function getUserOptions()
    {
        list($user) = $this->getUser();
        $user_options = [];
        foreach ($user as $row) {
            $user_options[$row['id']] = $row['username'];
        }

        return $user_options;
    }
    /* End User */

    /* Start User Group */
    public function getUserGroupForm()
    {
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['text', 'name', 'name', 'Name', [], true, true]);
        Functions::setDataSession('form', ['textarea', 'description', 'description', 'Description', [], false, false]);
        return Functions::getDataSession('form');
    }

    public function getUserGroupThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'name', 'Name', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'description', 'Description', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getUserGroup()
    {
        $params = [];
        $search = Functions::getSearch();

        if (!empty($search['search'])) $params['filter'] = "{$this->my_tables['user_group']}.name LIKE '%{$search['search']}%'";
        if (!empty($search['limit'])) $params['limit'] = $search['limit'];
        if (!empty($search['offset'])) $params['offset'] = $search['offset'];

        $params['sort'] = "{$this->my_tables['user_group']}.name ASC";

        $query = $this->getSelectQuery($this->my_tables['user_group'], $params);

        $this->execute($query);
        return $this->multiarray();
    }

    public function totalUserGroup()
    {
        return $this->totalRows($this->my_tables['user_group']);
    }

    public function getUserGroupDetail($id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables['user_group'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }

    public function prepareSaveUserGroup()
    {
        $values = [];
        $bindVar = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, ['id', 'action'])) continue;
            array_push($values, "{$key}=?");
            array_push($bindVar, $value);
        }
        $values = implode(", ", $values);

        array_push($bindVar, Auth::User('id'), $_SERVER['REMOTE_ADDR']);

        return [$values, $bindVar];
    }

    public function createUserGroup()
    {
        list($values, $bindVar) = $this->prepareSaveUserGroup();

        $query = "INSERT INTO {$this->my_tables['user_group']} SET {$values}, update_dt = NOW(), login_id = ?, remote_ip = ?";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function updateUserGroup()
    {
        list($values, $bindVar) = $this->prepareSaveUserGroup();
        array_push($bindVar, $_POST['id']);

        $this->createPermission();

        $query = "UPDATE {$this->my_tables['user_group']} SET {$values}, update_dt = NOW(), login_id = ?, remote_ip = ? WHERE id=?";
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function deleteUserGroup($id)
    {
        $this->deletePermission($id);

        $query = "DELETE FROM {$this->my_tables['user_group']} WHERE id = ?";
        $bindVar = [$id];
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function getUserGroupOptions()
    {
        list($list, $count) = $this->getUserGroup();
        $options = [];
        foreach ($list as $idx => $row) {
            $options[$row['id']] = $row['name'];
        }

        return $options;
    }
    /* End User Group */

    /* Start Permission */
    public function getPermission($id)
    {
        $params = [];
        $bindVar = [];

        $params['select'] = "action_id";
        $params['filter'] = "user_group_id=?";

        $query = $this->getSelectQuery($this->my_tables['permission'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->multiarray();
    }

    public function createPermission()
    {
        $this->deletePermission($_POST['id']);
        if (array_key_exists('action', $_POST)) {
            $values = [];
            foreach ($_POST['action'] as $a => $i) {
                foreach ($i as $b => $j) {
                    foreach ($j as $c => $k) {
                        array_push($values, "({$a}, {$b}, {$c}, {$_POST['id']})");
                    }
                }
            }
            $values = implode(",", $values);
            $query = "INSERT INTO {$this->my_tables['permission']} (system_id, module_id, action_id, user_group_id) VALUES {$values}";

            $this->execute($query);
        }
    }

    private function deletePermission($id)
    {
        $query = "DELETE FROM {$this->my_tables['permission']} WHERE user_group_id=?";
        $bindVar = [$id];
        $this->execute($query, $bindVar);
    }
    /* End Permission */
}
