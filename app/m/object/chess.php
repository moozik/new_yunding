<?php
/**
 * 英雄
 */
class app_m_object_chess{
    /**
     * id
     *
     * @var int
     */
    public $chessId;
    /**
     * 价格 1-5
     * 
     * @var int
     */
    public $price;
    /**
     * 阵营
     * 
     * @var int
     */
    public $raceIds = [];
    /**
     * 职业
     * 可有多个职业
     *
     * @var array
     */
    public $jobIds = [];

    /**
     * 存储种族和职业
     * races jobs + 100
     */
    public $Gids = [];
    /**
     * 推荐装备
     * 
     * @var array
     */
//    public $equipIds = [];
    
    /**
     * @param $chessObj
     */
    function __construct($chessObj)
    {
        if(is_numeric($chessObj)){
            $chessObj = app_m_dao_chess::$data[$chessObj];
        }
        if(empty($chessObj)){
            return false;
        }
        $this->price = $chessObj->price;
        $this->chessId = $chessObj->chessId;
        $this->name = $chessObj->title . ' ' . $chessObj->displayName;
        
        $this->raceIds = app_m_data_Factory::getRaceArr(usr_def::race, $chessObj->raceIds);
        $this->jobIds = app_m_data_Factory::getJobArr(usr_def::job, $chessObj->jobIds);
        $this->Gids = $this->raceIds + $this->jobIds;
        // $this->equipIds = app_m_data_Factory::getArr(usr_def::equip, $chessObj->recEquip);
    }
}