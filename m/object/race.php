<?php
/**
 * 一个确定的种族
 * imagePath: "https://game.gtimg.cn/images/lol/act/img/tft/origins/3101.png"
 */
class m_object_race extends m_object_groups{

    function __construct($raceObj)
    {
        if(is_numeric($raceObj)){
            $raceObj = m_dao_race::get($raceObj);
        }
        $this->raceId = $raceObj->raceId;
        $this->name = $raceObj->name;
        $this->level = [];
        foreach($raceObj->level as $count => $text){
            $this->level[] = $count;
        }
    }

    // public function addOne(){
    //     $this->count++;
    //     $ret = lib_conf::raceWorkCount($this->id, $this->count);
    //     if($ret === 0){
    //         $this->isWork = false;
    //         $this->isWorkCount = 0;
    //         $ret = lib_conf::raceWorkCount($this->id, $this->count + 1);
    //         if(0 != $ret){
    //             $this->featureCount = $ret;
    //         }
    //     }else{
    //         $this->isWork = true;
    //         $this->isWorkCount = $ret;
    //         // $this->value = lib_conf::raceValue($this->id, $ret);
    //         //featureCount归零
    //         $this->featureCount = 0;
    //     }
    // }
}