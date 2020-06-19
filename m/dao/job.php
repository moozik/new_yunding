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
        $ret = m_dao_base::init(self::$staticKey);
        self::$version = $ret['version'];
        self::$season = $ret['season'];
        self::$time = $ret['time'];

        $newData = [];
        foreach($ret['data'] as $key => $objItem) {
            //重新设置jobid为801起始
            // $objItem->id = $objItem->jobId + 800;
            $newData[$objItem->jobId] = $objItem;
        }
        self::$data = $newData;
    }
    /**
     * 801-813
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
    //     if($id >= 1 && $id <= 13){
    //         return true;
    //     }
    //     return false;
    // }
}