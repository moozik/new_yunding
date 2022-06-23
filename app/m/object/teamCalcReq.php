<?php

/**
 * 请求入参
 * 1. 安全校验
 * 2. 信息整理
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
    public $inEquipObj = [];
    /**
     * 转职装备
     * 羁绊id => 个数
     */
    public $inEquipMap = [];
    /**
     * 棋子价格限制
     */
    public $costList = [1, 2, 3, 4, 5, 8, 10];
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
     * hex
     */
    public $inHexList = [];
    /**
     * hex obj arr
     */
    public $hexArrObj = [];
    /**
     * 已存在的羁绊数量
     * 1. 龙神
     * 2. 符文
     */
    public $tagPlusByHex = [];
    /**
     * 输入列表是否有龙神
     */
    public $haveDragonGod = false;
    
    
    function __construct($input) {
        lib_log::debug("app_m_object_teamCalcReq",json_encode($input));
        // 原样参数
        if (isset($input->forCount)) {
            $this->forCount = intval($input->forCount);
        }
        if (isset($input->inChess)) {
            // 龙神版本 inchess可能为字符
            $this->inChess = $input->inChess;
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
        if (isset($input->costList)) {
            $this->costList = lib_tools::arrIntval($input->costList);
        }
        //计算当前棋子
        foreach ($this->inChess as $chessId) {
            $this->inChessObj[] = app_m_data_Factory::get(usr_def::chess, $chessId);
        }
        // lib_log::debug('$this->costList', $this->costList);exit;
        if (empty($this->costList)) {
            throw new Exception("参数错误costList");
        }
        //海克斯 符文
        foreach($input->hexList as $hexId) {
            $hexObj = app_m_data_Factory::get(usr_def::hex, $hexId);
            $this->inHexList[] = $hexId;
            $this->hexArrObj[] = $hexObj;
            $this->tagPlusByHex[] = $hexObj->Gid;
        }
        $this->dealCostList();
        $this->dealEquipPre();
        $this->getFreeChess();
    }

    /**
     * 转职装备预处理
     * 会考虑到装备的人员数量
     */
    public function dealEquipPre() {
        if (!empty($this->equip)) {
            foreach ($this->equip as $equipId) {
                $equipObj = app_m_data_Factory::get(usr_def::equip, $equipId);
                $this->inEquipObj[] = $equipObj;
                lib_number::addOrDefault($this->inEquipMap[$equipObj->getGid()], 1);
            }
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
            // if ($chess->chessId === 45){
            //     continue;
            // }
            //inChess banChess
            if (in_array($chess->chessId, $this->inChess)
                || in_array($chess->chessId, $this->banChess)) {
                continue;
            }
            //costList
            if (!in_array($chess->price, $this->costList) && $chess->price <= 5) {
                continue;
            }
            // 5以上的都认为是5费
            if ($chess->price > 5 && !in_array(5, $this->costList)){
                continue;
            }
            $this->freeChessArr[] = $chess->chessId;
            $this->freeChessArrObj[] = app_m_data_Factory::get(usr_def::chess, $chess->chessId);
        }
    }
}
