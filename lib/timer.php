<?php
class lib_timer{
    static $data = [];
    static $result = [];

    static function start($key){
        if(isset(self::$data[$key])){
            return false;
        }
        self::$data[$key] = microtime(true);
    }

    static function stop($key){
        $now = microtime(true);
        $time = $now - self::$data[$key];
        unset(self::$data[$key]);
        if(array_key_exists($key, self::$result)){
            self::$result[$key] += $time;
        }else{
            self::$result[$key] = $time;
        }
        return $time;
    }

    static function getResult(){
        foreach(self::$result as $key => &$time){
            if($time < 0.0001){
                self::$result[$key] = 0.0001;
            }
        }
        ksort(self::$result);
        return self::$result;
    }
}