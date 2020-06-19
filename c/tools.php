<?php

class c_tools{
    private $route = [
        'chessSort'
    ];
    function __construct()
    {
        m_dao_chess::init();
    }
    public function execute()
    {
        $action = $_GET['a'];
        if(in_array($action, $this->route)){
            $this->{$action}();
        }else{
            header("HTTP/1.1 404 Not Found");
        }
    }

    /**
     * 生成m_conf::hero_sort 的配置
     */
    public function chessSort(){
        $ret = [
            5=>[],
            4=>[],
            3=>[],
            2=>[],
            1=>[],
        ];
        foreach(m_dao_chess::$data as &$chess){
            $ret[$chess->price][] = $chess;
        }
        //展示
        header("Content-type: text/plain; charset=utf-8");
        foreach($ret as $price => $chessList){
            foreach($chessList as $chess){
                echo sprintf("%s,//%s %s %s\n",
                    $chess->chessId, $chess->price, $chess->title, $chess->displayName);
            }
        }
    }
}