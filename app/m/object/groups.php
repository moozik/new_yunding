<?php
/**
 * 族群：种族或者职业
 * 
 */
class app_m_object_groups{
    /**
     * 强度级别
     */
    public static $opList = [];
    /**
     * 返回可用羁绊数量
     * @return int
     */
    public function workCount($count){
        if(array_key_exists($count, $this->GidMap)){
            return $this->GidMap[$count];
        }else{
            //找不到返回顶级羁绊的数量
            return $this->GidMap[0];
        }
    }

    /**
     * 解析color_list字段
     * 3:1,5:2,7:3,9:4
     */
    public static function colorList($Gid, $colorList) {
        $ret = [];
        $p1 = explode(",", $colorList);
        foreach($p1 as $item) {
            $p2 = explode(":", $item);
            $ret[(int)$p2[0]] = (int)$p2[1];
        }
        self::$opList[$Gid] = $ret;
    }
}
