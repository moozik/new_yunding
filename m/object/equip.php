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
    public $equipId;

    /**
     * 当前装备指向职业或种族
     *
     * @var int
     */
    public $jobId = 0;
    public $raceId = 0;
    /**
     * 配方
     */
    public $formula = '';
    /**
     * 名字
     */
    public $name = '';
    public $type = 0;
    public $keywords = '';

    function __construct($equipObj)
    {
        if(is_numeric($equipObj)){
            $equipObj = m_dao_equip::get($equipObj);
        }
        $this->equipId = $equipObj->equipId;
        $this->formula = $equipObj->formula;
        $this->type = $equipObj->type;
        $this->keywords = $equipObj->keywords;
        $this->name = $equipObj->name;

        if($equipObj->jobId != '0'){
            $this->jobId = (int)$equipObj->jobId;
        }else if($equipObj->raceId != '0'){
            $this->raceId = (int)$equipObj->raceId;
        }
    }
}