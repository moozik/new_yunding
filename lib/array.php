<?php
class lib_array{
    /**
     * @param array $arr
     * @return int $sum
     */
    static function sumBykey($arr, $key){
        return array_sum(array_map(function($i) use ($key){
            return $i[$key];
        },$arr));
    }

    static function append(&$arr, $val){
        if(is_array($arr)){
            $arr[] = $val;
        }else{
            $arr = [$val];
        }
    }
}