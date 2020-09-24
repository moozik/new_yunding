<?php
/**
 * 一个确定的职业
 * imagePath: "https://game.gtimg.cn/images/lol/act/img/tft/classes/3001.png"
 */
// class m_job extends m_base_base{
class m_job extends m_groups{

    function __construct($jobObj)
    {
        if(is_numeric($jobObj)){
            $jobObj = m_dao_job::get($jobObj);
        }
        $this->id = (int)$jobObj->jobId;
    }

    public function addOne(){
        $this->count++;
        $ret = lib_conf::jobWorkCount($this->id, $this->count);
        if($ret === 0){
            $this->isWork = false;
            $this->isWorkCount = 0;
            $ret = lib_conf::jobWorkCount($this->id, $this->count + 1);
            if(0 != $ret){
                $this->featureCount = $ret;
            }
        }else{
            $this->isWork = true;
            $this->isWorkCount = $ret;
            $this->value = lib_conf::jobValue($this->id, $ret);
            //featureCount归零
            $this->featureCount = 0;
        }
    }
    function getArray()
    {
        $ret = [
            'id'=>$this->id,
            'count'=>$this->count,
            'isWork'=>$this->isWork,
            'featureCount'=>$this->featureCount,
        ];
        return $ret;
    }
}