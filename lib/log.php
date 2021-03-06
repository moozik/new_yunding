<?php
class lib_log{
    private static $instance = null;
    private $ip = '';
    private $arrLogLevel = [
        'debug',
        'trace',
        'warning',
        'fatal',
    ];
    private $debugPath = '';
    private $tracePath = '';
    private $warningPath = '';
    private $fatalPath = '';

    private $rootDirLength = 0;
    private function __construct(){
        self::genLogid();
        $this->ip = sen::getIp();
        $this->rootDirLength = strlen(ROOT_DIR) + 1;
        $lodDir = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, sen::LOG_DIR]);
        if(!file_exists($lodDir)) {
            mkdir($lodDir);
        }
        foreach($this->arrLogLevel as $levelName){
            $this->{$levelName.'Path'} = self::log_file($levelName);
        }
    }
    /**
     * 单例
     * @return lib_log
     */
    static function getIns(){
        if(null !== self::$instance){
            return self::$instance;
        }
        self::$instance = new self();
        return self::$instance;
    }
    static function genLogid(){
        $arr = gettimeofday();
        $logId = $arr['sec'] * 100000 + $arr['usec'] / 10 & 2147483647 | 2147483648;
        define('LOG_ID', $logId);
    }
    static function log_file($logFile){
        return implode(DIRECTORY_SEPARATOR, [ROOT_DIR, sen::LOG_DIR , $logFile . '.log.' . date('Y_W')]);
    }

    static function debug(string $name, $msg){
        if(IS_DEVELOP){
            if(is_array($msg))
                $msg = print_r($msg, 1);
            if(is_object($msg))
                $msg = lib_string::encode($msg);
            $instance = self::getIns();
            $instance->Log('DEBUG', $name, $msg, $instance->debugPath);
        }
    }
    static function warning(string $name, string $msg){
        $instance = self::getIns();
        $instance->Log('WARNING', $name, $msg, $instance->warningPath);
    }
    static function fatal(string $name, string $msg){
        $instance = self::getIns();
        $instance->Log('FATAL', $name, $msg, $instance->fatalPath);
    }
    static function trace(string $name, string $msg){
        $instance = self::getIns();
        $instance->Log('TRACE', $name, $msg, $instance->tracePath);
    }
    static function access(string $name, string $msg){
        $instance = self::getIns();
        $instance->LogMore('ACCESS', $name, $msg, $instance->accessPath);
    }
    private function LogMore($logLev, $name, $msg, $file){
        if (file_exists(iplocation::$filename)){
            $ipLocation = new iplocation();
            $position = $ipLocation->getlocation($this->ip);
        }
        error_log(
            sprintf(
                "%s:%s [%s-%s%s][logid:%s][%s]\n%s:%s\n",
                $logLev,
                date('Y-m-d H:i:s'),
                $this->ip,
                $position['country'],
                $position['area'],
                LOG_ID,
                $_SERVER['HTTP_USER_AGENT'],
                $name,
                $msg
            ),
            3,
            $file
        );
    }
    /**
     * @param string $msg
     * @param string $file
     * @return void
     */
    private function Log($logLev, $name, $msg, $file){
        $trace = debug_backtrace();
        //print_r($trace);
        error_log(
            sprintf(
                "%s:%s logid[%s][%s:%s]\n%s:%s\n",
                $logLev,
                date('Y-m-d H:i:s'),
                LOG_ID,
                substr($trace[1]['file'], $this->rootDirLength),
                $trace[1]['line'],
                $name,
                $msg
            ),
            3,
            $file
        );
    }
}