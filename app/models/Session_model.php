<?php

class Session_model extends Database {
    public function getExistUsername(){
        $username = $_POST['username'];
        $query = "SELECT tuser.*, tuser_group.name as user_group
            FROM tuser
            LEFT JOIN tuser_group ON tuser_group.id = tuser.user_group_id
            WHERE username=?
        ";
        $this->execute($query, array($username));
        return $this->singlearray();
    }

    public function checkPermission($user_group_id, $method){
        $bindVar = array($user_group_id, $method);
        $query = "SELECT COUNT(*) FROM tuser_group
            LEFT JOIN tpermission ON tpermission.user_group_id = tuser_group.id
            LEFT JOIN taction ON taction.id = tpermission.action_id
            WHERE tuser_group.id = ?
            AND taction.function_name = ?
        ";
        $this->execute($query, $bindVar);
        return $this->field();
    }
}
