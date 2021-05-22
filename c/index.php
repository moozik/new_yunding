<?php

class c_index extends lib_controlerBase{
    /**
     * 展示主页
     */
    public function actionIndex(){
        //判断definejs是否存在
        if(!file_exists(SEN::static_path('define'))){
            $obj = new c_tools();
            $obj->update();
        }
        SEN::display_page('index');
    }

    /**
     * 检查当前json文件版本是否过期
     */
    public function actionCheckVersion(){
        $inputChess = file_get_contents('php://input');
        $inputChessObj = json_decode($inputChess);
        m_dao_chess::init();
        // echo $inputChessObj->time;
        // echo m_dao_chess::$time;
        // return;
        if($inputChessObj->time != m_dao_chess::$time){
            // $obj = new c_tools();
            c_tools::update();
            echo 'updateDone.';
        }
        echo 'ok';
    }
}