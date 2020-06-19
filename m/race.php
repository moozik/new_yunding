<?php
/**
 * 一个确定的种族
 * imagePath: "https://game.gtimg.cn/images/lol/act/img/tft/origins/3101.png"
 */
// class m_race extends m_base_base{
class m_race extends m_groups{

    function __construct($raceObj)
    {
        if(is_numeric($raceObj)){
            $raceObj = m_dao_race::get($raceObj);
        }
        $this->id = (int)$raceObj->raceId;
    }

    public function addOne(){
        $this->count++;
        $ret = m_conf::raceWorkCount($this->id, $this->count);
        if($ret === 0){
            $this->isWork = false;
            $this->isWorkCount = 0;
            $ret = m_conf::raceWorkCount($this->id, $this->count + 1);
            if(0 != $ret){
                $this->featureCount = $ret;
            }
        }else{
            $this->isWork = true;
            $this->isWorkCount = $ret;
            $this->value = m_conf::raceValue($this->id, $ret);
            //featureCount归零
            $this->featureCount = 0;
        }
    }
    public function getArray()
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