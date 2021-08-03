<?php

/**
 * 每个棋子 职业 种族只存储一个对象
 */
class app_m_data_Factory {

    /**
     * 存储对象
     */
    static $instance = [
        usr_def::chess => [],
        usr_def::job => [],
        usr_def::race => [],
        usr_def::equip => [],
    ];

    /**
     * 返回对象
     * @return object
     */
    static function get($key, $id) {
        if (array_key_exists($id, self::$instance[$key])) {
            return self::$instance[$key][$id];
        }
        switch ($key) {
            case usr_def::chess:
                self::$instance[$key][$id] = new app_m_object_chess($id);
                break;
            case usr_def::job:
                self::$instance[$key][$id] = new app_m_object_job($id);
                break;
            case usr_def::race:
                self::$instance[$key][$id] = new app_m_object_race($id);
                break;
            case usr_def::equip:
                self::$instance[$key][$id] = new app_m_object_equip($id);
                break;
        }
        return self::$instance[$key][$id];
    }

    /**
     * Gid工厂
     * @param $Gid
     * @return app_m_object_groups
     */
    static function getGid($Gid): app_m_object_groups {
        if ($Gid > 100) {
            $key = usr_def::job;
        }
        $key = usr_def::race;
        if (array_key_exists($Gid, self::$instance[$key])) {
            return self::$instance[$key][$Gid];
        }
        self::$instance[$key][$Gid] = $Gid > 100 ? new app_m_object_job($Gid) : new app_m_object_race($Gid);
        return self::$instance[$key][$Gid];
    }

    /**
     * 返回数组
     * @return array
     */
    static function getRaceArr($key, $id) {
        if (is_int($id) || (is_string($id) && is_numeric($id))) {
            if (self::get($key, intval($id))) {
                return [$id => self::get($key, intval($id))];
            }
        }
        if (is_string($id)) {
            $ret = [];
            foreach (explode(',', $id) as $idItem) {
                if (isset(app_m_dao_race::$data[$idItem])) {
                    $ret[$idItem] = self::get($key, intval($idItem));
                }
            }
            return $ret;
        }
    }

    /**
     * 返回数组
     * @return array
     */
    static function getJobArr($key, $id) {
        if (is_int($id) || (is_string($id) && is_numeric($id))) {
            if (self::get($key, intval($id))) {
                return [($id + 100) => self::get($key, intval($id))];
            }
        }
        if (is_string($id)) {
            $ret = [];
            foreach (explode(',', $id) as $idItem) {
                if (isset(app_m_dao_job::$data[$idItem])) {
                    $ret[($idItem + 100)] = self::get($key, intval($idItem));
                }
            }
            return $ret;
        }
    }
}