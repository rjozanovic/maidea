<?php

namespace maidea\view;

abstract class viewAbstract
{

    //private static includedTemplates

    abstract public function getOutput();

    protected $data = array();

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    protected function getTemplate($name)
    {
        return file_get_contents('templates/' . $name . '.tpl');
    }

    protected function renderPage($content, $headerTpl = 'head', $footerTpl = 'foot')
    {
        $header = $this->getTemplate($headerTpl);
        $footer = $this->getTemplate($footerTpl);
        return $header . $content . $footer;
    }

    protected function renderTemplate($name, $data = array())
    {
        $scriptId = uniqid();
        $tplData = $this->getTemplate($name);
        if(is_array($data))
            $data = json_encode($data);
        $js = "window.maidea_renderRequests = window.maidea_renderRequests || [];";
        $js .= "maidea_renderRequests.push({name: '{$name}', data: {$data}, scriptId: '{$scriptId}'});";

        $ret = "<script id='{$scriptId}'>{$js}</script>";

        if(1)		//TODO static var check already included templates
            $ret .= "<script id='tpl-{$name}' type='x-tmpl-mustache'>{$tplData}</script>";
        return $ret;
    }

    protected function setJsonHeader()
    {
        header('Content-Type: application/json');
    }


}






