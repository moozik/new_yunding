<?php

class sensen{
    public function myaction(){
        yuyu::myaction2();
    }
}

class yuyu{
    static public function myaction2(){
        $res = debug_backtrace();
        print_r($res);
    }
}

$obj = new sensen;
$obj->myaction();