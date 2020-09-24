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
     * 存储棋子对象
     * @var array
     */
    private static $instence = [];
    
    /**
     * @param $chessObj
     */
    function __construct()
    {
        foreach(m_dao_chess::$data as $chessId => $chessObj){
            print_r($chessObj);
            self::$instence[$chessId] = new self();
            self::$instence[$chessId]->chessId = $chessObj->chessId;
            self::$instence[$chessId]->price = $chessObj->price;

            if(is_int($chessObj->raceIds)){
                self::$instence[$chessId]->raceIds = [(int)$chessObj->raceIds];
            }else{
                self::$instence[$chessId]->raceIds = array_map(function($var){
                    return (int)$var;
                }, explode(',', $chessObj->raceIds));
            }

            if(is_int($chessObj->jobIds)){
                self::$instence[$chessId]->jobIds = [(int)$chessObj->jobIds];
            }else{
                self::$instence[$chessId]->jobIds = array_map(function($var){
                    return (int)$var;
                }, explode(',', $chessObj->jobIds));
            }
        }
    }
    static function getInstence($chessId){
        return self::$instence[$chessId];
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