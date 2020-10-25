<?php
/**
 * 一个确定的种族
 * imagePath: "https://game.gtimg.cn/images/lol/act/img/tft/origins/3101.png"
 */
class m_object_race extends m_object_groups{

    function __construct($raceObj)
    {
        if(is_numeric($raceObj)){
            $raceObj = m_dao_race::$data[$raceObj];
        }
        $this->raceId = $raceObj->raceId;
        $this->GId = $raceObj->raceId;
        $this->name = $raceObj->name;
        $this->level = [];
        foreach($raceObj->level as $count => $text){
            $this->level[] = $count;
        }
        $this->count2level = lib_conf::races[$this->raceId][1];
    }
}