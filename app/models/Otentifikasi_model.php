<?php

class Otentifikasi_model extends Database
{
    private $my_tables = ['token' => 'ttoken'];

    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    /**
     * @param string token
     */
    public function createToken(string $token)
    {
        $values = ["token=?"];
        $bindVar = [$token];

        $values = implode(", ", $values);

        $query = "INSERT INTO {$this->my_tables['token']} SET {$values}, insert_dt = NOW()";

        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function checkToken(string $token)
    {

        $params = [];
        $params['filter'] = "token=? AND used = 0 AND DATE_SUB(NOW(), INTERVAL ? second) < insert_dt";
        $params['select'] = "COUNT(*)";
        $query = $this->getSelectQuery($this->my_tables['token'], $params);
        $bindVar = [$token, TOKEN_ACTIVE_PERIOD];

        $this->execute($query, $bindVar);
        return $this->field();
    }

    public function useToken(string $token)
    {
        $filter = ["token=?"];
        $bindVar = [$token];

        $filter = implode(" AND ", $filter);

        $query = "UPDATE {$this->my_tables['token']} SET used = 1 WHERE {$filter}";

        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function clearToken()
    {
        $query = "DELETE FROM {$this->my_tables['token']} WHERE used = 0 AND DATE_SUB(NOW(), INTERVAL ? second) > insert_dt";
        $bindVar = [TOKEN_ACTIVE_PERIOD];
        $this->execute($query, $bindVar);
    }
}
