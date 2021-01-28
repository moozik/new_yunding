<?php
class c_teamCalc extends lib_controlerBase{
    public function init(){
        // $this->teamData = new m_data_teamCalc();
        $this->teamData = new m_data_teamCalcOld();
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
    public function actionIndex(){
        $this->teamData->setInput($this->input);
        $this->result['data'] = $this->teamData->calc();
        echo lib_string::encode($this->result);
    }
}