<?php

class c_index extends lib_controlerBase {
    /**
     * 展示主页
     */
    public function actionIndex() {
        //判断definejs是否存在
        if (!file_exists(SEN::static_path('define'))) {
            $obj = new c_tools();
            $obj->update();
        }
        m_dao_race::init();
        SEN::display_page('index', [
            'timeStamp' => strtotime(m_dao_race::$time),
        ]);
    }

    /**
     * 检查当前json文件版本是否过期
     */
    public function actionCheckVersion() {
        $inputChess = file_get_contents('php://input');
        $inputChessObj = json_decode($inputChess);
        m_dao_chess::init();
        if ($inputChessObj->time != m_dao_chess::$time) {
            c_tools::update();
            echo 'updateDone.';
        }
        echo 'ok';
    }
}