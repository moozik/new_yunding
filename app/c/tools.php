<?php

class app_c_tools extends frame_controlerBase {
    function __construct() {
        parent::__construct();
        if (!IS_MANAGER) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
    }

    public function actionIndex() {
        $action = $_GET['a'] ?? '';
        $action_list = [
            //'log' => '参数日志',
//            'update' => '更新define,json',
            //'check' => '检查cdn同步',
            //'clean' => '清空缓存文件',
        ];

        echo "<a href='" . SITE_URL . "'>返回</a> | ";
        foreach ($action_list as $param => $title) {
            echo "<a href='?a={$param}'>{$title}</a> | ";
        }
        echo "<hr />\n";

        if (array_key_exists($action, $action_list)) {
            $this->{$action}();
            echo 'done.';
        }
    }

    public function actionTest() {
        // echo json_encode(app_m_dao_chess::$data);
        // print_r(new app_m_object_chess('10202_7114'));
        // print_r(app_m_data_Factory::get(usr_def::chess, '10202_7015'));
        // print_r(app_m_dao_job::$GidMap + app_m_dao_race::$GidMap);
        // $obj = new app_m_object_equip(7009);
        // $obj = app_m_data_Factory::get(usr_def::equip, 7009);
        // print_r($obj);
        // var_dump($obj->getGid());
    }
}
