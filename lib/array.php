<?php

class lib_array {
    /**
     * @param array $arr
     * @return $sum
     */
    static function sumBykey($arr, $key) {
        return array_sum(array_map(function ($i) use ($key) {
            return $i[$key];
        }, $arr));
    }

    /**
     * 排序函数
     *
     * @param [array] $arr
     * @param [mixed] $keys
     * @param $orderby
     * @return array
     */
    static function sort($arr, $keys, $orderby = 'desc') {
        $keysValue = $new_array = [];
        foreach ($arr as $k => $v) {
            $keysValue[$k] = $v[$keys];
        }
        if ($orderby == 'asc') {
            asort($keysValue);
        } else {
            if ($orderby == 'desc') {
                arsort($keysValue);
            }
        }
        reset($keysValue);
        foreach ($keysValue as $k => $v) {
            $new_array[] = $arr[$k];
        }
        return $new_array;
    }
}