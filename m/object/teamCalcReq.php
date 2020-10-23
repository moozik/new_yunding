<?php
/**
 * 请求入参
 */
class m_object_teamCalcReq{
    /**
     * 天选之人
     */
    public $theOne = 0;
    /**
     * 输出队伍人数
     */
    public $teamCount = 9;
    /**
     * 输入棋子 Gid
     */
    public $inChess = [];
    /**
     * 输入ban棋子 Gid
     */
    public $banChess = [];
    /**
     * 可用英雄列表
     */
    public $chessArr = [];
    /**
     * 转职装备 Gid
     */
    public $weapon = [];
    /**
     * 棋子价格限制
     */
    public $costList = [1,2,3,4,5];
    /**
     * 空闲位置个数
     */
    public $freePosition = 0;
    function __construct($input){
        if(isset($input->theOne)){
            $this->theOne = intval($input->theOne);
        }
        if(isset($input->teamCount)){
            $this->teamCount = intval($input->teamCount);
        }
        if(isset($input->inChess)){
            $this->inChess = lib_tools::arrIntval($input->inChess);
        }
        if(isset($input->banChess)){
            $this->banChess = lib_tools::arrIntval($input->banChess);
        }
        if(isset($input->weapon)){
            $this->weapon = lib_tools::arrIntval($input->weapon);
        }
        if(isset($input->costList)){
            $this->costList = lib_tools::arrIntval($input->costList);
        }
        $this->freePosition = $this->teamCount - count($this->inChess);
        if($this->freePosition < 0){
            throw new Exception("参数错误freePosition");
        }
        //修正costList
        foreach($this->costList as &$costVal){
            if($costVal < 1 || $costVal > 5){
                //非法数据
                unset($costVal);
                continue;
            }
            if(!in_array($costVal, lib_conf::LEVEL2COST[$this->teamCount])){
                //删除对应级别刷不出来的英雄价格
                unset($costVal);
            }
        }
        if(empty($this->costList)){
            throw new Exception("参数错误costList");
        }
        $this->chessArr = m_data_teamCalc::getFreeChess($this);
        SEN::debugLog('m_object_teamCalcReq', serialize($this));
    }
}