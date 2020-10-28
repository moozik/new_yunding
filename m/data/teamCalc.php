<?php

class m_data_teamCalc{
    /**
     * @var m_object_teamCalcReq
     */
    private $req = null;

    //入参羁绊列表 *只读 G->count
    //包括转职
    private $inputGid2count = [];

    //入参英雄以组成羁绊列表 G->count
    private $inputGid2readyCount = [];

    //可用羁绊列表 *只读 [lib_def::Gid,lib_def::Gcount,lib_def::Gneed]
    private $canChoseGidList = [];

    //当前可用棋子对应的可用羁绊数量
    private $freeGid2count = [];
    /**
     * job race 羁绊个数对应关系数组
     */
    static public $GidLevelMap = [];

    //当前可用棋子，对应的羁绊交集数组 [Gidmin][Gidmax] = count
    private $freeChessGidintersection = [];

    //羁绊组合结果
    private $generateGcombination = [];

    function __construct(){
        //初始化dao
        m_dao_race::init();
        m_dao_job::init();
        m_dao_chess::init();
        m_dao_equip::init();
        // self::$GidLevelMap = array_merge(m_dao_job::$GidMap, m_dao_race::$GidMap);
        self::$GidLevelMap = m_dao_job::$GidMap + m_dao_race::$GidMap;
        lib_log::debug('GidLevelMap', self::$GidLevelMap);
    }

    /**
     * 计算最优阵容
     */
    public function calc(){
        //当前已有羁绊数量

        lib_timer::start('getGid2count');
        $this->getGid2count($this->inputGid2count, $this->req);
        // lib_log::debug('inputGid2count', $this->inputGid2count);

        //当前已组成羁绊列表
        foreach($this->inputGid2count as $Gid => $count){
            $usedCount = m_data_Factory::getGid($Gid)->workCount($count);
            if(0 != $usedCount){
                $this->inputGid2readyCount[$Gid] = $usedCount;
            }
        }
        lib_timer::stop('getGid2count');

        //当前可用棋子，对应的可用羁绊数量
        lib_timer::start('getFreeGbyfreeChess');
        $this->freeGid2count = $this->getFreeGbyfreeChess($this->req);
        lib_timer::stop('getFreeGbyfreeChess');
        // lib_log::debug('freeGid2count', $this->freeGid2count);



        //当前可用棋子，对应的羁绊交集数组
        lib_timer::start('freeChessGidintersection');
        $this->freeChessGidintersection();
        lib_timer::stop('freeChessGidintersection');
        // lib_log::debug('freeChessGidintersection', $this->freeChessGidintersection);
        // return $this->freeChessGidintersection;

        //当前可选羁绊
        lib_timer::start('canChoseGidList');
        $this->canChoseGidList($this->canChoseGidList, $this->inputGid2count, $this->freeGid2count, $this->req);
        lib_timer::stop('canChoseGidList');
        // lib_log::debug('canChoseGidList', array_map(function($v){
        //     $v[lib_def::Gid] = lib_tools::Gid2Name($v[lib_def::Gid]);
        //     return $v;
        // },$this->canChoseGidList));
        // return $this->canChoseGidList;

        /**
        从可选羁绊中选出(1--n)个可选羁绊，n=队伍人数+1-已有羁绊，n为队伍人数可选得最大羁绊个数
        n = $req->teamCount + 1 - count($this->inputGid2readyCount)
         */
        //组合可选羁绊组合
        lib_timer::start('generateGcombination');
        $this->generateGcombination();
        lib_timer::stop('generateGcombination');
        // lib_log::debug('GcombinationResult', $this->GcombinationResult);

        lib_log::debug('loopCount', lib_number::getCount());
        lib_log::debug('lib_timer', lib_timer::$result);
        //遍历可选羁绊组合，获取对应棋子组合
        return $this->generateGcombination;
    }

    /**
     * 当前可用羁绊棋子交集，用于计算羁绊组合所需棋子数量
     */
    function freeChessGidintersection(){
        //遍历所有可用棋子
        foreach($this->req->chessArrObj as $chessObj){
            //遍历每两个羁绊，存储交集个数+1
            foreach(lib_tools::choseIterator(array_keys($chessObj->Gids), 2) as $Gtwo){
                lib_number::addCount(__FUNCTION__);
                //存储羁绊交集对应得chessObj
                lib_array::append($this->freeChessGidintersection[min($Gtwo[0], $Gtwo[1])][max($Gtwo[0], $Gtwo[1])], $chessObj->chessId);
                
                // lib_number::addOrDefault($this->freeChessGidintersection[min($Gtwo[0], $Gtwo[1])][max($Gtwo[0], $Gtwo[1])], 1);
            }
        }
    }
    /**
     * 组合可选羁绊
     */
    function generateGcombination(){
        //可能的最大组合个数
        $n = $this->req->teamCount + 1 - count($this->inputGid2readyCount);
        for($i = $n; $i > 0; $i--){
            lib_log::debug('generateGcombination:i', $i);
            //遍历选择器 羁绊m选n
            foreach(lib_tools::choseIterator($this->canChoseGidList, $i) as $Gcombination){
                lib_number::addCount(__FUNCTION__.$i);

                //理论所需个数
                $needCount = lib_array::sumBykey($Gcombination, lib_def::Gneed);
                //过滤羁绊组合，需要人数!=空闲位置
                if(1 == $i && $needCount != $this->req->freePosition){
                    //1羁绊情况下，需求个数不为空缺人数，跳出
                    continue;
                }
                //过滤羁绊组合，需要人数<空闲位置
                if($needCount < $this->req->freePosition){
                    lib_number::addCount(__FUNCTION__.'_con1');
                    continue;
                }

                //多羁绊情况下，计算多羁绊最少需要的人数，若需要的人数大于空缺人数，跳出
                //羁绊交集棋子列表
                $combinationArr = $this->GcombinationNeedChessCount($Gcombination, $needCount);
                if(false === $combinationArr){
                    lib_number::addCount(__FUNCTION__.'_con2');
                    continue;
                }
                //没有共用棋子且没有高级羁绊，跳出
                $flag = false;
                foreach($Gcombination as $Gitem){
                    if($Gitem[lib_def::GOPlevel] >= lib_def::Ghigh){
                        $flag = true;
                        break;
                    }
                }
                //没有顶级羁绊且共用结果为空，低质量组合，跳出
                if(!$flag && empty($combinationArr) && $this->req->teamCount >= 3){
                    lib_number::addCount(__FUNCTION__.'_con3');
                    continue;
                }


                //所需个数 != 空缺个数
                if($needCount - count($combinationArr) != $this->req->freePosition){
                    lib_number::addCount(__FUNCTION__.'_con8');
                    continue;
                }

                // var_dump(
                //     $combinationArr,
                //     $needCount,
                //     $this->req->freePosition,
                //     $Gcombination
                // );
                // exit;
                //记录当前羁绊组合
                $this->generateGcombination[] = $Gcombination;
            }
            lib_log::debug('generateGcombination:', sprintf("i:%s,count:%s", $i, lib_number::getCount(__FUNCTION__.$i)));
        }
    }

    /**
     * 计算多羁绊交集棋子个数 入参个数最少为2
     */
    function GcombinationNeedChessCount(&$Gcombination, $needCount){
        //每两个羁绊，计算交集个数
        if(1 == count($Gcombination)){
            return [];
        }
        lib_timer::start(__FUNCTION__);
        $combinationArr = [];
        foreach(lib_tools::choseIterator($Gcombination, 2) as $Gtwo){
            lib_number::addCount(__FUNCTION__);
            $min = min($Gtwo[0][lib_def::Gid], $Gtwo[1][lib_def::Gid]);
            $max = max($Gtwo[0][lib_def::Gid], $Gtwo[1][lib_def::Gid]);
            if(!isset($this->freeChessGidintersection[$min][$max])){
                lib_number::addCount(__FUNCTION__.'_con4');
                continue;
            }
            // echo $min.'-'.$max.':'.$this->freeChessGidintersection[$min][$max]."\n";
            foreach($this->freeChessGidintersection[$min][$max] as $chessId){
                $combinationArr[$chessId] = 1;
            }
            // lib_array::append($combinationArr, $this->freeChessGidintersection[$min][$max]);
            // $combinationArr += $this->freeChessGidintersection[$min][$max];
            
            //剪枝
            if(count($combinationArr) > $this->req->freePosition){
                lib_number::addCount(__FUNCTION__.'_con5');
                lib_timer::stop(__FUNCTION__);
                return false;
            }
            if(count($combinationArr) + $this->req->freePosition > $needCount){
                lib_number::addCount(__FUNCTION__.'_con6');
                lib_timer::stop(__FUNCTION__);
                return false;
            }
            
        }
        lib_timer::stop(__FUNCTION__);
        return array_keys($combinationArr);
    }
    /**
     * 计算当前可选羁绊列表
     * @param $chessArr 当前可用英雄
     * @param $
     */
    function canChoseGidList(&$canChoseGidList, &$inputGid2count, &$freeGid2count, m_object_teamCalcReq $req){
        //遍历所有羁绊
        foreach(array_keys(self::$GidLevelMap) as $Gid){
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
                lib_number::addCount(__FUNCTION__);
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
                $canChoseGidList[] = [
                    lib_def::Gid => $Gid,
                    lib_def::Gcount => $countIn,
                    lib_def::Gneed => $countIn - $existCount,
                    //存储羁绊的稀有度 0123依次变高
                    lib_def::GOPlevel => lib_conf::GidOPLevel($Gid, $countIn)
                ];
                //$Gid;
                // if(isset($canChoseGidList[$countIn])){
                //     $canChoseGidList[$countIn][] = $Gid;
                // }else{
                //     $canChoseGidList[$countIn] = [$Gid];
                // }
            }
        }
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
                lib_number::addCount(__FUNCTION__);
                lib_number::addOrDefault($inputGid2count[$Gid], 1);
            }
        }
        if(0 !== $req->theOne){
            lib_number::addOrDefault($inputGid2count[$req->theOne], 1);
        }
        foreach($req->weapon as $Gid){
            lib_number::addCount(__FUNCTION__);
            lib_number::addOrDefault($inputGid2count[$Gid], 1);
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
                lib_number::addCount(__FUNCTION__);
                // var_dump($ret,$Gid);exit;
                lib_number::addOrDefault($ret[$Gid], 1);
            }
        }
        return $ret;
    }

    /**
     * 设置参数
     */
    public function setInput($input){
        lib_log::trace('calcInput', print_r($input, true));
        $this->req = new m_object_teamCalcReq($input);
    }
}