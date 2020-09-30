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
        $this->count2level = CONF::jobs[$this->jobId][1];
    }
}