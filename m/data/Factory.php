<?php
/**
 * 每个棋子 职业 种族之存储一个对象
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
        return self::$instence[$key][$id];
    }
}