<?php

/**
 * 装备
 */
class m_dao_equip {
    /**
     * 数据key
     *
     * @var string
     */
    const STATIC_KEY = 'equip';
    /**
     * json[data]对象
     *
     * @var object
     */
    static public $data;

    /**
     * 根据Gid找装备
     */
    static public $dataByGid;

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

    static function init() {
        if (!empty(self::$data)) {
            return;
        }
        $retObj = m_dao_base::init(self::STATIC_KEY);
        self::$version = $retObj->version;
        self::$season = $retObj->season;
        self::$time = $retObj->time;

        foreach ($retObj->data as $key => $objItem) {
            if (!self::isValid($objItem->equipId)) {
                continue;
            }
            self::$data[$objItem->equipId] = $objItem;
            if(!empty($objItem->jobId) && $objItem->jobId != '0'){
                self::$dataByGid[$objItem->jobId + 100] = $objItem;
            }
            if(!empty($objItem->raceId) && $objItem->raceId != '0'){
                self::$dataByGid[$objItem->raceId] = $objItem;
            }
        }
    }

    /**
     * @param $equipId
     * @return array
     */
    static public function get($equipId) {
        return self::$data[$equipId];
    }
    static public function getByGid($Gid) {
        return self::$dataByGid[$Gid];
    }
    /**
     * 判断id是否合法
     */
    static public function isValid($id) {
        // Set5.5转职纹章id列表，配置在https://lol.qq.com/tft/js/main.js?v=20210722
        $transJobEquipIdList = [533, 563, 575, 593, 599, 605, 609, 610, 611, 612, 613, 614, 615, 616, 617, 618, 619, 620, 621, 622, 623, 624];
        $id = intval($id);
        if (in_array($id, $transJobEquipIdList)) {
            return true;
        }
        return false;
    }
}