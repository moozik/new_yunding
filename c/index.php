<?php

class c_index extends lib_controlerBase {
    /**
     * 展示主页
     */
    public function actionIndex() {
        m_dao_race::init();
        lib_log::access("index",json_encode($_SERVER));
        SEN::display_page('index', [
            'timeStamp' => strtotime(m_dao_race::$time) + 1627872224,
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