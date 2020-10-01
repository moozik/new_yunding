<?php

class c_teamCalc extends lib_controlerBase{
    private $usedHero = [];

    public function init()
    {
        $this->dataObj = new m_data_teamCalc();
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

        $this->dataObj->setInput($this->input);
        
        //获取可用英雄
        // $chessList = $this->canUseChess();
        //可用数量
        // $chessCount = count($chessList);
        
        // var_dump(memory_get_usage()/1024/1024);
        // $ret = [];
        // for($i=0;$i<2000;$i++){
        //     foreach(lib_conf::chess_sort as $chessId){
        //         $ret[] = m_data_Factory::get(lib_def::chess, $chessId);
        //     }
        // }
        // var_dump(memory_get_usage()/1024/1024);
        // var_dump($chessObj1);
        // var_dump($chessObj2);
        // return;
        // $resultCount = lib_tools::m_chose_n($chessCount, $this->input->forCount);
        //die($resultCount);
        //1. 低于 52360的走全遍历模式
        //2. 高于52360的走已有羁绊遍历模式
        // if($resultCount > 52360){
            //todo
            //Permutation 
        // }else{
            // $rangeData = [
                // $this->input->forCount
            // ];
        // }
        // $rangeData = [4];
        // $count = array_fill(0, count($rangeData), 0);
        // print_r($count);
        //保存已使用的棋子
        // $usedHero = [];
        // foreach($rangeData as $key => $loopCount){
            // foreach(lib_tools::choseIterator($this->canUseChess($usedHero), $loopCount) as $item){
                // $count[$key]++;
            // }
            // $usedHero = array_merge($usedHero, $item);
        // }
        // print_r($count);
    }


}