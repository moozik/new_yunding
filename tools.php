<?php
require_once 'conf.php';
SEN::init();
class Tools
{
    const PASSWORD = '520';
    function __construct()
    {
        if (isset($_GET['login']) && self::PASSWORD === $_GET['login']) {
            setcookie("passwd", self::PASSWORD, time() + 86400);
        } else {
            if (!SEN::isMe() && (!isset($_COOKIE['passwd']) || self::PASSWORD != $_COOKIE['passwd'])) {
                header("HTTP/1.1 404 Not Found");
                exit;
            }
        }
        $action = [
            'loglog' => '参数日志',
            'loglog2' => '访问日志',
            'update' => '更新define',
            'check' => '检查cdn同步',
            'clean' => '清空缓存文件',
        ];
        echo "<a href='/yunding/'>返回</a> | ";
        foreach ($action as $param => $title) {
            echo "<a href='?action={$param}'>{$title}</a> | ";
        }
        echo "<hr />\n";
        $this->execute($_GET);
    }

    //主流程
    function execute($input)
    {
        $input['action'] = $input['action'] ?? 'none';
        if ('clean' === $input['action']) {
            foreach (scandir(SEN::cache_dir()) as $filePath) {
                if(!in_array($filePath, ['.','..']))
                    unlink(SEN::cache_dir() . DIRECTORY_SEPARATOR . $filePath);
            }
            echo 'clean done.';
        }
        //检查cdn同步情况
        if ('check' === $input['action']) {
            foreach (SEN::STATIC_FILE as $fileName) {
                $localFile = SEN::$rootDir .DIRECTORY_SEPARATOR. SEN::STATIC_DIR .DIRECTORY_SEPARATOR. $fileName;
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
        //查看log
        if ('loglog' === $input['action']) {
            //查看log
            header("Content-type: text/plain; charset=utf-8");
            echo file_get_contents(SEN::log_file(SEN::LOG_FILE));
            exit;
        }
        //查看log
        if ('loglog2' === $input['action']) {
            //查看log
            header("Content-type: text/plain; charset=utf-8");
            echo file_get_contents(SEN::log_file(SEN::LOG_VISIT_FILE));
            exit;
        }
        //更新define.js文件
        if ('update' === $input['action']) {
            HERO::init();
            //更新js
            $fileContent = '/*update time:' . date('YmdHis') . '*/var heroArr=' . self::encode(HERO::heroList()) . ';' .
                'var groupArr=' . self::encode(HERO::groupList()) . ';' .
                'var weaponArr=' . self::encode(HERO::weaponList()) . ';' .
                'var levelArr=' . self::encode(HERO::LEVEL2COST) . ';';

            file_put_contents(SEN::static_path('define'), $fileContent);
            echo 'update done.';
            exit;
        }
    }
    static function encode($arr)
    {
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }
}
new Tools;
