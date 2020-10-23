<?php

class c_teamCalc extends lib_controlerBase{
    private $usedHero = [];

    public function init(){
        $this->teamData = new m_data_teamCalc();
    }
    /**
     * 入参校验
     * @return void
     */
    function inputCheck(){
        if(empty($this->arrInput['data'])){
            throw new Exception('param data empty.');
        }
        $this->input = json_decode($this->arrInput['data']);
    }

    /**
     * 主函数
     */
    public function doWork(){
        $this->teamData->setInput($this->input);
        $this->result['data'] = $this->teamData->calc();
    }

    /**
     * 组装返回结构
     */
    // public function display(){
    //     //todo
    //     return;
    // }

}