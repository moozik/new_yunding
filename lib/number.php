<?php
class lib_number{
    static function numberAddOrDefault(&$number, $default){
        if(is_int($number)){
            $number++;
        }else{
            $number = $default;
        }
    }
}