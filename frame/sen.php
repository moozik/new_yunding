<?php
date_default_timezone_set('Asia/Shanghai');

/**
 * 框架主类
 */
class SEN {
    /**
     * 访问密码
     */
    const PASSWORD = '520';
    /**
     * 静态文件目录
     */
    const STATIC_DIR = 'static';
    /**
     * 数据目录
     */
    const DATA_DIR = 'data';
    /**
     * 日志文件目录
     */
    const LOG_DIR = 'log';
    /**
     * 图标文件
     */
    const ICO_FILE = 'gold.ico';
    /**
     * page static file
     */
    const STATIC_FILE = [
        'define' => 'define.js',
        'frame' => 'frame.js',
        'css' => 'css.css',
    ];
    const DATA_FILE = [
        'chess' => 'chess.json',
        'race' => 'race.json',
        'job' => 'job.json',
        'equip' => 'equip.json',
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
        'keywords' => '云顶之弈,云顶之弈模拟器,云顶之弈计算器,自走棋,lol自走棋,自走棋模拟器',
    ];

    static function init() {
        //自动加载 _分割
        spl_autoload_register(function ($className) {
            // require_once str_replace('_', DIRECTORY_SEPARATOR, strtolower($className)) . '.php';
            require_once str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        });
        define('ROOT_DIR', realpath('.'));
        define('SITE_URL', dirname($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']));
        define('IS_DEVELOP', self::isDevelop());
        define('IS_MANAGER', self::isMe());
    }

    /**
     * 获取路由信息
     *
     * @return array
     */
    static function getRoute() {
        if ('/index.php' === $_SERVER['SCRIPT_NAME']) {
            $pathStr = $_SERVER['REQUEST_URI'];
        } else {
            $pathStr = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']) - strlen('/index.php'));
        }
        if (!preg_match("/^\/([a-zA-Z0-9]+)\/?([a-zA-Z0-9]+)?/", $pathStr, $matchs)) {
            return [
                0 => 'app/c/index.php',
                1 => 'app_c_index',
                2 => 'actionIndex',
            ];
        }
        if (empty($matchs[2])) {
            $matchs[2] = 'Index';
        }
        return [
            0 => 'app/c/' . $matchs[1] . '.php',
            1 => 'app_c_' . $matchs[1],
            2 => 'action' . $matchs[2],
        ];
    }

    /**
     * 开发环境
     * @return bool
     */
    static function isDevelop() {
        if ('moozik.cn' == $_SERVER['SERVER_NAME']) {
            return false;
        }
        if ('yunding.moozik.cn' == $_SERVER['SERVER_NAME']) {
            return false;
        }
        return true;
    }

    /**
     * 管理员
     * @return bool
     */
    static function isMe() {
        if (isset($_COOKIE['passwd']) && SEN::PASSWORD == $_COOKIE['passwd']) {
            return true;
        } else {
            return false;
        }
    }

    static function static_url($name) {
        return implode('/', [SITE_URL, self::STATIC_DIR, self::STATIC_FILE[$name]]);
    }

    /**
     * 展示视图
     * @param $name string 视图名称
     */
    static function display_page($name, $param = []) {
        extract($param);
        $res = debug_backtrace();
        preg_match("/_([^_]+)$/", $res[1]['class'], $res);
        require_once implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'app', 'v', $res[1], $name . '.php']);
    }

    static function static_path($name) {
        return implode(DIRECTORY_SEPARATOR, [ROOT_DIR, self::STATIC_DIR, self::STATIC_FILE[$name]]);
    }
    static function data_path($name) {
        return implode(DIRECTORY_SEPARATOR, [ROOT_DIR, self::DATA_DIR, self::DATA_FILE[$name]]);
    }

    static function ico_url() {
        return SITE_URL . '/' . self::ICO_FILE;
    }

    static function getIp() {
        foreach ([
                     'HTTP_CLIENT_IP',
                     'HTTP_X_FORWARDED_FOR',
                     'HTTP_X_FORWARDED',
                     'HTTP_X_CLUSTER_CLIENT_IP',
                     'HTTP_FORWARDED_FOR',
                     'HTTP_FORWARDED',
                     'REMOTE_ADDR',
                 ] as $key) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    //会过滤掉保留地址和私有地址段的IP，例如 127.0.0.1会被过滤
                    //也可以修改成正则验证IP
                    if ((bool)filter_var(
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
}


