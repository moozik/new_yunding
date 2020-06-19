<?php
/**
 * 英雄
 */
// class m_chess extends m_base_base{
class m_chess{
    /**
     * id
     *
     * @var int
     */
    public $id;
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
    public $raceIds;
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
        $this->id = $chessObj->id;
        $this->price = $chessObj->price;
        $this->raceIds = $chessObj->raceIds;

        if(is_numeric($chessObj->jobIds)){
            $this->jobIds = [$chessObj->jobIds];
        }else{
            $this->jobIds = array_map(function($var){
                return (int)$var;
            }, explode(',', $chessObj->jobIds));
        }
    }

    function getArray()
    {
        $ret = [
            'id'=>$this->id,
            'price'=>$this->price,
            'raceIds'=>$this->raceIds,
            'jobIds'=>$this->jobIds,
        ];
        return $ret;
    }
}