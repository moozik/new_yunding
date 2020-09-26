<?php
/**
 * 一个确定的职业
 * imagePath: "https://game.gtimg.cn/images/lol/act/img/tft/classes/3001.png"
 */
class m_object_job extends m_object_groups{

    function __construct($jobObj)
    {
        if(is_numeric($jobObj)){
            $jobObj = m_dao_job::get($jobObj);
        }
        $this->jobId = $jobObj->jobId;
        $this->name = $jobObj->name;
        $this->level = [];
        foreach($jobObj->level as $count => $text){
            $this->level[] = $count;
        }
    }

    // public function addOne(){
    //     $this->count++;
    //     $ret = lib_conf::jobWorkCount($this->id, $this->count);
    //     if($ret === 0){
    //         $this->isWork = false;
    //         $this->isWorkCount = 0;
    //         $ret = lib_conf::jobWorkCount($this->id, $this->count + 1);
    //         if(0 != $ret){
    //             $this->featureCount = $ret;
    //         }
    //     }else{
    //         $this->isWork = true;
    //         $this->isWorkCount = $ret;
    //         // $this->value = lib_conf::jobValue($this->id, $ret);
    //         //featureCount归零
    //         $this->featureCount = 0;
    //     }
    // }
}