<?php

/*
 * Represents a row in a table.
 *
 * 1) Extend in /models/{NAME}Model.php
 * 2) Override getTable() method (also getPkey() if not 'id')
 * 3) Initialise using init::model("{NAME}");
 * 4) Use: load($id), save(), get{COLUMN_NAME}(), set{COLUMN_NAME}($val)
 */

namespace maidea\model;

abstract class modelAbstract
{
    protected $db;
    protected $table;

    protected $data;
    protected $loaded;

    const T_SQL_INT = PDO::PARAM_INT;
    const T_SQL_VARCHAR = PDO::PARAM_STR;
    const T_SQL_DATETIME = PDO::PARAM_STR;

    /**
     * @return string -table name
     */
    abstract protected function getTable();

    /**
     * Table Schema. An array of <column_name> => <pdo_bind_type>
     *
     * Available types defined here as constants prefixed with T_SQL_
     *
     * @return array
     */
    protected function getFieldBindings()
    {
        return array();
    }

    /**
     * @return string -primary key column name
     */
    protected function getPkey()
    {
        return 'id';
    }

    /*---------------------------------------------------------------------*/

    function __construct($db)
    {
        $this->db = $db;
        $this->table = $this->getTable();
        $this->data = array();
        $this->loaded = false;
    }

    /**
     * getter and setter support
     * @param string $name
     * @param array $args
     */
    public function __call($name, $args)
    {
        $getPfix = 'get';
        $setPfix = 'set';
        if (substr($name, 0, strlen($getPfix)) === $getPfix) {        //getter
            $pName = substr($name, -1 * (strlen($name) - strlen($getPfix)));
            $pName = $this->convName($pName);
            return $this->data[$pName];
        }
        if (substr($name, 0, strlen($setPfix)) === $setPfix) {        //setter
            $pName = substr($name, -1 * (strlen($name) - strlen($setPfix)));
            $pName = $this->convName($pName);
            $pk = $this->getPkey();
            if ($pName !== $pk)
                $this->data[$pName] = $args[0];
            return $this;
        }
    }

    public function __toString()
    {
        $ret = '';
        foreach ($this->data as $k => $v) {
            $ret .= $k . '=>' . $v . '<br>';
        }
        return $ret;
    }

    public function getFieldNames()
    {
        return array_keys($this->getFieldBindings());
    }

    /*---------------------------------------------------------------------*/

    /**
     * Loads a row from the database
     * @param int $id - id of row in database
     * @return boolean - true if success
     */
    public function load($id)
    {
        $sql = $this->getSelectSql();
        $sth = $this->db->prepare($sql);
        $this->bindPkey($sth, $id);
        $sth->execute();
        $rs = $sth->fetchAll(PDO::FETCH_ASSOC);
        $row = $rs[0];
        if ($row !== null) {
            $this->loaded = true;
            $this->data = $row;
            return true;
        }
    }

    /**
     * Update or insert row to db
     * @return string -id of the row
     */
    public function save()
    {
        $pkey = $this->getPkey();

        if ($this->loaded !== true) {
            $sql = $this->getInsertSql();
        } else {
            $sql = $this->getUpdateSql();
        }

        $sth = $this->db->prepare($sql);
        $this->bindParams($sth);
        if (!$sth->execute()) {
            die(var_dump($sth->errorInfo()));
        }

        if ($this->isPkeyLoaded())
            $id = $this->getPkeyValue();
        else
            $id = $this->db->lastInsertId();    //TODO - might not work with every DBMS
        return $id;
    }

    /*---------------------------------------------------------------------*/

    private function getSelectSql()
    {
        $pKeyCond = $this->getPkeyConditionString();
        $sql = 'SELECT * from ' . $this->table . " WHERE $pKeyCond;";
        return $sql;
    }

    /**
     * @return string -SQL to insert a new row with. data should be provided in advance ($this->data)
     */
    private function getInsertSql()
    {
        $cols = array_keys($this->data);
        $sql = 'INSERT INTO ' . $this->table;
        $sql = $sql . ' (' . implode(', ', $cols) . ') VALUES (:' . implode(', :', $cols) . ');';
        return $sql;
    }

    /**
     * @return string -SQL to update a new row with.
     */
    private function getUpdateSql()
    {
        $cols = array_keys($this->data);
        $pkey = $this->getPkey();
        $sql = 'UPDATE ' . $this->table . ' SET ';
        foreach ($cols as $col) {
            if ($col !== $pkey)
                $sql .= $col . ' = :' . $col . ',';
        }
        $sql = substr($sql, 0, -1);     //remove last ","
        $pkeyCond = $this->getPkeyConditionString();
        $sql .= " WHERE $pkeyCond";
        return $sql;
    }

    /*---------------------------------------------------------------------*/

    /**
     * @param PDOStatement $sth -reference to a PREPARED PDOStatement object
     */
    private function bindParams($sth)
    {
        $cols = array_keys($this->data);
        foreach ($cols as $col) {
            $sth->bindParam(':' . $col, $this->data[$col]);
        }
    }

    /**
     * @param PDOStatement $sth -reference to a PREPARED PDOStatement object
     */
    private function bindPkey($sth, $val)
    {
        $pk = $this->getPkey();
        $sth->bindParam(':' . $pk, $val);
    }

    /*---------------------------------------------------------------------*/

    /**
     * Used by model collection to set the data pulled from db or gets populated
     * by $this->load($id) also loaded from db. Other uses should be avoided.
     * @param array $data -associative array (COLUMN_NAME=>COLUMN_VALUE)
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * Used by model collection. Will cause update rather than insert statement on save().
     */
    public function flagAsLoaded()
    {
        $this->loaded = true;
    }

    /*---------------------------------------------------------------------*/

    /**
     * @return string -{keyName} = :{keyName} [AND {keyName} = :{keyName}...] select where condition with support for composite pkeys
     */
    private function getPkeyConditionString()
    {
        $pk = $this->getPkey();
        $ret = "$pk = :$pk";
        return $ret;
    }

    /**
     * @return boolean -if primary key value(s) have been already loaded
     */
    private function isPkeyLoaded()
    {
        $pk = $this->getPkey();
        $ret = true;
        if (!isset($this->data[$pk]))
            $ret = false;
        return $ret;
    }

    /**
     * @return string -or array of primary key values if composite pkey
     */
    private function getPkeyValue()
    {
        $pk = $this->getPkey();
        $ret = array();
        $ret = $this->data[$pk];
        return $ret;
    }

    /*---------------------------------------------------------------------*/

    /**
     * Depending on if first character is upper or lover case, converts from
     * camel case to underscore or reverse.
     * @param string $name
     * @return string
     */
    protected function convName($name)
    {
        if (ctype_upper(substr($name, 0, 1))) {
            $name = strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $name));
        } else {
            $name = preg_replace('/(?:^|_)(.?)/e', "strtoupper('$1')", $name);
        }
        return $name;
    }


}