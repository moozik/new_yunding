<?php

/**
 * 职业
 */
class app_m_dao_hex {
    /**
     * 数据key
     *
     * @var string
     */
    const STATIC_KEY = 'hex';
    /**
     * json[data]对象
     *
     * @var array
     */
    static public $data = [];

    static function init() {
        if (!empty(self::$data)) {
            return;
        }
        $retObj = app_m_dao_base::init(self::STATIC_KEY);
        foreach ($retObj as $objItem) {
            self::$data[$objItem->hexId] = $objItem;
        }
    }
}