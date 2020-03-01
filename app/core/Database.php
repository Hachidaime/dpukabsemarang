<?php

/**
 * * app/core/Database.php
 */
class Database extends Controller
{
    /**
     * * Define variable
     */
    protected $db;
    protected $stmt;
    protected $stmt_no_limit;

    /**
     * * Database::__construct
     * ? Constructor function
     */
    public function __construct()
    {
        // * Call Global variable
        global $db;

        // TODO: Set fetch mode as array assosiative
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

        // TODO: Set global method
        $this->db = &$db;
    }

    /**
     * * Database::getSelectQuery
     * ? Build select query
     * @param string $table
     * ? Table name
     * @param array $params
     * ? Parameter array ['select','join','filter','sort','limit','offset']
     */
    protected function getSelectQuery(string $table, array $params = [])
    {
        // TODO: Set variable dari parameter
        $select     = (isset($params['select'])) ? $params['select'] : '*';
        $join       = (isset($params['join'])) ? $params['join'] : '';
        $condition  = (isset($params['filter']) && !empty($params['filter'])) ? "WHERE " . $params['filter'] : '';
        $order      = (isset($params['sort'])) ? "ORDER BY " . $params['sort'] : '';
        $limit      = (isset($params['limit'])) ? "LIMIT " . $params['limit'] : '';
        $offset     = (isset($params['offset'])) ? "OFFSET " . $params['offset'] : '';

        // TODO: Build query
        $query = "SELECT {$select} FROM {$table} {$join} {$condition} {$order} {$limit} {$offset}";

        // TODO: Return query
        return $query;
    }

    /**
     * * Database::execute
     * ? Menjalankan Query
     * @param string $query
     * ? Query yang dijalankan
     * @param array $bindVar
     * ? bind variable
     */
    public function execute(string $query, array $bindVar = [])
    {
        // TODO: Execute query
        $this->stmt = $this->db->execute($query, $bindVar);

        // TODO: Build query WHITOUT limit
        $query_no_limit = strstr($query, "LIMIT", true);
        $query_no_limit = (strlen($query_no_limit) > 0) ? $query_no_limit : $query;

        // TODO: Check Limit
        if (strpos($query_no_limit, 'SELECT') !== false) {
            // TODO: Execute query WITHOUT limit
            $this->stmt_no_limit = $this->db->execute($query_no_limit, $bindVar);
        }
    }

    /**
     * * Database::multiarray
     * ? Get all rows from select query
     */
    public function multiarray()
    {
        return array($this->stmt->getAll(), $this->stmt_no_limit->recordCount());
    }

    /**
     * * Database::singlearray
     * ? Get one row select query
     */
    public function singlearray()
    {
        return array($this->stmt->fetchRow(), $this->stmt_no_limit->recordCount());
    }

    /**
     * * Database::field
     * ? Get one field from select query
     */
    public function field()
    {
        return array_shift(array_values($this->stmt->fetchRow()));
    }

    /**
     * * Database::insert_id
     * ? Get last id from insert query
     */
    public function insert_id()
    {
        return $this->db->insert_Id();
    }

    /**
     * * Database::affected_rows
     * ? Get sum of rows that affected by query 
     */
    public function affected_rows()
    {
        return $this->db->affected_rows();
    }

    /**
     * * Database::totalRows
     * ? Get total rows of table
     * @param string $table
     * ? Table name
     */
    public function totalRows(string $table)
    {
        $query = "SELECT COUNT(*) FROM {$table}";
        $this->execute($query);
        return $this->field();
    }

    /**
     * * Database::checkUnique
     * ? Check unique value from database
     * @param string $table
     * ? Table name
     * @param int $id
     * ? ID 
     * @param string $field
     * ? Field name
     * @param string $value
     * ? Value
     */
    public function checkUnique(string $table, int $id, string $field, string $value)
    {
        $params = [];
        $params['select'] = "COUNT(*)";
        $params['filter'] = "id != ? AND {$field} = ?";
        $query = $this->getSelectQuery($table, $params);
        $bindVar = [$id, $value];

        $this->execute($query, $bindVar);
        return $this->field();
    }
}
