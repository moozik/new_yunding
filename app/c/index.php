<?php

class app_c_index extends frame_controlerBase {
    /**
     * 展示主页
     */
    public function actionIndex() {
        app_m_dao_race::init();
        lib_log::access("index",json_encode($_SERVER));
        SEN::display_page('index', [
            'timeStamp' => strtotime(app_m_dao_race::$time) + 1627872224,
        ]);
    }

    /**
     * 检查当前json文件版本是否过期
     */
    public function actionCheckVersion() {
        $inputChess = file_get_contents('php://input');
        $inputChessObj = json_decode($inputChess);
        app_m_dao_chess::init();
        if ($inputChessObj->time != app_m_dao_chess::$time) {
            c_tools::update();
            echo 'updateDone.';
        }
        echo 'ok';
    }
}