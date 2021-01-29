<?php

class m_data_teamCalcOld{
    /**
     * @var m_object_teamCalcReq
     */
    public $req;
    /**
     * 分组计算最大值
     *
     * @var integer
     */
    public $teamCaleLimit = 20000;
    //入参羁绊列表 *只读 G->count
    //包括转职
    private $inputGid2count = [];

    //当前可用棋子对应的可用羁绊数量
    private $freeGid2count = [];
    /**
     * job race 羁绊个数对应关系数组
     */
    static public $GidLevelMap = [];

    //羁绊组合结果个数
    const G_RESULT_COUNT = 10;

    function __construct(){
        //初始化dao
        m_dao_race::init();
        m_dao_job::init();
        m_dao_chess::init();
        m_dao_equip::init();
        // self::$GidLevelMap = array_merge(m_dao_job::$GidMap, m_dao_race::$GidMap);
        self::$GidLevelMap = m_dao_job::$GidMap + m_dao_race::$GidMap;
        $this->forCountFunc = 'forCountOld';
    }

    function forCountNew(){
        // lib_timer::start(__FUNCTION__);
        if(0 === $this->req->forCount){
            yield new m_data_teamList($this->req->inChess);
        }else{
            foreach(lib_tools::choseIterator($this->req->freeChessArrObj, $this->req->forCount) as $chessArrObj){
                yield new m_data_teamList(array_merge($this->req->inChessObj, $chessArrObj));
            }
        }
        // lib_timer::stop(__FUNCTION__);
    }
    function forCountOld(){
        // lib_timer::start(__FUNCTION__);
        // $teamList = [];
        if($this->req->forCount === 0){
            yield new m_data_teamList($this->req->inChessObj);
            return;
        }

        foreach($this->req->freeChessArrObj as $k1 => $chessObj_1){
            if($this->req->forCount == 1){
                yield new m_data_teamList(array_merge($this->req->inChessObj, [$chessObj_1]));
                continue;
            }
            foreach($this->req->freeChessArrObj as $k2 => $chessObj_2){
                if($k2 <= $k1)continue;
                if($this->req->forCount == 2){
                    yield new m_data_teamList(array_merge($this->req->inChessObj, [$chessObj_1, $chessObj_2]));
                    continue;
                }
                foreach($this->req->freeChessArrObj as $k3 => $chessObj_3){
                    if($k3 <= $k2)continue;
                    yield new m_data_teamList(array_merge($this->req->inChessObj, [$chessObj_1, $chessObj_2, $chessObj_3]));
                }
            }
        }
        
        // lib_timer::stop(__FUNCTION__);
    }
    function calc(){
        $retData = [];
        // lib_log::debug('$this->req', $this->req);
        // return;
        //输出人数
        $this->retCount = count($this->req->inChess) + $this->req->forCount;


        
        // lib_log::debug('$teamList[0]', $teamList[0]);
        // lib_log::debug('$teamList[0]', $teamList[0]->getArr());
        // exit;

        /**
         *     [forCountNew] => 0.60750102996826
         *     [forCountOld] => 0.12744784355164
         */
        // 新算法更慢一点
        // $this->forCountNew($teamList);
        // 老算法更快
        // $this->forCountOld($teamList);
        // lib_log::debug('timer', lib_timer::getResult());
        // exit;
        $forCountFunc = $this->forCountFunc;
        //存储队伍价格到数量的映射，用于快速筛选有价值的阵容
        $valueLog = [];
        
        //结果数组
        $teamList = [];
        //筛选出羁绊价值最高的组合
        foreach($this->{$forCountFunc}() as $teamListObj){
            //天选之人
            if($this->req->theOne != 0){
                $teamListObj->group[$this->req->theOne] = 1;
            }
            
            //给当前羁绊计数
            foreach($teamListObj->chessArrObj as $k => $chessObj){
                //棋子价值
                $teamListObj->idVal += $chessObj->price;
                //给当前英雄的所有羁绊计数
                foreach($chessObj->Gids as $Gid => $groupObj){
                    lib_number::addOrDefault($teamListObj->group[$Gid], 1);
                }
            }
            //处理转职装备 $teamListObj->weapon
            $this->dealWeapon($teamListObj);
            // lib_log::debug('teamListObj', $teamListObj);
            // exit; no error
            //遍历羁绊计算羁绊个数
            foreach($teamListObj->group as $Gid => $count){
                //形成羁绊的有效英雄个数 类似3剧毒和4剧毒中，3个是有效的 $groupValue=3 $count=4
                $Glevel = &self::$GidLevelMap[$Gid];
                $Gcount = $Glevel[$count];
                //如果没有形成羁绊，那么删除当前阵营
                if(0 == $Gcount){
                    unset($teamListObj->group[$Gid]);
                    if(0 != $Glevel[$count + 1]){
                        //如果数量+1buff不为0，则加入tips
                        $teamListObj->tips[1] .= '['.($count + 1) . m_data_Factory::getGid($Gid)->name . '],';
                    }
                    //当前羁绊不成形
                    continue;
                }
                
                //顶级羁绊加入 K_GROUPTOP 删除原group中的数据
                //mid级羁绊加入 K_GROUPMID
                $opLevel = lib_conf::GidOPLevel($Gid, $count);
                $teamListObj->result->group[$opLevel][$Gid] = $count;
                //(羁绊级别 + 1) * 羁绊个数 = 羁绊分数
                $teamListObj->result->score += ($opLevel + 1) * $Gcount;
            }
            if(!empty($teamListObj->tips[1])){
                $teamListObj->tips[1] = '即将成型:' . trim($teamListObj->tips[1], ',') .';';
            }

            //如果普通羁绊和顶级羁绊都为空 那么跳出 20191109修复bug：之判断了普通羁绊没判断顶级羁绊
            if(empty($teamListObj->result->group)){
                //unset($teamList[$k]);
                continue;
            }
            
            //阵容价值=棋子价值+羁绊价值
            $teamListObj->result->score += $teamListObj->idVal;
            
            lib_number::addOrDefault($valueLog[$teamListObj->result->score], 1);
            $teamListObj->result->tips = implode('', $teamListObj->tips);
            $teamListObj->result->op = (string)round($teamListObj->result->score / $this->retCount,2);

            $teamList[] = $teamListObj;
            if(count($teamList) >= $this->teamCaleLimit){
                $retData = array_merge($retData, $this->calcValueTeam($valueLog, $teamList));
                $teamList = [];
                $valueLog = [];
                echo "retData:". count($retData). "\n";
            }
        }
        $retData = array_merge($retData, $this->calcValueTeam($valueLog, $teamList));
        return $retData;
    }

    /**
     * 根据$valueLog计算阵容
     *
     * @return void
     */
    function calcValueTeam(&$valueLog, &$teamList){
        krsort($valueLog);
        //取头部阵容
        $teamCount = 0;
        $divLine = 0;
        foreach($valueLog as $val => $num){
            $teamCount += $num;
            //若总和大于预定数量则跳出
            if($teamCount >= lib_conf::SHOW_LIMIT){
                $divLine = $val;
                break;
            }
        }
        lib_log::debug('divLine', $divLine);
        //取待排序阵容
        $sortTeamList = [];
        foreach($teamList as $teamListObj){
            if($teamListObj->result->score >= $divLine){
                foreach($teamListObj->chessArrObj as $chessObj){
                    $teamListObj->result->chess[] = $chessObj->chessId;
                }
                $sortTeamList[] = $teamListObj->getArr();
            }
        }
        //排序
        $sortedTeamList = $this->array_sort($sortTeamList, 'score');
        return array_slice($sortedTeamList, 0, lib_conf::SHOW_LIMIT);
    }

    /**
     * 处理转职装备
     * 剔除不合法装备，修正可用装备数量，添加提醒
     */
    function dealWeapon($teamListObj){
        // lib_timer::start(__FUNCTION__);
        if(empty($this->req->weapon)){
            return;
        }
        //遍历转职装备 并计算能用到的最大装备数量到 $teamListObj->weapon[$Gid]
        foreach($this->req->weapon as $Gid => $count){
            if(isset($teamListObj->group[$Gid])){
                $teamListObj->weapon[$Gid] = $this->retCount - $teamListObj->group[$Gid];
            }else{
                $teamListObj->weapon[$Gid] = $this->retCount;
            }
            if($teamListObj->weapon[$Gid] < $count){
                //装备过多，可用装备等于可装备人数
                $count = $teamListObj->weapon[$Gid];
                //获取羁绊名字
                $teamListObj->tips[0] .= m_data_Factory::getGid($Gid)->name . ',';
            }
            //羁绊数量经过转职装备修正
            lib_number::addOrDefault($teamListObj->group[$Gid], $count);
        }
        if(!empty($teamListObj->tips[0])){
            $teamListObj->tips[0] = '剩余装备:' . trim($teamListObj->tips[0],',').';';
        }
        // lib_timer::stop(__FUNCTION__);
    }
    /**
     * 排序函数
     *
     * @param [array] $arr
     * @param [mixed] $keys
     * @param string $orderby
     * @return array
     */
    function array_sort($arr, $keys, $orderby = 'desc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v){
            $keysvalue[$k] = $v[$keys];
        }
        if($orderby == 'asc'){
            asort($keysvalue);
        }else if($orderby == 'desc'){
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v){
            $new_array[] = $arr[$k];
        }
        return $new_array;
    }
    /**
     * 计算英雄 天选 专职装备合计羁绊的 id=>count
     * @param &$ret
     * @param $chessArr 可用棋子
     * @param $theOne 当前天选
     * @param $weaponArr 当前转职装备
     */
    function getGid2count(){
        $this->inputGid2count = [];
        foreach($this->req->inChess as $chessId){
            foreach(m_data_Factory::get(lib_def::chess, $chessId)->Gids as $Gid => $Gitem){
                lib_number::addCount(__FUNCTION__);
                lib_number::addOrDefault($this->inputGid2count[$Gid], 1);
            }
        }
        if(0 !== $this->req->theOne){
            lib_number::addOrDefault($this->inputGid2count[$this->req->theOne], 1);
        }
        foreach($this->req->weapon as $Gid){
            lib_number::addCount(__FUNCTION__);
            lib_number::addOrDefault($this->inputGid2count[$Gid], 1);
        }
    }

    /**
     * 根据可用英雄获取可用羁绊个数
     * @param m_object_teamCalcReq $req
     */
    function getFreeGbyfreeChess(){
        $this->freeGid2count = [];
        foreach($this->req->chessArr as $chessId){
            foreach(m_data_Factory::get(lib_def::chess, $chessId)->Gids as $Gid => $Gitem){
                lib_number::addCount(__FUNCTION__);
                lib_number::addOrDefault($this->freeGid2count[$Gid], 1);
            }
        }
    }

    /**
     * 设置参数
     */
    public function setInput($input){
        lib_log::trace('calcInput', print_r($input, true));
        $this->req = new m_object_teamCalcReq($input);
        $this->req->dealCostList();
        $this->req->dealWeaponPre();
        $this->req->getFreeChess();
    }
}