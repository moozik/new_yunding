<?php
/**
 * 一种海克斯科技
 */
class app_m_object_hex {

    function __construct($hexId)
    {
        $hexObj = app_m_dao_hex::$data[$hexId];
        if(empty($hexObj)){
            return false;
        }
        $this->hexId = $hexObj->hexId;
        $this->id = $hexObj->id;
        $this->name = $hexObj->name;
        $this->type = $hexObj->type;
        $this->Gid = $this->getGid();
    }

    public function getGid(){
        if ($this->hexId == '127') {
            $this->name = '冒险家';
        }
        foreach (app_m_dao_job::$data as $jobItem) {
            if (strpos($this->name, $jobItem->name) === 0) {
                return $jobItem->jobId + usr_def::GID_NUMBER;
            }
        }
        foreach (app_m_dao_race::$data as $raceItem) {
            if (strpos($this->name, $raceItem->name) === 0) {
                return $raceItem->raceId;
            }
        }
        throw new Exception(sprintf("参数异常,hexName:%s,id:%d", $this->name, $this->hexId));
    }
}