<?php
/**
 * 英雄
 */
class m_dao_chess{
    /**
     * 数据key
     *
     * @var string
     */
    const STATIC_KEY = 'chess';
    /**
     * json[data]对象
     *
     * @var object
     */
    static public $data;

    /**
     * 版本
     *
     * @var string
     */
    static public $version;
    /**
     * 赛季
     *
     * @var string
     */
    static public $season;
    /**
     * 更新时间
     *
     * @var string
     */
    static public $time;

    static function init()
    {
        if(!empty(self::$data)){
            return;
        }
        $retObj = m_dao_base::init(self::STATIC_KEY);
        self::$version = $retObj->version;
        self::$season = $retObj->season;
        self::$time = $retObj->time;

        foreach($retObj->data as $key => $objItem) {
            self::$data[$objItem->chessId] = $objItem;
        }
    }
    /**
     * @param int $id
     * @return object
     */
    static public function get($chessId)
    {
        return self::$data[$chessId];
    }
}