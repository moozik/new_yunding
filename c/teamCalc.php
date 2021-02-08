<?php
class c_teamCalc extends lib_controlerBase{
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
        lib_log::trace('calcInput', $this->arrInput['data']);
        if($this->input->teamCount != -1){
            //新版本
            $this->teamData = new m_data_teamCalc();
        }else{
            //老版本
            $this->teamData = new m_data_teamCalcOld();
        }
        $this->teamData->setInput($this->input);
        $this->result['data'] = $this->teamData->calc();
        echo lib_string::encode($this->result);
    }
}