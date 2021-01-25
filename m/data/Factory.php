<?php
/**
 * 每个棋子 职业 种族只存储一个对象
 */
class m_data_Factory{

    /**
     * 存储对象
     */
    static $instence = [
        lib_def::chess => [],
        lib_def::job => [],
        lib_def::race => [],
        lib_def::equip => []
    ];
    /**
     * 返回对象
     * @return object
     */
    static function get(int $key, int $id) : object{
        if(array_key_exists($id, self::$instence[$key])){
            return self::$instence[$key][$id];
        }
        switch($key){
            case lib_def::chess:
                self::$instence[$key][$id] = new m_object_chess($id);break;
            case lib_def::job:
                self::$instence[$key][$id] = new m_object_job($id);break;
            case lib_def::race:
                self::$instence[$key][$id] = new m_object_race($id);break;
            case lib_def::equip:
                self::$instence[$key][$id] = new m_object_equip($id);break;
        }
        if(empty(self::$instence[$key][$id])){
            unset(self::$instence[$key][$id]);
            return false;
        }
        return self::$instence[$key][$id];
    }
    /**
     * Gid工厂
     * @return m_object_groups
     */
    static function getGid(int $Gid) : object{
        if($Gid > 100){
            $key = lib_def::job;
        }
        $key = lib_def::race;
        if(array_key_exists($Gid, self::$instence[$key])){
            return self::$instence[$key][$Gid];
        }
        self::$instence[$key][$Gid] = $Gid > 100 ? new m_object_job($Gid) : new m_object_race($Gid);
        return self::$instence[$key][$Gid];
    }
    /**
     * 返回数组
     * @return array
     */
    static function getArr(int $key, $id) : array{
        if(is_int($id) || (is_string($id) && is_numeric($id))){
            if(self::get($key, intval($id)))
                return [$id => self::get($key, intval($id))];
        }
        if(is_string($id)){
            $ret = [];
            foreach(explode(',', $id) as $idItem){
                if(self::get($key, intval($idItem)))
                    $ret[$idItem] = self::get($key, intval($idItem));
            }
            return $ret;
        }
    }
    /**
     * 返回数组
     * @return array
     */
    static function getJobArr(int $key, $id) : array{
        if(is_int($id) || (is_string($id) && is_numeric($id))){
            if(self::get($key, intval($id)))
                return [($id + 100) => self::get($key, intval($id))];
        }
        if(is_string($id)){
            $ret = [];
            foreach(explode(',', $id) as $idItem){
                if(self::get($key, intval($idItem)))
                    $ret[($idItem + 100)] = self::get($key, intval($idItem));
            }
            return $ret;
        }
    }
}