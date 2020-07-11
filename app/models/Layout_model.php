<?php
class Layout_model extends Database
{
    public function system()
    {
        $user_group_id = $_SESSION['USER']['user_group_id'];

        $query = "SELECT DISTINCT tsystem.*
            FROM tsystem
            LEFT JOIN tpermission ON tsystem.id = tpermission.system_id
            WHERE tpermission.user_group_id = ?
            ORDER BY tsystem.sort ASC
        ";

        $this->execute($query, array($user_group_id));
        list($list, $count) = $this->multiarray();

        $menu = [];
        if ($count > 0) {
            foreach ($list as $idx => $row) {
                $menu[$row['id']] = $row['name'];
            }
        }

        return $menu;
    }

    public function module()
    {
        $user_group_id = $_SESSION['USER']['user_group_id'];
        $menu_id = $_SESSION['USER']['menu_id'];

        $query = "SELECT
            tmodule.id as module_id,
            tmodule.name as module_name,
            tmodule.class_name as controller,
            taction.id as action_id,
            taction.name as action_name,
            taction.function_name as method
            FROM tmodule
            RIGHT JOIN taction ON taction.module_id = tmodule.id
            LEFT JOIN tpermission ON tpermission.action_id = taction.id
            WHERE tpermission.user_group_id = ?
            AND tpermission.system_id = ?
            ORDER BY tmodule.sort ASC, taction.sort ASC
        ";

        $bindVar = array($user_group_id, $menu_id);
        $this->execute($query, $bindVar);
        list($list, $count) = $this->multiarray();

        $module = [];
        if ($count > 0) {
            foreach ($list as $idx => $row) {
                $module[$row['module_id']]['module_id'] = $row['module_id'];
                $module[$row['module_id']]['module_name'] = $row['module_name'];
                $module[$row['module_id']]['controller'] = $row['controller'];
                $module[$row['module_id']]['action'][$row['action_id']]['action_id'] = $row['action_id'];
                $module[$row['module_id']]['action'][$row['action_id']]['action_name'] = $row['action_name'];
                $module[$row['module_id']]['action'][$row['action_id']]['method'] = $row['method'];
            }
        }
        return $module;
    }

    public function menu()
    {
        $params = [];
        $bindVar = [];
        if (isset($_SESSION['admin']) && isset($_SESSION['USER'])) {
            $params['filter'] = "show_admin = ?";
        } else {
            $params['filter'] = "show_website = ?";
        }
        $bindVar[] = 1;

        $params['sort'] = "tmenu.sort ASC";

        $query = $this->getSelectQuery('tmenu', $params);
        $this->execute($query, $bindVar);
        list($list, $count) = $this->multiarray();

        $menu = [];
        foreach ($list as $row) {
            if (is_null($row['parent'])) $menu[$row['id']] = $row;
            else $menu[$row['parent']]['child'][$row['id']] = $row;
        }

        return $menu;
    }
}
