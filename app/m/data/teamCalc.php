<?php

class app_m_data_teamCalc {
    //装饰器
//    use lib_decorator;

    /**
     * @var m_object_teamCalcReq
     */
    private $req;

    //入参羁绊列表 *只读 G->count
    //包括转职
    private $inputGid2count = [];

    //入参英雄以组成羁绊列表 G->count
    private $inputGid2readyCount = [];

    //可用羁绊列表 *只读 [usr_def::Gid,usr_def::Gcount,usr_def::Gneed]
    private $canChoseGidList = [];

    //当前可用棋子对应的可用羁绊数量
    private $freeGid2count = [];
    /**
     * job race 羁绊个数对应关系数组
     */
    static public $GidLevelMap = [];

    //当前可用棋子，对应的羁绊交集数组 [Gidmin][Gidmax] = count
    private $freeChessGidIntersection = [];

    //羁绊组合结果
    private $generateGcombination = [];

    //羁绊组合结果个数
    const G_RESULT_COUNT = 10;

    function __construct() {
        self::$GidLevelMap = app_m_dao_job::$GidMap + app_m_dao_race::$GidMap;
        // lib_log::debug('GidLevelMap', self::$GidLevelMap);
    }

//    function beforeAction(&$method, &$params){
//        lib_number::addCount('call_'.$method);
//        lib_timer::start($method);
//        // lib_log::debug('afterAction:'.$method, $params);
//    }
//    function afterAction(&$method, &$params, &$res){
//        lib_timer::stop($method);
//        // lib_log::debug('afterAction:'.$method, $res);
//    }
    /**
     * 计算最优阵容
     */
    public function calc() {
        //当前已有羁绊数量

        $this->getGid2count();

        //当前已组成羁绊列表
        foreach ($this->inputGid2count as $Gid => $count) {
            $usedCount = app_m_data_Factory::getGid($Gid)->workCount($count);
            if (0 != $usedCount) {
                $this->inputGid2readyCount[$Gid] = $usedCount;
            }
        }

        //当前可用棋子，对应的可用羁绊数量
        $this->getFreeGbyfreeChess();

        //当前可用棋子，对应的羁绊交集数组
        $this->freeChessGidIntersection();
        // return $this->freeChessGidintersection;

        //当前可选羁绊
        $this->canChoseGidList();

        // lib_log::debug('canChoseGidList', array_map(function($v){
        //     $v[usr_def::Gid] = lib_tools::Gid2Name($v[usr_def::Gid]);
        //     return $v;
        // },$this->canChoseGidList));
        // return $this->canChoseGidList;

        /**
         * 从可选羁绊中选出(1--n)个可选羁绊，n=队伍人数+1-已有羁绊，n为队伍人数可选得最大羁绊个数
         * n = $req->teamCount + 1 - count($this->inputGid2readyCount)
         */
        //组合可选羁绊组合
        $this->generateGcombination();

        lib_log::debug('loopCount', lib_number::getCount());
        lib_log::debug('lib_timer', lib_timer::getResult());
        //遍历可选羁绊组合，获取对应棋子组合
        return $this->generateGcombination;
    }

    /**
     * 当前可用羁绊棋子交集，用于计算羁绊组合所需棋子数量
     */
    function freeChessGidIntersection() {
        //遍历所有可用棋子
        foreach ($this->req->chessArrObj as $chessObj) {
            //遍历每两个羁绊，存储交集个数+1
            foreach (lib_tools::choseIterator(array_keys($chessObj->Gids), 2) as $Gtwo) {
                lib_number::addCount(__FUNCTION__);
                //存储羁绊交集对应得chessObj
                lib_array::append($this->freeChessGidIntersection[min($Gtwo[0], $Gtwo[1])][max($Gtwo[0], $Gtwo[1])], $chessObj->chessId);

                // lib_number::addOrDefault($this->freeChessGidintersection[min($Gtwo[0], $Gtwo[1])][max($Gtwo[0], $Gtwo[1])], 1);
            }
        }
    }

    /**
     * 组合可选羁绊
     */
    function generateGcombination() {
        //最终阵容人数 - 已有羁绊个数 = 最大羁绊个数
        $n = $this->req->teamChessCount - count($this->inputGid2readyCount);
        // lib_log::debug(__FUNCTION__.'_n', $n);
        //race和job的数量差不超过2 也就是 abs(jobCount - raceCount) < 2
        $ret = [];
        for ($raceCount = $n - 1; $raceCount >= 0; $raceCount--) {
            for ($jobCount = $n - 1; $jobCount >= 0; $jobCount--) {
                lib_number::addCount(__FUNCTION__ . '_racejob');
                //限制羁绊总数
                if ($raceCount + $jobCount > $n) {
                    lib_number::addCount(__FUNCTION__ . '_racejob_continue_1');
                    continue;
                }
                //限制种族和职业羁绊数量的差值 越小越严格 覆盖越差 性能越好
                if (abs($raceCount - $jobCount) > 1) {
                    lib_number::addCount(__FUNCTION__ . '_racejob_continue_2');
                    continue;
                }
                if (0 === $raceCount && 0 === $jobCount) {
                    continue;
                }
                $ret[] = [
                    'r' => $raceCount,
                    'j' => $jobCount,
                ];
                lib_number::addCount(__FUNCTION__ . '_racejob_result_2');
            }
        }
        // lib_log::debug('canChoseGidList',$this->canChoseGidList);

        //遍历种族和职业得组合
        foreach ($ret as $raceAndJob) {

            lib_number::addCount(__FUNCTION__ . 'loop0');
            // lib_log::debug('generateGcombination:i', $raceAndJob);
            //从可选种族中选出 $raceAndJob['r'] 个，$raceArr中是各种级别
            foreach (lib_tools::choseIterator($this->canChoseGidList[0], $raceAndJob['r']) as $raceArr) {
                // foreach($raceArr as $k => $race){
                //     unset($raceArr[$k]);
                //     $raceArr[$race[usr_def::Gid]] = $race;
                // }
                // //刀妹
                // $daomei = array_key_exists(2, $raceArr) && array_key_exists(5, $raceArr);
                // //卡特
                // $kate = array_key_exists(7, $raceArr) && array_key_exists(12, $raceArr);
                // $num = $daomei + $kate;
                // $raceNeedCount = lib_array::sumBykey($raceArr, usr_def::Gneed);
                // //如果race人数过多，跳出
                // if($raceNeedCount - $num > $this->req->freePosition){
                //     lib_number::addCount(__FUNCTION__.'continue_race');
                //     continue;
                // }

                foreach (lib_tools::choseIterator($this->canChoseGidList[1], $raceAndJob['j']) as $jobArr) {
                    lib_number::addCount(__FUNCTION__ . 'loop1');
                    // foreach($jobArr as $k => $job){
                    //     unset($jobArr[$k]);
                    //     $jobArr[$job[usr_def::Gid]] = $job;
                    // }
                    // //狼人
                    // $langren = array_key_exists(103, $jobArr) && array_key_exists(106, $jobArr);
                    // //肾
                    // $shen = array_key_exists(103, $jobArr) && array_key_exists(106, $jobArr);
                    // $num = $langren + $shen;

                    // $jobNeedCount = lib_array::sumBykey($jobArr, usr_def::Gneed);
                    // //如果job人数过多，跳出
                    // if($jobNeedCount - $num > $this->req->freePosition){
                    //     lib_number::addCount(__FUNCTION__.'continue_job');
                    //     continue;
                    // }

                    $count = $raceAndJob['r'] + $raceAndJob['j'];
                    $Gidarr = array_merge($raceArr, $jobArr);
                    //从子数组中循环选择
                    foreach (lib_tools::choseIteratorArr($Gidarr) as $Gcombination) {

                        lib_number::addCount(__FUNCTION__ . $count . 'loop2');
                        //理论所需个数
                        $needCount = [
                            //race
                            0 => 0,
                            //job
                            1 => 0,
                        ];
                        foreach ($Gcombination as $key => $Gitem) {
                            unset($Gcombination[$key]);
                            $Gcombination[$Gitem[usr_def::Gid]] = $Gitem;
                            $needCount[intval($Gitem[usr_def::Gid] / 100)] += $Gitem[usr_def::Gneed];
                        }
                        //刀妹
                        // $daomei = array_key_exists(2, $Gcombination) && array_key_exists(5, $Gcombination);
                        //卡特
                        // $kate = array_key_exists(7, $Gcombination) && array_key_exists(12, $Gcombination);
                        // $raceNum = $daomei + $kate;
                        // if($needCount[0] - $raceNum > $this->req->freePosition){
                        //     lib_number::addCount(__FUNCTION__.'continue_race');
                        //     continue;
                        // }
                        //狼人
                        // $langren = array_key_exists(103, $jobArr) && array_key_exists(106, $jobArr);
                        //肾
                        // $shen = array_key_exists(103, $jobArr) && array_key_exists(106, $jobArr);
                        // $jobNum = $langren + $shen;
                        // $jobNum = $daomei + $kate;
                        // if($needCount[1] - $jobNum > $this->req->freePosition){
                        //     lib_number::addCount(__FUNCTION__.'continue_job');
                        //     continue;
                        // }
                        //过滤羁绊组合，需要人数!=空闲位置
                        // if(1 == $i && $needCount != $this->req->freePosition){
                        //     //1羁绊情况下，需求个数不为空缺人数，跳出
                        //     lib_number::addCount(__FUNCTION__.'_continue_i=1');
                        //     continue;
                        // }
                        //过滤羁绊组合，需要人数<空闲位置
                        $needCountAll = $needCount[0] + $needCount[1];
                        if ($needCountAll < $this->req->freePosition) {
                            lib_number::addCount(__FUNCTION__ . '_continue_needCount<freePosition');
                            continue;
                        }

                        //多羁绊情况下，计算多羁绊最少需要的人数，若需要的人数大于空缺人数，跳出
                        //羁绊交集棋子列表
                        $combinationArr = $this->GcombinationNeedChessCount($Gcombination, $needCount);
                        if (false === $combinationArr) {
                            lib_number::addCount(__FUNCTION__ . '_continue_GcombinationNeedChessCount false');
                            continue;
                        }
                        //没有共用棋子且没有高级羁绊，跳出
                        $flag = false;
                        foreach ($Gcombination as $Gitem) {
                            if ($Gitem[usr_def::GOPlevel] >= usr_def::Ghigh) {
                                $flag = true;
                                break;
                            }
                        }
                        //没有顶级羁绊且共用结果为空，低质量组合，跳出
                        if (!$flag && empty($combinationArr) && $this->req->teamChessCount >= 3) {
                            lib_number::addCount(__FUNCTION__ . '_continue_lowValue');
                            continue;
                        }

                        //所需个数 != 空缺个数
                        if ($needCountAll - count($combinationArr) != $this->req->freePosition) {
                            lib_number::addCount(__FUNCTION__ . '_continue_needCount not match');
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
                }
            }
            if (self::G_RESULT_COUNT < count($this->generateGcombination)) {
                break;
            }
            // lib_log::debug('generateGcombination:', sprintf("i:%s,count:%s", $i, lib_number::getCount(__FUNCTION__.$i)));
        }
    }

    /**
     * 计算多羁绊交集棋子个数 入参个数最少为2
     */
    function GcombinationNeedChessCount(&$Gcombination, $needCount) {
        lib_number::addCount('call__' . __FUNCTION__);
        lib_number::addCount(__FUNCTION__ . '_count_' . count($Gcombination));
        lib_number::addCount(__FUNCTION__ . '_needCount_' . $needCount);
        //每两个羁绊，计算交集个数
        // if(1 == count($Gcombination)){
        //     return [];
        // }
        lib_timer::start(__FUNCTION__);

        $races = [];
        $jobs = [];
        foreach ($Gcombination as $G) {
            if ($G[usr_def::Gid] > 100) {
                $jobs[] = $G[usr_def::Gid];
            } else {
                $races[] = $G[usr_def::Gid];
            }
        }
        if (empty($races)) {
            return [];
        }

        $combinationArr = [];
        //todo 这里不能随机算，9羁绊情况下遍历次数过多
        // foreach(lib_tools::choseIterator($Gcombination, 2) as $Gtwo){
        foreach ($races as $min) {
            foreach ($jobs as $max) {
                lib_number::addCount(__FUNCTION__);
                // $min = min($Gtwo[0][usr_def::Gid], $Gtwo[1][usr_def::Gid]);
                // $max = max($Gtwo[0][usr_def::Gid], $Gtwo[1][usr_def::Gid]);
                if (!isset($this->freeChessGidIntersection[$min][$max])) {
                    lib_number::addCount(__FUNCTION__ . '_continue_!isset');
                    continue;
                }
                // echo $min.'-'.$max.':'.$this->freeChessGidintersection[$min][$max]."\n";
                foreach ($this->freeChessGidIntersection[$min][$max] as $chessId) {
                    $combinationArr[$chessId] = 1;
                }
                // lib_array::append($combinationArr, $this->freeChessGidintersection[$min][$max]);
                // $combinationArr += $this->freeChessGidintersection[$min][$max];

                //剪枝
                if (count($combinationArr) > $this->req->freePosition) {
                    lib_number::addCount(__FUNCTION__ . '_return_>freePosition');
                    lib_timer::stop(__FUNCTION__);
                    return false;
                }
                if (count($combinationArr) + $this->req->freePosition > $needCount) {
                    lib_number::addCount(__FUNCTION__ . '_return_>needCount');
                    lib_timer::stop(__FUNCTION__);
                    return false;
                }
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
    function canChoseGidList() {
        //遍历所有羁绊
        foreach (array_keys(self::$GidLevelMap) as $Gid) {
            //修正已有羁绊数量
            if (array_key_exists($Gid, $this->inputGid2count)) {
                $existCount = $this->inputGid2count[$Gid];
            } else {
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
            foreach (self::$GidLevelMap[$Gid] as $countIn => $countRet) {
                lib_number::addCount(__FUNCTION__);
                // echo "a,$countIn=>$countRet\n";

                //跳过0位
                if (0 === $countIn) {
                    continue;
                }
                //跳过当前既有羁绊的个数 比如传入3三国，便不再考虑3三国，只考虑更高的级别
                if ($countIn <= $existCount) {
                    continue;
                }
                //当前羁绊所需个数-已有个数 > 当前剩余位置，说明位置不够
                if (($countIn - $existCount) > $this->req->freePosition) {
                    continue;
                }
                //可选羁绊个数 != 有效羁绊个数，这里相当于羁绊的冗余，类似在5魔法师的基础上再加一个魔法师，虽然羁绊不变，但是多了一个享受双重技能的单位，可注释
                //TODO
                if ($countIn != $countRet) {
                    continue;
                }
                //可选羁绊个数 > 当前可用羁绊个数
                if ($countIn > $this->freeGid2count[$Gid]) {
                    continue;
                }
                // 0 为race
                // 1 为job
                if (!isset($this->canChoseGidList[intval($Gid / 100)][$Gid])) {
                    $this->canChoseGidList[intval($Gid / 100)][$Gid] = [];
                }
                //可选羁绊
                $this->canChoseGidList[intval($Gid / 100)][$Gid][] = [
                    // <100 race
                    // >100 job
                    usr_def::Gid => $Gid,
                    // 总数
                    usr_def::Gcount => $countIn,
                    // 需要的个数
                    usr_def::Gneed => $countIn - $existCount,
                    //存储羁绊的稀有度 1234依次变高
                    usr_def::GOPlevel => app_m_object_groups::$opList[$Gid][$countIn],
                ];
                //$Gid;
                // if(isset($canChoseGidList[$countIn])){
                //     $canChoseGidList[$countIn][] = $Gid;
                // }else{
                //     $canChoseGidList[$countIn] = [$Gid];
                // }
            }
        }
        //重排key值
        sort($this->canChoseGidList[0]);
        sort($this->canChoseGidList[1]);
    }

    /**
     * 计算英雄 天选 专职装备合计羁绊的 id=>count
     * @param &$ret
     * @param $chessArr 可用棋子
     * @param $theOne 当前天选
     * @param $weaponArr 当前转职装备
     */
    function getGid2count() {
        $this->inputGid2count = [];
        foreach ($this->req->inChess as $chessId) {
            foreach (app_m_data_Factory::get(usr_def::chess, $chessId)->Gids as $Gid => $Gitem) {
                lib_number::addCount(__FUNCTION__);
                lib_number::addOrDefault($this->inputGid2count[$Gid], 1);
            }
        }
        // if(0 !== $this->req->theOne){
        //     lib_number::addOrDefault($this->inputGid2count[$this->req->theOne], 1);
        // }
        foreach ($this->req->equip as $Gid) {
            lib_number::addCount(__FUNCTION__);
            lib_number::addOrDefault($this->inputGid2count[$Gid], 1);
        }
    }

    /**
     * 根据可用英雄获取可用羁绊个数
     * @param m_object_teamCalcReq $req
     */
    function getFreeGbyfreeChess() {
        $this->freeGid2count = [];
        foreach ($this->req->chessArr as $chessId) {
            foreach (app_m_data_Factory::get(usr_def::chess, $chessId)->Gids as $Gid => $Gitem) {
                lib_number::addCount(__FUNCTION__);
                // var_dump($ret,$Gid);exit;
                lib_number::addOrDefault($this->freeGid2count[$Gid], 1);
            }
        }
    }

    /**
     * 设置参数
     */
    public function setInput($input) {
        lib_log::trace('calcInput', print_r($input, true));
        $this->req = new m_object_teamCalcReq($input);
        $this->req->dealCostList();
        $this->req->dealEquipPre();
        $this->req->getFreeChess();
    }
}