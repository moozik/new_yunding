<?php
/**
 * 一个确定的职业
 * imagePath: "https://game.gtimg.cn/images/lol/act/img/tft/classes/3001.png"
 */
class app_m_object_job extends app_m_object_groups{

    function __construct($jobObj)
    {
        if(is_numeric($jobObj)){
            if($jobObj > usr_def::GID_NUMBER){
                $jobObj -= usr_def::GID_NUMBER;
            }
            $jobObj = app_m_dao_job::$data[$jobObj];
        }
        if(empty($jobObj)){
            return false;
        }
        $this->jobId = $jobObj->jobId;
        $this->GId = $jobObj->jobId + usr_def::GID_NUMBER;
        $this->name = $jobObj->name;
        $this->level = [];
        foreach($jobObj->level as $count => $text){
            $this->level[] = $count;
        }
        $this->GidMap = lib_tools::getLevelMap($jobObj);
        self::colorList($this->GId, $jobObj->job_color_list);
    }
}