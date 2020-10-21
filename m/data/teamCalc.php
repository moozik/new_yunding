<?php

class m_data_teamCalc{
    private $theOne = 0;
    private $teamCount = 9;
    private $inChess = [];
    private $banChess = [];
    private $weapon = [];
    private $costList = [1,2,3,4,5];
    
    //可用的所有英雄
    private $chess = [];
    function __construct()
    {
        //初始化dao
        m_dao_race::init();
        m_dao_job::init();
        m_dao_chess::init();
        m_dao_equip::init();
    }

    /**
     * 计算最优阵容
     */
    public function calc(){
        //获取英雄 阵容 金额 天选 转职装备
        $this->chess = $this->getChessArr();
        //todo
        /**
         * 1、找出当前可用，可遍历的所有羁绊，循环
         *      限制条件有：
         *          羁绊的人数要求：天选+装备+英雄羁绊
         *          羁绊的英雄要求：是否够金额，是否被ban
         */
    }

    
    /**
     * 获取可用英雄列表
     * @param array $usedHero
     * @return array
     */
    private function getChessArr(){
        $ret = [];
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
            //usedHero
            // if(in_array($chess->chessId, $usedHero)){
                //     continue;
                // }
                $ret[] = $chess->chessId;
        }
        SEN::debugLog('getChessArr', print_r($ret, true));
        return $ret;
    }

    /**
     * 设置参数
     */
    public function setInput($input){
        SEN::traceLog('calcInput', print_r($input, true));

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
    }
}