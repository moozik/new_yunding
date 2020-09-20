<?php
class lib_tools{
    /**
     * 英雄组合生成器
     * @param array $inArr
     * @param int $count
     * @return yield
     */
    static function heroCombine(&$inArr, $count){
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
}