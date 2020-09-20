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
    static private $staticKey = 'chess';
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
        $ret = m_dao_base::init(self::$staticKey);
        self::$version = $ret['version'];
        self::$season = $ret['season'];
        self::$time = $ret['time'];

        $newData = [];
        foreach($ret['data'] as $key => $objItem) {
            // $objItem->id = $key + 200;
            $newData[$objItem->chessId] = $objItem;
        }
        self::$data = $newData;
    }
    /**
     * @param int $id
     * @return array
     */
    static public function get($chessId)
    {
        return self::$data[$chessId];
    }
    /**
     * 判断id是否合法
     */
    // static public function isValid($id)
    // {
    //     if($id >= 200 && $id <= 280){
    //         return true;
    //     }
    //     return false;
    // }
}