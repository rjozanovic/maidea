<?php

namespace maidea\controller;

class controllerAbstract
{

    /*public function __call($name, $args)
    {
        echo $this->getView('error404');
    }*/

    protected function getControllerClassName($name)
    {
        return "\\maidea\\controller\\" . $name . 'Controller';
    }

    protected function getActionFunctionName($name)
    {
        return $name . 'Action';
    }

    /*private function getViewClassName($name){
        return $name . 'View';
    }

    protected function getView($name){
        $class = $this -> getViewClassName($name);
        return new $class();
    }*/

    protected function getRequestParam($name)
    {
        if(isset($_REQUEST[$name]))
            return $_REQUEST[$name];
        else
            return null;
    }


}