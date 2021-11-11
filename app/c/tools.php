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

    public function getGMapLevel() {
        $ret = [];
        foreach (usr_def::races as $Gid => $item) {
            $ret[$Gid] = [
                $item[0],
                app_m_dao_race::$GidMap[$Gid],
            ];
        }
        foreach (usr_def::jobs as $Gid => $item) {
            $ret[$Gid] = [
                $item[0],
                app_m_dao_job::$GidMap[$Gid],
            ];
        }
        return $ret;
    }

    public function clean() {
        foreach (scandir(SEN::cache_dir()) as $filePath) {
            if (!in_array($filePath, ['.', '..'])) {
                unlink(SEN::cache_dir() . DIRECTORY_SEPARATOR . $filePath);
            }
        }
        echo 'clean done.';
    }

    public function check() {
        foreach (SEN::STATIC_FILE as $fileName) {
            $localFile = ROOT_DIR . DIRECTORY_SEPARATOR . SEN::STATIC_DIR . DIRECTORY_SEPARATOR . $fileName;
            $cdnUrl = SEN::CDN_URL . DIRECTORY_SEPARATOR . SEN::STATIC_DIR . DIRECTORY_SEPARATOR . $fileName;
            $localData = file_get_contents($localFile);
            $cdnData = file_get_contents($cdnUrl);
            if ($localData === $cdnData) {
                echo $cdnUrl . ' 一致<br>';
            } else {
                echo $cdnUrl . ' <span style="color:red;">不一致</span><br>';
            }
        }
    }
    // public function log(){
    //     //查看log
    //     header("Content-type: text/plain; charset=utf-8");
    //     echo file_get_contents(SEN::log_file($_GET['f']));
    // }
}
