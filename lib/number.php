<?php
class lib_number{
    //计数器
    static $count = [];

    static function addOrDefault(&$number, $num){
        if(is_int($number)){
            $number += $num;
        }else{
            $number = $num;
        }
    }

    /**
     * 计数器
     */
    static function addCount($name = '', $num = 1){
        if(IS_DEVELOP){
            lib_number::addOrDefault(self::$count[$name], $num);
        }
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