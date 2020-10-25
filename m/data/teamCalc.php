<?php

class m_data_teamCalc{
    /**
     * @var m_object_teamCalcReq
     */
    private $req = null;
    
    //可用的所有英雄
    private $chess = [];

    //入参羁绊列表 *只读 G->count
    //包括转职
    private $inputGid2count = [];

    //可用羁绊列表 *只读 count->G
    private $freeCount2Gid = [];

    //当前可用应用对应的可用羁绊数量
    private $freeGid2count = [];
    /**
     * job race 羁绊个数对应关系数组
     */
    static public $GidLevelMap = [];

    function __construct(){
        //初始化dao
        m_dao_race::init();
        m_dao_job::init();
        m_dao_chess::init();
        m_dao_equip::init();
        // self::$GidLevelMap = array_merge(m_dao_job::$GidMap, m_dao_race::$GidMap);
        self::$GidLevelMap = m_dao_job::$GidMap + m_dao_race::$GidMap;
        sen::debugLog('GidLevelMap', self::$GidLevelMap);
    }

    /**
     * 计算最优阵容
     */
    public function calc(){
        //当前已有羁绊数量
        $this->getGid2count($this->inputGid2count, $this->req);
        sen::debugLog('inputGid2count', $this->inputGid2count);

        //当前可用棋子，对应的可用羁绊数量
        $this->freeGid2count = $this->getFreeGbyfreeChess($this->req);
        sen::debugLog('freeGid2count', $this->freeGid2count);

        //当前可选羁绊
        $this->getCount2Gid($this->freeCount2Gid, $this->inputGid2count, $this->freeGid2count, $this->req);
        sen::debugLog('freeCount2Gid', array_map(function($v){
            $v[lib_def::Gid] = lib_tools::Gid2Name($v[lib_def::Gid]);
            return $v;
        },$this->freeCount2Gid));

        return $this->freeCount2Gid;
    }

    /**
     * 计算当前可选羁绊列表
     * @param $chessArr 当前可用英雄
     * @param $
     */
    function getCount2Gid(&$freeCount2Gid, &$inputGid2count, &$freeGid2count, m_object_teamCalcReq $req){
        //遍历所有羁绊
        foreach($this->Gidarr() as $Gid){
            //修正已有羁绊数量
            if(array_key_exists($Gid, $inputGid2count)){
                $existCount = $inputGid2count[$Gid];
            }else{
                $existCount = 0;
            }
            // if(106 == $Gid){
            //     var_dump(
            //         $inputGid2count,
            //         $freeGid2count,
            //         $existCount
            //     );
            // }else{
            //     continue;
            // }

            //遍历对应羁绊的 羁绊有效个数map
            foreach(self::$GidLevelMap[$Gid] as $countIn => $countRet){
                // echo "a,$countIn=>$countRet\n";
                
                //跳过0位
                if(0 === $countIn){
                    continue;
                }
                //跳过当前既有羁绊的个数 比如传入3三国，便不再考虑三国*3
                if($countIn <= $existCount){
                    continue;
                }
                //当前羁绊所需个数-已有个数 > 当前剩余位置
                if(($countIn - $existCount) > $req->freePosition){
                    continue;
                }
                //可选羁绊个数 != 有效羁绊个数
                if($countIn != $countRet){
                    continue;
                }
                //可选羁绊个数 > 当前可用羁绊个数
                if($countIn > $freeGid2count[$Gid]){
                    continue;
                }
                //可选羁绊
                $freeCount2Gid[] = [
                    lib_def::Gid => $Gid,
                    lib_def::Gcount => $countIn,
                    lib_def::Gneed => $countIn - $existCount,
                ];
                //$Gid;
                // if(isset($freeCount2Gid[$countIn])){
                //     $freeCount2Gid[$countIn][] = $Gid;
                // }else{
                //     $freeCount2Gid[$countIn] = [$Gid];
                // }
            }
        }
    }

    /**
     * 返回所有羁绊Gid
     */
    function Gidarr(){
        $jobArr = array_map(
            function($x){return $x + 100;},
            array_keys(m_dao_job::$data)
        );
        $raceArr = array_keys(m_dao_race::$data);
        return array_merge($jobArr, $raceArr);
    }
    /**
     * 计算英雄 天选 专职装备合计羁绊的 id=>count
     * @param &$ret
     * @param $chessArr 可用棋子
     * @param $theOne 当前天选
     * @param $weaponArr 当前转职装备
     */
    function getGid2count(&$inputGid2count, m_object_teamCalcReq $req){
        foreach($req->inChess as $chessId){
            foreach(m_data_Factory::get(lib_def::chess, $chessId)->Gids as $Gid => $Gitem){
                lib_number::numberAddOrDefault($inputGid2count[$Gid], 1);
            }
        }
        if(0 !== $req->theOne){
            lib_number::numberAddOrDefault($inputGid2count[$req->theOne], 1);
        }
        foreach($req->weapon as $Gid){
            lib_number::numberAddOrDefault($inputGid2count[$Gid], 1);
        }
    }

    /**
     * 根据可用英雄获取可用羁绊个数
     * @param m_object_teamCalcReq $req
     */
    static function getFreeGbyfreeChess(m_object_teamCalcReq $req){
        $ret = [];
        foreach($req->chessArr as $chessId){
            foreach(m_data_Factory::get(lib_def::chess, $chessId)->Gids as $Gid => $Gitem){
                // var_dump($ret,$Gid);exit;
                lib_number::numberAddOrDefault($ret[$Gid], 1);
            }
        }
        return $ret;
    }

    /**
     * 设置参数
     */
    public function setInput($input){
        SEN::traceLog('calcInput', print_r($input, true));
        $this->req = new m_object_teamCalcReq($input);
    }
}