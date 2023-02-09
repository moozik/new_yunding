<?php

class app_c_index extends frame_controlerBase {
    /**
     * 展示主页
     */
    public function actionIndex() {
        lib_log::access("index",json_encode($_SERVER));
        SEN::display_page('index', [
            'timeStamp' => strtotime(app_m_dao_race::$time) + 1675917186,
        ]);
    }

    /**
     * 检查当前json文件版本是否过期
     */
    public function actionCheckVersion() {
        $inputChess = file_get_contents('php://input');
        $inputChessObj = json_decode($inputChess);
        echo "inTime:".$inputChessObj->time."\n";
        if ($this->ifUpdateJson($inputChessObj->time)) {
            //强制更新json
            app_m_dao_base::init(app_m_dao_chess::STATIC_KEY, true);
            app_m_dao_base::init(app_m_dao_equip::STATIC_KEY, true);
            app_m_dao_base::init(app_m_dao_race::STATIC_KEY, true);
            app_m_dao_base::init(app_m_dao_job::STATIC_KEY, true);
            echo 'updateDone.';
        }
        echo 'ok';
    }

    private function ifUpdateJson($timeStr){
        if ($timeStr != app_m_dao_chess::$time){
            return true;
        }
        if ($timeStr != app_m_dao_job::$time){
            return true;
        }
        if ($timeStr != app_m_dao_race::$time){
            return true;
        }
        if ($timeStr != app_m_dao_equip::$time){
            return true;
        }
        return false;
    }
}