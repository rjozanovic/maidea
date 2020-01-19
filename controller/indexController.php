<?php

namespace maidea\controller;

class indexController extends controllerAbstract
{

    function __construct(){

        $opt = array(
            'controller' => 'frontend',
            'action' => 'main'
        );

        $controller = $this->getRequestParam('controller');
        if($controller)
            $opt['controller'] = $controller;

        $action = $this->getRequestParam('action');
        if($action)
            $opt['action'] = $action;

        $controllerName = $this->getControllerClassName($opt['controller']);
        $actionName = $this->getActionFunctionName($opt['action']);

        $controller = new $controllerName();
        return $controller->$actionName();

    }

}