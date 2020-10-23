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
     * @var array
     */
    static public $data = [];

    static public $GidMap = [];
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
        $retObj = m_dao_base::init(self::$staticKey);
        self::$version = $retObj->version;
        self::$season = $retObj->season;
        self::$time = $retObj->time;

        foreach($retObj->data as $key => $objItem) {
            self::$data[$objItem->raceId] = $objItem;
            self::$GidMap[$objItem->raceId] = lib_tools::getLevelMap($objItem);
        }
        // sen::debugLog('race GidMap', self::$GidMap);
    }
}