<?php

/**
 * 配置定义
 */
class usr_conf {


    /**
     * 展示个数
     */
    const SHOW_LIMIT = 20;
    /**
     * 支持的最多人数 count($inHero)的最大值为
     */
    const IN_HERO_MAX = 10;
    /**
     * 支持的最多人数 count($inHero)的最大值为
     */
    const OUT_TEAM_MAX = 10;

    const LEVEL2COST = [
        1 => [1],
        2 => [1],
        3 => [1, 2],
        4 => [1, 2, 3],
        5 => [1, 2, 3, 4],
        6 => [1, 2, 3, 4],
        7 => [1, 2, 3, 4, 5],
        8 => [1, 2, 3, 4, 5],
        9 => [1, 2, 3, 4, 5],
        10 => [1, 2, 3, 4, 5],
    ];
    /**
     * 计算G羁绊的稀有程度
     */
    static function GidOPLevel($Gid, $count) {
        return app_m_object_groups::$opList[$Gid][$count];
    }
}
