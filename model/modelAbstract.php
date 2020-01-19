<?php

/**
 * Class modelAbstract
 *
 * Represents a row in table
 *
 *
 *
 */

namespace maidea\model;

abstract class modelAbstract
{
    protected $pdo = null;
    protected $data = null;

    abstract static public function getTableName();

    abstract static public function getPkName();

    abstract static public function getSchema();

    public function __construct()
    {
        $this->pdo = \maidea\db::getPdoHandle();
    }

    /**
     * getter and setter support
     * @param string $name
     * @param array $args
     */
    public function __call($name, $args){
        $getPfix = 'get';
        $setPfix = 'set';
        if(substr($name, 0, strlen($getPfix)) === $getPfix){        //getter
            $pName = substr($name, -1 * (strlen($name) - strlen($getPfix)));
            $pName = $this->convName($pName);
            return $this->data[$pName];
        }
        if(substr($name, 0, strlen($setPfix)) === $setPfix){        //setter
            $pName = substr($name, -1 * (strlen($name) - strlen($setPfix)));
            $pName = $this->convName($pName);
            $pk = $this->getPkName();
            if($pName !== $pk)
                $this->data[$pName] = $args[0];
            return $this;
        }
    }

    public function loadByPk($pkValue)
    {
        try {
            $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ' . $this->getPkName() . ' =  :pkValue';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pkValue', $pkValue, $this->getFieldBindType($this->getPkName()));
            $stmt->execute();
            $this->data = $stmt->fetchAll(\PDO::FETCH_ASSOC)[0];
        } catch (Exception $e) {
            die('err'); //TODO
        }
        return $this;
    }

    protected function getFieldBindType($fieldName)
    {
        return $this->getSchema()[$fieldName];
    }

    public function setData($data)
    {
        //TODO check vs schema
        //TODO disable setting primary key
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function save()
    {
        try {
            if (array_key_exists($this->getPkName(), $this->data))
                $sql = $this->getUpdateSql();
            else
                $sql = $this->getInsertSql();
            $stmt = $this->pdo->prepare($sql);
            $this->bindParams($stmt);
            $stmt->execute();
        } catch (Exception $e) {
            die('err'); //TODO
        }
    }

    private function getUpdateSql()
    {
        $cols = array_keys($this->data);
        $pkey = $this->getPkName();
        $sql = 'UPDATE ' . $this->getTableName() . ' SET ';
        $setData = array();
        foreach ($cols as $col) {
            if ($col !== $pkey)
                $setData[] = "{$col} = :{$col}";
        }
        $sql .= implode(',', $setData) . " WHERE {$pkey} = :{$pkey}";
        return $sql;
    }

    private function getInsertSql()
    {
        $cols = array_keys($this->data);
        $sql = 'INSERT INTO ' . $this->getTableName();
        $sql = $sql . ' (' . implode(', ', $cols) . ') VALUES (:' . implode(', :', $cols) . ');';
        return $sql;
    }

    private function bindParams($stmt)
    {
        $cols = array_keys($this->data);
        foreach ($cols as $col)
            $stmt->bindValue(':' . $col, $this->data[$col], $this->getFieldBindType($col));
    }

    public function getJson()
    {
        return json_encode($this->data);
    }

    public function getFieldNames()
    {
        return array_keys($this->getSchema());
    }

    /**
     * Depending on if first character is upper or lover case, converts from
     * camel case to underscore or reverse.
     * @param string $name
     * @return string
     */
    protected function convName($name){
        if(ctype_upper(substr($name, 0, 1))){
            $name = strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $name));
        }
        else{
            $name = preg_replace('/(?:^|_)(.?)/e',"strtoupper('$1')", $name);
        }
        return $name;
    }

}