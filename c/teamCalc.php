<?php

class c_teamCalc extends lib_controlerBase {

    /**
     * @var object 入参
     */
    protected $input;

    /**
     * 入参校验
     * @return void
     * @throws Exception
     */
    function inputCheck()  {
        if (empty($this->arrInput['data'])) {
            throw new Exception('param data empty.');
        }
        $this->input = json_decode($this->arrInput['data']);
    }

    /**
     * 主函数
     */
    public function actionIndex() {
        lib_log::trace('actionIndex.calcInput', $this->arrInput['data']);
        if ($this->input->teamCount != -1) {
            //新版本
            $teamData = new m_data_teamCalc();
        } else {
            //老版本
            $teamData = new m_data_teamCalcOld();
        }
        $teamData->setInput($this->input);
        $this->result['data'] = $teamData->calc();
        $this->result['debug'] = $teamData->debugData();
//        lib_log::trace('actionIndex.result', json_encode($this->result));
        echo lib_string::encode($this->result);
    }
}