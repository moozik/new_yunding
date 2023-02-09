<?php

/**
 * 装备
 */
class app_m_dao_equip {
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
        $retObj = app_m_dao_base::init(self::STATIC_KEY);
        self::$version = $retObj->version;
        self::$season = $retObj->season;
        self::$time = $retObj->time;

        foreach ($retObj->data as $key => $objItem) {
            //if (!self::isValid($objItem->equipId)) {
            //    continue;
            //}
            //if ($objItem->equipId == "8010") {
            //    echo json_encode($objItem);
            //    exit;
            //}
            if ($objItem->isShow == "0") {
                continue;
            }
            self::$data[$objItem->equipId] = $objItem;
            if(!empty($objItem->jobId) && $objItem->jobId != '0'){
                self::$dataByGid[$objItem->jobId + usr_def::GID_NUMBER] = $objItem;
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
        $id = intval($id);
        if ($id >= 7000) {
            return true;
        }
        return false;
    }
}