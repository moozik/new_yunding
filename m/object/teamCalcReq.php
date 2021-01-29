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
     * 循环层数
     */
    public $forCount = 3;
    /**
     * 输出队伍人数
     */
    public $teamCount = 9;
    /**
     * 输入棋子 chessid
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
     * 转职装备 Gid
     */
    public $weapon = [];
    /**
     * 棋子价格限制
     */
    public $costList = [1,2,3,4,5];
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
    public $freePosition = 0;
    function __construct($input){
        //{"theOne":0,"teamCount":9,"forCount":3,"inChess":[],"banChess":[],"weapon":[],"costList":[1,2,3,4,5]}
        // 原样参数
        if(isset($input->theOne)){
            $this->theOne = intval($input->theOne);
        }
        if(isset($input->forCount)){
            $this->forCount = intval($input->forCount);
        }
        // if(isset($input->teamCount)){
        //     $this->teamCount = intval($input->teamCount);
        // }
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
        //计算参数
        // $this->freePosition = $this->teamCount - count($this->inChess);
        // if($this->freePosition < 0){
        //     throw new Exception("参数错误freePosition");
        // }
        //计算当前棋子
        foreach($this->inChess as $chessId){
            $this->inChessObj[] = m_data_Factory::get(lib_def::chess, $chessId);
        }
        lib_log::debug('$this->inChessObj', $this->inChessObj);
        // lib_log::debug('$this->costList', $this->costList);exit;
        if(empty($this->costList)){
            throw new Exception("参数错误costList");
        }
        // $this->freeChessArr = $this->getFreeChess($this);
        // $this->freeChessArrObj = $this->getFreeChess($this, true);
        // lib_log::debug('m_object_teamCalcReq', serialize($this));
    }

    public function dealWeaponPre(){
        //转职装备预处理
        if(!empty($this->req->weapon)){
            //处理转职装备
            if(count($this->req->weapon) > lib_conf::IN_WEAPON_MAX){
                //超过最大值的装备被删除
                $this->req->weapon = array_slice($this->req->weapon, 0, lib_conf::IN_WEAPON_MAX);
            }
            //groupid2count映射
            $weaponGroup2Count = [];
            foreach($this->req->weapon as $Gid){
                lib_number::addOrDefault($weaponGroup2Count[$Gid], 1);
            }
            $this->req->weapon = $weaponGroup2Count;
        }
    }

    public function dealCostList(){
        //修正costList
        foreach($this->costList as $k => $costVal){
            if($costVal < 1 || $costVal > 5){
                //非法数据
                unset($this->costList[$k]);
                continue;
            }
            if(!in_array($costVal, lib_conf::LEVEL2COST[$this->forCount + count($this->inChess)])){
                //删除对应级别刷不出来的英雄价格
                unset($this->costList[$k]);
            }
        }
    }

    /**
     * 获取可用英雄列表
     * @param m_object_teamCalcReq $req
     * @return array
     */
    public function getFreeChess(){
        $this->freeChessArr = [];
        $this->freeChessArrObj = [];
        foreach(m_dao_chess::$data as $chess){
            //inChess banChess
            if(in_array($chess->chessId, $this->inChess)
                || in_array($chess->chessId, $this->banChess)){
                continue;
            }
            //costList
            if(!in_array($chess->price, $this->costList)){
                continue;
            }
            $this->freeChessArr[] = $chess->chessId;
            $this->freeChessArrObj[] = m_data_Factory::get(lib_def::chess, $chess->chessId);
        }
    }
}