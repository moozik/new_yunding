<?php
/**
 * 一个确定的装备
 * imagePath: "https://game.gtimg.cn/images/lol/act/img/tft/equip/206.png"
 */
// class m_equip extends m_base_base{
class m_object_equip{
    /**
     * id
     *
     * @var int
     */
    public $id;
    /**
     * 当前装备数量
     *
     * @var int
     */
    public $count;

    /**
     * 当前装备指向职业或种族
     *
     * @var int
     */
    public $jobOrRace = 0;

    /**
     * 当前转职装备有效数量
     *
     * @var int
     */
    public $isWorkCount = 0;

    function __construct($equipObj)
    {
        if(is_numeric($equipObj)){
            $equipObj = m_dao_race::get($equipObj);
        }
        $this->equipId = $equipObj->equipId;
        $this->formula = $equipObj->formula;
        $this->type = $equipObj->type;

        if($equipObj->jobId != '0'){
            $this->jobOrRace = (int)$equipObj->jobId;
        }else if($equipObj->raceId != '0'){
            $this->jobOrRace = (int)$equipObj->raceId;
        }
    }
}