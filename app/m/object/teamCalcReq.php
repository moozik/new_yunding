<?php

/**
 * 请求入参
 */
class app_m_object_teamCalcReq {
    /**
     * 循环层数
     */
    public $forCount = 3;
    /**
     * 输出队伍人数
     */
    public $teamChessCount = 9;
    /**
     * 输入棋子 chess
     */
    public $inChess = [];
    /**
     * 输入棋子 chessObj
     */
    public $inChessObj = [];
    /**
     * 输入ban棋子 Gid
     */
    public $banChess = [];
    /**
     * 可用英雄列表
     */
    public $chessArr = [];
    /**
     * 可用英雄对象列表
     */
    public $chessArrObj = [];
    /**
     * 转职装备
     * 原始数据
     */
    public $equip = [];
    /**
     * 转职装备
     * 原始数据
     */
    public $tagPlus = [];
    /**
     * 转职装备
     * 原始数据
     */
    public $tagPlusMap = [];
    /**
     * 棋子价格限制
     */
    public $costList = [1, 2, 3, 4, 5];
    /**
     * 可用棋子id数组
     *
     * @var array
     */
    public $freeChessArr = [];
    /**
     * 可用棋子id数组对象
     *
     * @var array
     */
    public $freeChessArrObj = [];
    /**
     * 空闲位置个数
     */
//    public $freePosition = 0;

    public $hexTecGid1 = 0;
    public $hexTecGid3 = 0;
    function __construct($input) {
        // 原样参数
        // if(isset($input->theOne)){
        //     $this->theOne = intval($input->theOne);
        // }
        if (isset($input->forCount)) {
            $this->forCount = intval($input->forCount);
        }
        if (isset($input->inChess)) {
            $this->inChess = lib_tools::arrIntval($input->inChess);
        }
        //当teamCount=-1 代表用forCount计算
        if (isset($input->teamCount) && $input->teamCount > 0) {
            $this->teamChessCount = intval($input->teamCount);
        } else {
            $this->teamChessCount = $input->forCount + count($input->inChess);
        }

        if ($this->teamChessCount > 10) {
            $this->forCount = 0;
            $this->teamChessCount = 10;
        }

        if (isset($input->banChess)) {
            $this->banChess = lib_tools::arrIntval($input->banChess);
        }
        if (isset($input->equip)) {
            $this->equip = lib_tools::arrIntval($input->equip);
        }
        if (isset($input->tagPlus)) {
            $this->tagPlus = lib_tools::arrIntval($input->tagPlus);
        }
        if (isset($input->costList)) {
            $this->costList = lib_tools::arrIntval($input->costList);
        }
        //计算参数
        // if($this->teamCount < 0){
        //     $this->freePosition = $this->forCount;
        // }else{
        //     $this->freePosition = $this->teamCount - count($this->inChess);
        // }

        // if($this->freePosition < 0){
        //     throw new Exception("freePosition");
        // }
        //计算当前棋子
        foreach ($this->inChess as $chessId) {
            $this->inChessObj[] = app_m_data_Factory::get(usr_def::chess, $chessId);
        }
        // lib_log::debug('$this->costList', $this->costList);exit;
        if (empty($this->costList)) {
            throw new Exception("参数错误costList");
        }
        //海克斯
        if (!empty($input->hexType1)){
            $this->hexTecGid1 = $this->getGidByHex($input->hexType1);
        }
        if (!empty($input->hexType3)){
            $this->hexTecGid3 = $this->getGidByHex($input->hexType3);
        }
    }

    /**
     * 转职装备预处理
     */
    public function dealEquipPre() {
        if (!empty($this->tagPlus)) {
            //处理转职装备
            // if(count($this->weapon) > usr_def::IN_WEAPON_MAX){
            //     //超过最大值的装备被删除
            //     $this->weapon = array_slice($this->weapon, 0, usr_def::IN_WEAPON_MAX);
            // }
            //groupid2count映射
            $equipGroup2Count = [];
            foreach ($this->tagPlus as $Gid) {
                lib_number::addOrDefault($equipGroup2Count[$Gid], 1);
            }
            $this->tagPlusMap = $equipGroup2Count;
            //lib_log::debug('$this->equip', $this->equip);
        }
    }

    /**
     * 计算当前队伍人数所属级别对应的英雄价格
     */
    public function dealCostList() {
        //修正costList
        foreach ($this->costList as $k => $costVal) {
            if ($costVal < 1 || $costVal > 5) {
                //非法数据
                unset($this->costList[$k]);
                continue;
            }
            if (isset(usr_conf::LEVEL2COST[$this->teamChessCount])) {
                if (!in_array($costVal, usr_conf::LEVEL2COST[$this->teamChessCount])) {
                    //删除对应级别刷不出来的英雄价格
                    unset($this->costList[$k]);
                }
            }
        }
    }

    /**
     * 获取可用英雄列表
     * @return array
     */
    public function getFreeChess() {
        $this->freeChessArr = [];
        $this->freeChessArrObj = [];
        foreach (app_m_dao_chess::$data as $chess) {
            //大魔王不匹配
            if ($chess->chessId === 45){
                continue;
            }
            //inChess banChess
            if (in_array($chess->chessId, $this->inChess)
                || in_array($chess->chessId, $this->banChess)) {
                continue;
            }
            //costList
            if (!in_array($chess->price, $this->costList)) {
                continue;
            }
            $this->freeChessArr[] = $chess->chessId;
            $this->freeChessArrObj[] = app_m_data_Factory::get(usr_def::chess, $chess->chessId);
        }
    }

    /**
     * 根据海克斯装备名称搜索gid
     */
    public function getGidByHex($hexName) {
        foreach (app_m_dao_job::$data as $jobItem) {
            if (strpos($hexName, $jobItem->name) === 0) {
                return $jobItem->jobId + 100;
            }
        }
        foreach (app_m_dao_race::$data as $raceItem) {
            if (strpos($hexName, $raceItem->name) === 0) {
                return $raceItem->raceId;
            }
        }
        throw new Exception("参数异常,hexName:" . $hexName);
    }
}
