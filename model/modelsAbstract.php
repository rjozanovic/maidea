<?php

namespace maidea\model;

/**
 * Class modelsAbstract
 *
 * Represents a collection of rows in table (resultset)
 */
abstract class modelsAbstract implements \iterator
{

    /**
     * @var \PDO $pdo
     */
    protected $pdo;

    private $where = '';
    private $orderBy = '';
    private $limit = 20;
    private $offset = 0;

    protected $bindValues = array();
    protected $bindTypes = array();

    public function __construct()
    {
        $this->pdo = maidea\db::getPdoHandle();
    }

    abstract static protected function getModelName();

    protected $data;

    public function getColumns()        //TODO remove
    {
        /**
         * @var modelAbstract $modelName
         */
        $modelName = $this->getModelName();
        return array_keys($modelName::getSchema());
    }

    private function getTableName()
    {
        /**
         * @var modelAbstract $modelName
         */
        $modelName = $this->getModelName();
        return $modelName::getTableName();
    }

    public function next()
    {
        return $this->createModel(next($this->data));
    }
    public function rewind()
    {
        reset($this->data);
    }
    public function current()
    {
        return $this->createModel(current($this->data));
    }
    public function valid()
    {
        return current($this->data);
    }
    public function key()
    {
        return key($this->data);
    }
    public function count()
    {
        return count($this->data);
    }

    protected function createModel($data)
    {
        $modelName = $this->getModelName();
        $model = new $modelName();
        return $model->setData($data);
    }

    /**
     * @param string $sql - where condition using named placeholders
     * @param array $bindValues - of placeholder => value key value pairs
     * @param array $bindTypes - of placeholder => pdo_bind_constant key value pairs
     */
    public function setWhere($sql, $bindValues, $bindTypes)
    {
        $this->where = $sql;
        $this->bindValues = $bindValues;
        $this->bindTypes = $bindTypes;
    }

    protected function getSelectSql()
    {
        $sql = "SELECT * FROM " . $this->getTableName();
        $sql .= $this->where ? ' WHERE ' . $this->where : '';
        $sql .= $this->orderBy ? ' ORDER BY ' . $this->orderBy : '';
        $sql .= $this->limit ? ' LIMIT ' . $this->limit : '';
        $sql .= ';';
        return $sql;
    }

    protected function bindParams(\PDOStatement $stmt)
    {
        foreach($this->bindValues as $placeholder => $value){
            $stmt->bindValue(':' . $placeholder, $value, $this->bindTypes[$placeholder]);
        }
    }

    public function load()
    {
        $stmt = $this->pdo->prepare($this->getSelectSql());
        $this->bindParams($stmt);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($rs !== null){
            //$this->loaded = true;//TODO
            $this->data = $rs;
        }
    }


    public function getJson()
    {
        return json_encode($this->data);
    }

    public function getData()
    {
        return $this->data;
    }

}