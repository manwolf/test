<?php

class responseMsg
{
    public function __construct()
    {}

    public $code;

    public $msg;

    public $data;

    public $pages;

    public function setMsg($code, $msg, $data, $pages)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
        $this->pages = $pages;
    }

    public function ResponseMsg($code, $msg, $data, $pages, $prefixJS)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
        $this->pages = $pages;
        echo $prefixJS, '(', json_encode($this), ')';
    }
}

    
