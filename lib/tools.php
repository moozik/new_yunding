<?php
class lib_tools{
    /**
     * 组合生成器
     * @param array $inArr
     * @param int $count
     * @return yield
     */
    static function choseIterator(&$inArr, $count){
        $inArrLen = count($inArr);
        if($inArrLen < $count){
            throw new Exception('inArr count error.');
        }
        if($inArrLen === $count){
            yield $inArr;
            return;
        }
        //二进制组合
        $position = array_fill(0, $inArrLen, 0);
        //前count个填充1
        for($i = 0; $i < $count; $i++){
            $position[$i] = 1;
        }
        //返回第一个组合
        yield self::genReturn($inArr, $position);
        while(self::nextCombine($position, $inArrLen)){
            yield self::genReturn($inArr, $position);
        }
    }
    /**
     * 二进制列表迭代
     */
    static private function nextCombine(&$position, &$inArrLen){
        $count_1 = 0;
        //搜索 1 0
        for($o = 0; $o < $inArrLen - 1; $o++){
            //1
            if(1 === $position[$o]){
                //0
                if(0 === $position[$o + 1]){
                    break;
                }
                $count_1++;
                //置0
                $position[$o] = 0;
            }
        }
        //截止点
        if($o === $inArrLen - 1 || 1 != $position[$o] || 0 != $position[$o + 1]){
            return false;
        }
        //交换10
        $position[$o] = 0;
        $position[$o + 1] = 1;
        //1移到左边
        for($o = 0; $o < $count_1; $o++){
            $position[$o] = 1;
        }
        return true;
    }
    /**
     * 由二进制列表生成chess
     */
    static function genReturn(&$inArr, &$position){
        static $ret = [];
        if($ret != []){
            $ret = [];
        }
        foreach($position as $key => &$flag){
            if($flag){
                $ret[] = $inArr[$key];
            }
        }
        return $ret;
    }

    /**
     * m选n结果集个数
     * @param int $m
     * @param int $n
     * @return int
     */
    static function m_chose_n($m, $n){
        return (self::fn($m) / (self::fn($n) * self::fn($m - $n)));
    }

    /**
     * 阶乘
     * @param int $n
     * @return void
     */
    static function fn($n){
        if($n == 0) return 1;
        $fn = 1;
        for($i = 1; $i <= $n; $i++){
            $fn *= $i;
        }
        return $fn;
    }

    /**
     * 数组成员类型转为int
     * @return array
     */
    static function arrIntval($arr){
        return array_map(function($var){
            return intval($var);
        },$arr);
    }

    /**
     * 根据json原数据生成levelmap数组
     * @return array
     */
    static function getLevelMap($obj) : array{
        if(isset($obj->raceId) && '9' == $obj->raceId){
            //忍者特殊处理
            return [0=>4, 1=>1, 2=>0, 3=>0, 4=>4];
        }
        $ret = [];
        // var_dump($obj->level);exit;
        foreach(array_keys((array)$obj->level) as $count){
            $ret[$count] = $count;
        }
        //默认顶级羁绊个数
        $ret[0] = $count;
        if(!array_key_exists(1, $ret)){
            $ret[1] = 0;
        }
        for($i = 2; $i < $count; $i++){
            if(!array_key_exists($i, $ret)){
                $ret[$i] = $ret[$i - 1];
            }
        }
        return $ret;
    }

    /**
     * Debug:
     * 递归转换Gid为羁绊名称
     */
    static function Gid2NameArr($arr){
        foreach($arr as $k => &$v){
            if(is_array($v)){
                $v = self::Gid2NameArr($v);
            }
            if(is_int($v)){
                $v = self::Gid2Name($v);
            }
        }
        return $arr;
    }

    /**
     * 转换Gid为羁绊名称
     */
    static function Gid2Name($Gid){
        if($Gid > 100){
            return m_dao_job::$data[$Gid - 100]->name;
        }else{
            return m_dao_race::$data[$Gid]->name;
        }
    }
}