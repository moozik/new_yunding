<?php
/**
 * 英雄
 */
class m_object_chess{
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
     * @param $chessObj
     */
    function __construct($chessObj)
    {
        if(is_numeric($chessObj)){
            $chessObj = m_dao_chess::get($chessObj);
        }
        $this->price = $chessObj->price;
        $this->chessId = $chessObj->chessId;
        $this->name = $chessObj->title . ' ' . $chessObj->displayName;
        
        if(is_int($chessObj->raceIds)){
            $this->raceIds[$chessObj->raceIds] = m_data_Factory::get(lib_def::race, $chessObj->raceIds);
        }else{
            $this->raceIds = array_map(function($var){
                return m_data_Factory::get(lib_def::race, intval($var));
            }, explode(',', $chessObj->raceIds));
        }

        if(is_int($chessObj->jobIds)){
            $this->jobIds[$chessObj->jobIds] = m_data_Factory::get(lib_def::job, $chessObj->jobIds);
        }else{
            $this->jobIds = array_map(function($var){
                return m_data_Factory::get(lib_def::job, intval($var));
            }, explode(',', $chessObj->jobIds));
        }
    }
}