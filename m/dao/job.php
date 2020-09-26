<?php
/**
 * 职业
 */
class m_dao_job{
    /**
     * 数据key
     *
     * @var string
     */
    static private $staticKey = 'job';
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
        $retObj = m_dao_base::init(self::$staticKey);
        self::$version = $retObj->version;
        self::$season = $retObj->season;
        self::$time = $retObj->time;

        $newData = [];
        foreach($retObj->data as $key => $objItem) {
            $newData[$objItem->jobId] = $objItem;
        }
        self::$data = $newData;
    }
    /**
     * @param int $id
     * @return array
     */
    static public function get($id)
    {
        return self::$data[$id];
    }
}