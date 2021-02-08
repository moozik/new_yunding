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

    /**
     * 排序函数
     *
     * @param [array] $arr
     * @param [mixed] $keys
     * @param string $orderby
     * @return array
     */
    static function sort($arr, $keys, $orderby = 'desc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v){
            $keysvalue[$k] = $v[$keys];
        }
        if($orderby == 'asc'){
            asort($keysvalue);
        }else if($orderby == 'desc'){
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v){
            $new_array[] = $arr[$k];
        }
        return $new_array;
    }
}