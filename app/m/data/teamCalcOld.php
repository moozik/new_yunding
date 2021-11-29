<?php

class app_m_data_teamCalcOld {
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
    /**
     * job race 羁绊个数对应关系数组
     */
    static public $GidLevelMap = [];
    private $debugData = [
        'teamListCount' => 0,
    ];

    function __construct() {
        self::$GidLevelMap = app_m_dao_job::$GidMap + app_m_dao_race::$GidMap;
    }

    function debugData() {
        if(!SEN::isDevelop()){
            return [];
        }
        return $this->debugData;
    }

    function forCountNew(): Generator {
        if (0 === $this->req->forCount) {
            yield new app_m_object_teamList($this->req->inChess);
        } else {
            foreach (lib_tools::choseIterator($this->req->freeChessArrObj, $this->req->forCount) as $chessArrObj) {
                yield new app_m_object_teamList(array_merge($this->req->inChessObj, $chessArrObj));
            }
        }
    }

    function forCountOld() {
        if ($this->req->forCount === 0) {
            yield ($this->req->inChessObj);
            return;
        }

        foreach ($this->req->freeChessArrObj as $k1 => $chessObj_1) {
            if ($this->req->forCount === 1) {
                //1人口无法匹配巨像
                if($chessObj_1->isTheFat()){
                    continue;
                }
                yield (array_merge($this->req->inChessObj, [$chessObj_1]));
                continue;
            }
            //2人口匹配巨像直接返回
            if($chessObj_1->isTheFat()){
                continue;
            }
            foreach ($this->req->freeChessArrObj as $k2 => $chessObj_2) {
                if ($k2 <= $k1) {
                    continue;
                }
                if ($this->req->forCount === 2) {
                    //2人口只在第一个位置匹配巨像
                    if($chessObj_2->isTheFat()){
                        continue;
                    }
                    yield (array_merge($this->req->inChessObj, [$chessObj_1, $chessObj_2]));
                    continue;
                }
                foreach ($this->req->freeChessArrObj as $k3 => $chessObj_3) {
                    if ($k3 <= $k2) {
                        continue;
                    }
                    //第三个位置不匹配巨像
                    if($chessObj_3->isTheFat()){
                        continue;
                    }
                    yield (array_merge($this->req->inChessObj, [$chessObj_1, $chessObj_2, $chessObj_3]));
                }
            }
        }
    }

    /**
     * 计算阵容
     */
    function calc() {
        //存储队伍价格到数量的映射，用于快速筛选有价值的阵容
        $valueLog = [];

        //结果数组
        $teamList = [];
        $retData = [];
        //筛选出羁绊价值最高的组合
        foreach ($this->forCountOld() as $teamListParam) {
            $teamListObj = new app_m_object_teamList($teamListParam);
            //初步判断是否ok

            //海克斯科技
            if (!empty($this->req->hexTecGid1)){
                lib_number::addOrDefault($teamListObj->group[$this->req->hexTecGid1], 1);
            }
            if (!empty($this->req->hexTecGid3)){
                lib_number::addOrDefault($teamListObj->group[$this->req->hexTecGid3], 2);
            }
            //给当前羁绊计数
            foreach ($teamListObj->chessArrObj as $chessObj) {
                //棋子价值
                $teamListObj->idVal += $chessObj->price;
                //给当前英雄的所有羁绊计数
                foreach ($chessObj->Gids as $Gid => $groupObj) {
                    lib_number::addOrDefault($teamListObj->group[$Gid], 1);
                }
            }
            //处理转职装备
            $this->dealEquip($teamListObj);
            //浪费的羁绊数量，用于剪枝
            $wastCount = 0;
            //遍历羁绊计算羁绊个数
            foreach ($teamListObj->group as $Gid => $count) {
                //形成羁绊的有效英雄个数 类似3剧毒和4剧毒中，3个是有效的 $groupValue=3 $count=4
                $Glevel = &self::$GidLevelMap[$Gid];
                $Gcount = $Glevel[$count];
                //计算浪费羁绊的数量
                $wastCount += $count - $Gcount;
                //如果没有形成羁绊，那么删除当前阵营
                if (0 == $Gcount) {
                    unset($teamListObj->group[$Gid]);
                    //TODO refactry 放到最后
                    if (0 != $Glevel[$count + 1]) {
                        //如果数量+1buff不为0，则加入tips
                        $teamListObj->tips[1] .= '[' . ($count + 1) . app_m_data_Factory::getGid($Gid)->name . '],';
                    }
                    //当前羁绊不成形
                    continue;
                }
                //计算羁绊级别,根据 $Gcount 有效个数 羁绊等级为1234
                $opLevel = usr_conf::racesJobs[$Gid][$Gcount];
                $teamListObj->resultGroup[$opLevel][$Gid] = $Gcount;
                // 羁绊级别 * 羁绊个数 = 羁绊分数
                // 会根据 羁绊分数 来计算阵容强度
                $teamListObj->score += $opLevel * $Gcount;
            }
            //如果普通羁绊和顶级羁绊都为空 那么跳出 20191109修复bug：之判断了普通羁绊没判断顶级羁绊
            // if (empty($teamListObj->resultGroup)) {
            if ($this->req->forCount != 0 && $wastCount > $this->req->teamChessCount) {
                //释放
                unset($teamListObj);
                continue;
            }
            if (!empty($teamListObj->tips[1])) {
                $teamListObj->tips[1] = '即将成型:' . trim($teamListObj->tips[1], ',') . ';';
            }

            //阵容价值=棋子价值+羁绊价值
            $teamListObj->score += $teamListObj->idVal;

            lib_number::addOrDefault($valueLog[$teamListObj->score], 1);
            $teamListObj->tips = implode('', $teamListObj->tips);
            $teamList[] = $teamListObj;
            //分段计算,优化计算量大的case
            //  if (count($teamList) >= 20000) {
            //      $retData = array_merge($retData, $this->calcValueTeam($valueLog, $teamList));
            //      $teamList = [];
            //      $valueLog = [];
            //  }
        }
        if (!empty($teamList)) {
            $retData = array_merge($retData, $this->calcValueTeam($valueLog, $teamList));
        }
        $retData = array_slice(lib_array::sort($retData, 'score'), 0, usr_conf::SHOW_LIMIT);
        return $retData;
    }

    /**
     * 根据$valueLog计算阵容
     *
     * @param $valueLog
     * @param $teamList []app_m_data_teamList
     * @return array
     */
    function calcValueTeam(&$valueLog, &$teamList) {
        $this->debugData['teamListCount'] += count($teamList);
        //根据key降序
        krsort($valueLog);
        //取头部阵容
        $teamCount = 0;
        $divLine = 0;
        foreach ($valueLog as $val => $num) {
            $teamCount += $num;
            //若总和大于预定数量则跳出
            if ($teamCount >= usr_conf::SHOW_LIMIT) {
                $divLine = $val;
                break;
            }
        }
        //取待排序阵容
        $sortTeamList = [];
        foreach ($teamList as $teamListObj) {
            if ($teamListObj->score >= $divLine) {
                foreach ($teamListObj->chessArrObj as $chessObj) {
                    $teamListObj->resultChess[] = $chessObj->chessId;
                }
                $sortTeamList[] = $teamListObj->getArr();
            }
        }
        //排序
        $sortedTeamList = lib_array::sort($sortTeamList, 'score');
        return array_slice($sortedTeamList, 0, usr_conf::SHOW_LIMIT);
    }

    /**
     * 处理转职装备
     * 剔除不合法装备，修正可用装备数量，添加提醒
     */
    function dealEquip(app_m_object_teamList $teamListObj) {
        // lib_timer::start(__FUNCTION__);
        if (empty($this->req->tagPlus)) {
            return;
        }
        //遍历转职装备 并计算能用到的最大装备数量到 $teamListObj->equip[$Gid]
        foreach ($this->req->tagPlusMap as $Gid => $count) {
            if (isset($teamListObj->group[$Gid])) {
                $equipMax = $this->req->teamChessCount - $teamListObj->group[$Gid];
            } else {
                $equipMax = $this->req->teamChessCount;
            }
            if ($equipMax < $count) {
                //装备过多，可用装备等于可装备人数
                $count = $equipMax;
                //获取羁绊名字
                $teamListObj->tips[0] .= app_m_data_Factory::getGid($Gid)->name . ',';
            }
            //羁绊数量经过转职装备修正
            lib_number::addOrDefault($teamListObj->group[$Gid], $count);

            for($i = 0; $i < $count; $i++){
                $teamListObj->equip[] = app_m_dao_equip::getByGid($Gid);
            }
        }
        if (!empty($teamListObj->tips[0])) {
            $teamListObj->tips[0] = '剩余装备:' . trim($teamListObj->tips[0], ',') . ';';
        }
        // lib_timer::stop(__FUNCTION__);
    }

    /**
     * 设置参数
     */
    public function setInput($input) {
        // lib_log::trace('calcInput', print_r($input, true));
        $this->req = new app_m_object_teamCalcReq($input);
        $this->req->dealCostList();
        $this->req->dealEquipPre();
        $this->req->getFreeChess();
        $this->debugData['req'] = $this->req;
    }
}
