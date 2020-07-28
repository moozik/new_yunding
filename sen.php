<?php
date_default_timezone_set('Asia/Shanghai');

/**
 * 框架主类
 */
class SEN
{
    /**
     * 访问密码
     */
    const PASSWORD = '520';
    /**
     * CDN url
     */
    const CDN_URL = 'https://static.moozik.cn/yunding';
    /**
     * 是否使用cdn加速
     */
    const USE_CDN = false;
    /**
     * 静态文件目录
     */
    const STATIC_DIR = 'static';
    /**
     * 缓存文件目录
     */
    const CACHE_DIR = 'cache';
    /**
     * 日志文件目录
     */
    const LOG_DIR = 'log';

    const NICE_FILE = 'nice.json';
    const ICO_FILE = 'gold.ico';
    /**
     * ip白名单
     */
    const IPLIST = [
        '127.0.0.1'
    ];
    /**
     * page static file
     */
    const STATIC_FILE = [
        'define' => 'define.js',
        'frame' => 'frame.js',
        'css' => 'css.css',

        'chess' => 'chess.js',
        'race' => 'race.js',
        'job' => 'job.js',
        'equip' => 'equip.js',
    ];
    const REMOTE_URL = [
        'chess' => 'http://game.gtimg.cn/images/lol/act/img/tft/js/chess.js',
        'race' => 'http://game.gtimg.cn/images/lol/act/img/tft/js/race.js',
        'job' => 'http://game.gtimg.cn/images/lol/act/img/tft/js/job.js',
        'equip' => 'http://game.gtimg.cn/images/lol/act/img/tft/js/equip.js',
    ];
    const SITE = [
        'title' => '云顶之弈计算器',
        'description' => '只需要选择英雄,点击计算,就可以得到根据阵容和英雄价格计算出来的最优阵容.',
        'keywords' => '云顶之弈,云顶之弈模拟器,云顶之弈计算器,自走棋,lol自走棋,自走棋模拟器'
    ];

    const VIEW_FILE = [
        'index' => 'index.php',
    ];
    public static $rootDir = '';
    public static $siteUrl = '';
    public static $webDir = '';
    static function init()
    {
        //自动加载 _分割
        spl_autoload_register(function($className){
            require_once str_replace('_', DIRECTORY_SEPARATOR, strtolower($className)) . '.php';
        });
        self::$rootDir = realpath('.');
        self::$siteUrl = dirname($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
        self::$webDir = dirname($_SERVER['SCRIPT_NAME']);
        if(!file_exists(self::log_dir())) {
            mkdir(self::log_dir());
        }
        if(!file_exists(self::cache_dir())) {
            mkdir(self::cache_dir());
        }
    }
    // static function getAction(){
    //     $action = substr($_SERVER['REDIRECT_URL'], strlen(self::$webDir));
    //     return trim($action, '/');
    // }
    static function isMe()
    {
        if (in_array(self::getIp(), self::IPLIST) || SEN::PASSWORD == $_COOKIE['passwd']) {
            return true;
        } else {
            return false;
        }
    }

    static function static_url($name)
    {
        if (self::USE_CDN) {
            return implode('/', [self::CDN_URL , self::STATIC_DIR , self::STATIC_FILE[$name]]);
        } else {
            return implode('/', [self::$siteUrl , self::STATIC_DIR , self::STATIC_FILE[$name]]);
        }
    }
    /**
     * 展示视图
     */
    static function display_page($name)
    {
        $res = debug_backtrace();
        preg_match_all("/_([^_]+)$/", $res[1]['class'], $res);
        require_once self::view_path($res[1][0], $name); 
    }
    /**
     * 视图路径
     */
    static function view_path($className, $fileName)
    {
        return implode(DIRECTORY_SEPARATOR, [self::$rootDir , 'v' , $className, self::VIEW_FILE[$fileName]]);
    }
    static function static_path($name)
    {
        return implode(DIRECTORY_SEPARATOR, [self::$rootDir , self::STATIC_DIR , self::STATIC_FILE[$name]]);
    }
    static function cache_dir()
    {
        return implode(DIRECTORY_SEPARATOR, [self::$rootDir , self::CACHE_DIR]);
    }
    static function log_dir()
    {
        return implode(DIRECTORY_SEPARATOR, [self::$rootDir , self::LOG_DIR]);
    }
    static function log_file($logFile)
    {
        //'.log.'.date('Ymd')
        return implode(DIRECTORY_SEPARATOR, [self::log_dir() , $logFile . '.log.' . date('Y_W')]);
    }
    static function nice_file()
    {
        return implode(DIRECTORY_SEPARATOR, [self::$rootDir , self::NICE_FILE]);
    }
    static function ico_url()
    {
        return self::$siteUrl . '/' . self::ICO_FILE;
    }

    static function fatalLog($msg)
    {
        self::Log($msg, self::log_file('fatal'));
    }
    static function traceLog($msg)
    {
        self::Log($msg, self::log_file('trace'));
    }
    static function accessLog($msg)
    {
        self::Log($msg, self::log_file('access'), false);
    }
    /**
     * @param string $msg
     * @param string $file
     * @param boolean $short 是否简写
     * @return void
     */
    static function Log($msg, $file, $short = true)
    {
        $ip = self::getIp();
        if($short){
            error_log(
                sprintf(
                    "%s[%s]\n[%s]\n",
                    date('Y-m-d H:i:s'),
                    $ip,
                    $msg
                ),
                3,
                $file
            );
        }else{
            if (file_exists(iplocation::$filename)){
                $ipLocation = new iplocation();
                $position = $ipLocation->getlocation($ip);
            }
            error_log(
                sprintf(
                    "%s[%s %s%s][%s]\n[%s]\n",
                    date('Y-m-d H:i:s'),
                    $ip,
                    $position['country'],
                    $position['area'],
                    $_SERVER['HTTP_USER_AGENT'],
                    $msg
                ),
                3,
                $file
            );
        }
    }
    static function getIp()
    {
        foreach (array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ) as $key) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    //会过滤掉保留地址和私有地址段的IP，例如 127.0.0.1会被过滤
                    //也可以修改成正则验证IP
                    if ((bool) filter_var(
                        $ip,
                        FILTER_VALIDATE_IP,
                        FILTER_FLAG_IPV4 |
                            FILTER_FLAG_NO_PRIV_RANGE |
                            FILTER_FLAG_NO_RES_RANGE
                    )) {
                        return $ip;
                    }
                }
            }
        }
        return 'IPERR';
    }
    static function encode($arr)
    {
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }
}

