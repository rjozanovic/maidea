<?php

/**
 * Represents a collection of database rows.
 *
 * 1) Extend in /models/{NAME}CollectionModel.php
 * 2) Override getTable() method and getModelName (needs to have a matching model
 *      that uses same table)
 * 3) Initialise using init::collection("{NAME}");
 * 4) Use: add to select clause: setSelectAttributes([{column_name1},...])
 *                where          setFilterAttributes([{column_name1} => {column_value1},...])
 *    load(), save()...
 * 5) after load:
 *      foreach($collection as $model){}
 */

namespace maidea\model;

abstract class modelAbstract implements Iterator
{

    protected $db;
    protected $table;

    protected $data;
    protected $loaded;

    protected $filter;
    protected $select;
    protected $join;
    protected $orderBy;

    private $operators;

    /**
     * db table name
     */
    abstract protected function getTable();

    /**
     * model name
     */
    abstract protected function getModelName();

    /*---------------------------------------------------------------------*/

    function __construct($db){
        $this->db = $db;
        $this->table = $this->getTable();
        $this->data = array();
        $this->loaded = false;
        $this->select = array('*');
        $this->join = array();
        $this->orderBy = null;

        $this->operators = array(
            //key   => sql
            'OR'    => 'OR',
            'AND'   => 'AND',
            '>'     => '>',
            '<'     => '<',
            '>='    => '>=',
            '<='    => '<=',
            '!='     => '!='
        );

    }

    public function __toString() {
        $ret = '';
        foreach($this->data as $k => $v){
            foreach($v as $ik => $iv)
                $ret .= $ik . '=>' . $iv . '<br>';
        }
        return $ret;
    }

    public function getData(){
        return $this->data;
    }

    /*---------------------------------------------------------------------*/

    public function next(){
        $ret = init::model($this->getModelName());
        $ret->setData(next($this->data));
        $ret->flagAsLoaded();
        return $ret;
    }
    public function rewind(){
        reset($this->data);
    }
    public function current(){
        $ret = init::model($this->getModelName());
        $ret->setData(current($this->data));
        $ret->flagAsLoaded();
        return $ret;
    }
    public function valid(){
        return current($this->data);
    }
    public function key(){
        return key($this->data);
    }

    public function count(){
        return count($this->data);
    }

    /*---------------------------------------------------------------------*/

    /**
     * Sets where clause
     * @param array $attributes -associative array attribute_name=>value
     */
    public function setFilterAttributes($attributes){
        $this->filter = $attributes;
    }

    /**
     * Sets select attributes
     * @param array $attributes
     */
    public function setSelectAttributes($attributes){
        $this->select = $attributes;
    }

    /**
     * Sets up a joins
     * @param string $table2
     * @param string $table2key
     * @param string $table1key
     * @param string $type
     */
    public function addJoin($table2, $table2key, $table1key, $type = 'INNER JOIN'){
        $this->join[] = array('type' => $type,
            't2' => $table2,
            't2key' => $table2key,
            't1key' => $table1key
        );
    }

    public function setOrderBy($attribute){
        $this->orderBy = $attribute;
    }

    /*---------------------------------------------------------------------*/

    /**
     * Loads collection from the database
     * @return boolean - true if success
     */
    public function load(){				//TODO try... catch
        if(!$this->loaded){
            $sql = $this->getSelectSql();
            $sth = $this->db->prepare($sql);
            $this->bindParams($sth);
            $sth->execute();
            $rs = $sth->fetchAll(PDO::FETCH_ASSOC);
            if($rs !== null){
                $this->loaded = true;
                $this->data = $rs;
                return true;
            }
        }
    }

    private function bindParams($sth){
        //var_dump($this->filter);
        //string expressions do not get bound to statements
        if(is_array($this->filter)){
            return $this ->_bindParams($sth, $this->filter);
        }
    }

    private function _bindParams($sth, $filter, $parentKey = null){ //TODO test
        if($filter){
            foreach($filter as $fk => $fv){

                if(is_array($fv))
                    $this->_bindParams($sth, $fv, $fk);
                else{
                    $type = PDO::PARAM_STR;
                    if(is_numeric($fv))
                        $type = PDO::PARAM_INT;

                    if(!array_key_exists($fk, $this->operators)){
                        $sth->bindParam(":" . $this->formatBindTarget($fk) , $filter[$fk], $type);
                        //echo 'binding ' . $this->formatBindTarget($fk) . ' = ' . $fv . '<br>';
                    }
                    else{
                        $sth->bindParam(":" . $this->formatBindTarget($parentKey) , $filter[$fk], $type);
                        //echo 'binding ' . $this->formatBindTarget($parentKey) . ' = ' . $fv . '<br>';
                    }
                }

            }
        }
    }

    /*---------------------------------------------------------------------*/

    public function getSelectSql(){
        $cols = implode(', ', $this->select);

        $where = '';
        if($this->filter)
            $where = ' WHERE ' . $this->getConditionSql($this->filter);

        $join = '';
        $t1 = $this->getTable();
        foreach($this->join as $j){
            $type = $j['type'];
            $t2 = $j['t2'];
            $t1key = $j['t1key'];
            $t2key = $j['t2key'];
            $join .= " $type $t2 ON ($t2.$t2key = $t1.$t1key) ";
            $t1 = $t2;
        }

        $orderBy = $this->orderBy ? ' ORDER BY ' . $this->orderBy . ' ' : '';

        $sql = "SELECT $cols FROM " . $this->table . $join . $where . $orderBy . ';';
        return $sql;
    }



    /**
     * There are problems binding statement if placeholders have a "." inside
     * @param string $name
     * @return string
     */
    protected function formatBindTarget($name){
        return str_replace('.', "", $name);
    }

    /**
     * Returns where condition, with some ability to handle logical and comparision opperators... see below
     * "x = 5 AND y > 12" ----> x = 5 AND y > 12  -----> no variable binding here->use only for testing TODO add placeholders (questionmark and named)
     * array('id' => 23, 'name' => 'blah')  ---> ... id = 23 AND name = 'blah'
     * array('id' => ['#gt#' => 23], 'name' => 'blah')        ---> id > 23 AND name = 'blah'
     * array('#OR#' => [name => 'blah', '#AND#' => [id => ['#gt#' => 23], 'title' => 'default'], x => 5)    ---> name = 'blah' OR (id > 23 AND title = 'default') OR x = 5
     *
     * @param {array or string} $data -if string just return if array try to make string
     * @param string $key -key from previous recursive cycle
     * @return string -sql where condition part
     */
    public function getConditionSql($data, $key = 'AND', $parentKey = null){     //TODO test, improve
        $sql = '';

        $mark = $this->operators;

        if(isset($mark[$key]))
            $key = $mark[$key];

        if(is_array($data)){
            foreach($data as $k => $v){
                if($key === 'AND' || $key === 'OR'){
                    $sql .= $this->getConditionSql($v, $k, $key) . " $key ";
                }
                elseif($key === '>' || $key === '<' || $key === '<=' || $key === '>=' || $key === '!='){
                    $sql .= $this->getConditionSql($v, $k, $key) . " $key ";
                }
                else{
                    $sql .= $key . $this->getConditionSql($v, $k, $key);
                }
            }
            if($key === 'AND' || $key === 'OR')
                $sql = '(' . substr($sql, 0, -1 * (strlen($key) + 2)) . ')';    //cut off last and/or
            return $sql;
        }
        else{
            if($key === 'AND')  //string already no conversion needed
                return $data;
            elseif($key === '>' || $key === '<' || $key === '<=' || $key === '>=' || $key === '!=')
                return " $key  :{$this->formatBindTarget($parentKey)}";
            else{
                return $key . " = :{$this->formatBindTarget($key)}";
            }
        }
    }


}