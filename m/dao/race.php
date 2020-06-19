<?php
/**
 * 种族
 */
class m_dao_race{
    /**
     * 数据key
     *
     * @var string
     */
    static private $staticKey = 'race';
    /**
     * json[data]对象
     *
     * @var object
     */
    static private $data;

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
            $newData[$objItem->raceId] = $objItem;
        }
        self::$data = $newData;
    }
    /**
     * 701-710
     * @param int $id
     * @return array
     */
    static public function get($id)
    {
        return self::$data[$id];
    }
    /**
     * 判断id是否合法
     */
    // static public function isValid($id)
    // {
    //     if($id >= 1 && $id <= 10){
    //         return true;
    //     }
    //     return false;
    // }
}