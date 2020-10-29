<?php
class lib_number{
    //计数器
    static $count = [];

    static function addOrDefault(&$number, $default){
        if(is_int($number)){
            $number++;
        }else{
            $number = $default;
        }
    }

    /**
     * 计数器
     */
    static function addCount($name = ''){
        lib_number::addOrDefault(self::$count[$name], 1);
    }
    /**
     * 取出计数
     */
    static function getCount($name = ''){
        if('' === $name){
            ksort(self::$count);
            return self::$count;
        }
        return self::$count[$name];
    }
}