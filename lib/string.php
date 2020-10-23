<?php
class lib_string{
    static function encode($arr){
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }
}