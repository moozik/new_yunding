<?php
/**
 * 族群：种族或者职业
 * 
 */
abstract class m_object_groups{
    /**
     * id
     *
     * @var int
     */
    // public $id;
    /**
     * 当前种族数量
     *
     * @var int
     */
    // public $count = 0;
    /**
     * 当前种族是否有效
     *
     * @var bool
     */
    // public $isWork = false;
    /**
     * 当前种族有效个数
     *
     * @var int
     */
    // public $isWorkCount = 0;

    /**
     * 当前价值
     *
     * @var integer
     */
    // public $value = 0;
    /**
     * 即将成型的种族数量
     *
     * @var int
     */
    // public $featureCount = 0;

    /**
     * 返回可用羁绊数量
     * @return int
     */
    public function workCount($count){
        if(array_key_exists($count, $this->count2level)){
            return $this->count2level[$count];
        }else{
            //找不到返回顶级羁绊的数量
            return $this->count2level[0];
        }
    }
}