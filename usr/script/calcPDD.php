<?php
ini_set('memory_limit','1024M');
require_once 'sen.php';
SEN::init();


// function forCountNew(){
//     $arr = ['a','b','c','d','e','f','g','h','i','j'];
//     foreach(lib_tools::choseIterator($arr, 4) as $chessArrObj){
//         yield implode(',',$chessArrObj);
//     }
// }

// foreach(forCountNew() as $item){
//     echo $item."\n";
// }

// exit;

class calcPdd extends app_m_data_teamCalcOld{
    function calc(){
        $retData = [];
        //输出人数
        $this->retCount = count($this->req->inChess) + $this->req->forCount;
        $forCountFunc = $this->forCountFunc;
        //存储队伍价格到数量的映射，用于快速筛选有价值的阵容
        $valueLog = [];
        //结果数组
        $teamList = [];
        $i = 0;
        //筛选出羁绊价值最高的组合
        foreach($this->{$forCountFunc}() as $teamListObj){
            if(++$i % 2000 == 0){
                echo $i."\n";
            }

            //给当前羁绊计数
            foreach($teamListObj->chessArrObj as $k => $chessObj){
                //棋子价值
                // $teamListObj->idVal += $chessObj->price;
                //给当前英雄的所有羁绊计数
                foreach($chessObj->Gids as $Gid => $groupObj){
                    if($teamListObj->group[$Gid]){
                        $teamListObj->group[$Gid]++;
                    }else{
                        $teamListObj->group[$Gid] = 1;
                    }
                }
            }
            //羁绊太多，不可能凑成
            if(count($teamListObj->group) >= $this->retCount + 3){
                continue;
            }
            //羁绊太少，不符合要求
            if(count($teamListObj->group) <= $this->retCount - 2){
                continue;
            }
            //天选之人
            if($this->req->theOne != 0){
                lib_number::addOrDefault($teamListObj->group[$this->req->theOne], 1);
                //天选之人羁绊必须刚刚好
                if(!in_array($teamListObj->group[$this->req->theOne], app_m_data_Factory::getGid($this->req->theOne)->level)){
                    continue;
                }
            }
            

            //处理转职装备 $teamListObj->weapon
            $this->dealEquip($teamListObj);
            //遍历羁绊计算羁绊个数
            foreach($teamListObj->group as $Gid => $count){
                $Gcount = self::$GidLevelMap[$Gid][$count];
                //如果没有形成羁绊，那么删除当前阵营
                if(0 == $Gcount){
                    //当前羁绊不成形
                    continue;
                }
                $opLevel = usr_conf::GidOPLevel($Gid, $count);
                $teamListObj->result->group[$opLevel][$Gid] = $count;
                //拼多多阵容，只计算羁绊个数
                $teamListObj->result->score += 1;
            }

            //如果羁绊为空 跳出
            if($teamListObj->result->score < $this->retCount - 2){
                continue;
            }
            
            //阵容价值=棋子价值+羁绊价值
            // $teamListObj->result->score += $teamListObj->idVal;
            
            lib_number::addOrDefault($valueLog[$teamListObj->result->score], 1);
            // $teamListObj->result->tips = implode('', $teamListObj->tips);
            // $teamListObj->result->op = (string)round($teamListObj->result->score / $this->retCount,2);

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
    public function setInput($input){
        lib_log::trace('calcInput', print_r($input, true));
        $this->req = new app_m_object_teamCalcReq($input);
        // $this->req->dealCostList();
        $this->req->dealEquipPre();
        $this->req->getFreeChess();
    }
}


$input = '{"theOne":0,"forCount":6,"inChess":[],"banChess":[],"weapon":[],"costList":[1,2,3]}';

$pdd = new calcPdd;
$pdd->setInput(json_decode($input));
$pdd->forCountFunc = 'forCountNew';
$pdd->teamCaleLimit = 10000;


// echo lib_tools::m_chose_n(count($pdd->req->freeChessArr), $pdd->req->forCount);exit;

$result = $pdd->calc();
file_put_contents("./result", lib_string::encode($result));
echo lib_string::encode($result);