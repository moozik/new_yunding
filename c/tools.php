<?php

class c_tools{
    // private $route = [
    //     'chessSort'
    // ];
    function __construct()
    {
        if (isset($_GET['login']) && SEN::PASSWORD === $_GET['login']) {
            setcookie("passwd", SEN::PASSWORD, time() + 86400);
        } else {
            if (!IS_MANAGER) {
                header("HTTP/1.1 404 Not Found");
                exit;
            }
        }
        m_dao_chess::init();
    }
    public function execute()
    {
        $action = $_GET['a'];
        $action_list = [
            //'log' => '参数日志',
            'update' => '更新define,json',
            //'check' => '检查cdn同步',
            //'clean' => '清空缓存文件',
        ];

        echo "<a href='" . SITE_URL . "'>返回</a> | ";
        foreach ($action_list as $param => $title) {
            echo "<a href='?a={$param}'>{$title}</a> | ";
        }
        echo "<hr />\n";

        if(array_key_exists($action, $action_list)){
            $this->{$action}();
        }
    }
    public function update(){
        //强制更新json
        m_dao_base::init(m_dao_race::$staticKey, true);
        m_dao_base::init(m_dao_job::$staticKey, true);
        m_dao_race::init();
        m_dao_job::init();
        //更新js
        $fileContent = 
            '/*update time:' . date('YmdHis') . '*/' .
            // 'var heroArr=' . SEN::encode(HERO::heroList()) . ';' .
            // 'var groupArr=' . SEN::encode(HERO::groupList()) . ';' .
            // 'var weaponArr=' . SEN::encode(HERO::weaponList()) . ';' .
            'var levelArr=' . lib_string::encode(lib_conf::LEVEL2COST) . ';' .
            'var GLevel=' . lib_string::encode($this->getGMapLevel()) . ';';

        file_put_contents(SEN::static_path('define'), $fileContent);
        echo 'update done.';
    }
    public function getGMapLevel(){
        $ret = [];
        foreach(lib_conf::races as $Gid => $item){
            $ret[$Gid] = [
                $item[0],
                m_dao_race::$GidMap[$Gid],
            ];
        }
        foreach(lib_conf::jobs as $Gid => $item){
            $ret[$Gid] = [
                $item[0],
                m_dao_job::$GidMap[$Gid],
            ];
        }
        return $ret;
    } 
    public function clean(){
        foreach (scandir(SEN::cache_dir()) as $filePath) {
            if(!in_array($filePath, ['.','..']))
                unlink(SEN::cache_dir() . DIRECTORY_SEPARATOR . $filePath);
        }
        echo 'clean done.';
    }
    public function check(){
        foreach (SEN::STATIC_FILE as $fileName) {
            $localFile = ROOT_DIR .DIRECTORY_SEPARATOR. SEN::STATIC_DIR .DIRECTORY_SEPARATOR. $fileName;
            $cdnUrl = SEN::CDN_URL .DIRECTORY_SEPARATOR. SEN::STATIC_DIR .DIRECTORY_SEPARATOR. $fileName;
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
    /**
     * 生成lib_conf::hero_sort 的配置
     */
    /*public function chessSort(){
        $ret = [
            5 => [],
            4 => [],
            3 => [],
            2 => [],
            1 => [],
        ];
        foreach(m_dao_chess::$data as &$chess){
            $ret[$chess->price][] = $chess;
        }
        //展示
        header("Content-type: text/plain; charset=utf-8");
        foreach($ret as $price => $chessList){
            foreach($chessList as $chess){
                echo sprintf("%s,//%s %s %s\n",
                    $chess->chessId, $chess->price, $chess->title, $chess->displayName);
            }
        }
    }*/
}
