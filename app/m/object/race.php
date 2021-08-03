<?php
/**
 * 一个确定的种族
 * imagePath: "https://game.gtimg.cn/images/lol/act/img/tft/origins/3101.png"
 */
class app_m_object_race extends app_m_object_groups{

    function __construct($raceObj)
    {
        if(is_numeric($raceObj)){
            $raceObj = app_m_dao_race::$data[$raceObj];
        }
        if(empty($raceObj)){
            return false;
        }
        $this->raceId = $raceObj->raceId;
        $this->GId = $raceObj->raceId;
        $this->name = $raceObj->name;
        $this->level = [];
        if(empty($raceObj->level)){
            //汕海绘卷null
            $this->level[] = 1;
        }else 
        foreach($raceObj->level as $count => $text){
            $this->level[] = $count;
        }
        $this->GidMap = lib_tools::getLevelMap($raceObj);
    }
}