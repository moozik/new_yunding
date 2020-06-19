<?php
/**
 * 装备
 */
class m_dao_equip{
    /**
     * 数据key
     *
     * @var string
     */
    static private $staticKey = 'equip';
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
            if (self::isValid($objItem->equipId)) {
                $newData[$objItem->equipId] = $objItem;
            }
        }
        self::$data = $newData;
    }
    /**
     * @param int $id
     * @return array
     */
    static public function get($equipId)
    {
        return self::$data[$equipId];
    }
    /**
     * 判断id是否合法
     */
    static public function isValid($id)
    {
        if($id >= 301){
            return true;
        }
        return false;
    }
}