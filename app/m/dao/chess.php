<?php

/**
 * 英雄
 */
class app_m_dao_chess {
    /**
     * 数据key
     *
     * @var string
     */
    const STATIC_KEY = 'chess';
    /**
     * 龙神
     */
    //const DRAGON_LIST = ['3001','3002','10201','10202','13601','102','136'];
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

    static function init() {
        if (!empty(self::$data)) {
            return;
        }
        $retObj = app_m_dao_base::init(self::STATIC_KEY);
        self::$version = $retObj->version;
        self::$season = $retObj->season;
        self::$time = $retObj->time;

        foreach ($retObj->data as $key => $objItem) {
            //if (in_array($objItem->chessId, self::DRAGON_LIST)) {
            //    // 龙神英雄额外按照羁绊添加
            //    $ids = $objItem->jobIds . ',' . $objItem->raceIds;
            //    $origin_chess_id = $objItem->chessId;
            //    foreach(explode(',', $ids) as $id_item) {
            //        if ($id_item == '7102' || $id_item == '7015') {
            //            continue;
            //        }
            //        $newChessId = $origin_chess_id . '_' . $id_item;
            //        $newObjItem = clone $objItem;
            //        $newObjItem->chessId = $newChessId;
            //        $newObjItem->isDragonGod = true;
            //        $newObjItem->dragonGodId = (int)$id_item;
            //        self::$data[$newObjItem->chessId] = $newObjItem;
            //    }
            //} else {
                $objItem->isDragonGod = false;
                self::$data[$objItem->chessId] = $objItem;
            //}
        }
    }

    /**
     * @param $chessId
     * @return object
     */
    static public function get($chessId) {
        return self::$data[$chessId];
    }
}