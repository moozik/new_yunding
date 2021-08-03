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

    /*
    var data = '';
    races.data.forEach((item, index) => {
        groupType = JSON.stringify(Object.keys(item.level).map(function(item){return Number(item);}));
        groupType2 = Object.keys(item.level).join('_');
        data = data + "//" + item.name + "\n" + item.raceId + " => [" + groupType + ",self::BF_" + groupType2 + "],\n";
    });
    console.log(data);

    var data = '';
    jobs.data.forEach((item, index) => {
        groupType = JSON.stringify(Object.keys(item.level).map(function(item){return Number(item);}));
        groupType2 = Object.keys(item.level).join('_');
        data = data + "//" + item.name + "\n" + (parseInt(item.jobId) + 100) + " => [" + groupType + ",self::BF_" + groupType2 + "],\n";
    });
    console.log(data);
    */
    /**
     * races jobs 强度级别标准
     * 6=>(9-12)
     * 9=>(12-15)
     *
     */
    const races = [
        //raceId  展示级别 数字级别
        //破败军团
        1 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4
        ],
        //黑夜使者
        2 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4
        ],
        //魔女
        // 3 => [0,0,3],
        //小恶魔
        4 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4
        ],
        //屠龙勇士
        // 5 => [2,0,4],
        //丧尸 攻略里与游戏里不符
        6 => [
            3 => 1,
            4 => 2,
            5 => 4
        ],
        //圣光卫士
        7 => [
            3 => 1,
            6 => 3,
            9 => 4
        ],
        //黎明使者
        8 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4
        ],
        //神佑之森
        // 9 => [2,0,3],
        //龙族
        10 => [
            3 => 1,
            5 => 3
        ],
        //铁甲卫士
        11 => [
            2 => 1,
            3 => 3,
            4 => 4
        ],
        //永猎双子
        12 => [1 => 3],
        //复生亡魂
        13 => [
            2 => 1,
            3 => 3,
            4 => 4,
            5 => 4
        ],
        //光明哨兵
        14 => [
            3 => 1,
            6 => 3,
            9 => 4
        ],
        //灵罗娃娃
        15 => [1 => 3],
        //神王凯旋
        16 => [1 => 3],
    ];
    const jobs = [
        //jobId  展示级别 数字级别
        //刺客
        101 => [
            2 => 1,
            4 => 2,
            6 => 3
        ],
        //游侠
        102 => [
            2 => 1,
            4 => 3,
            6 => 4
        ],
        //斗士
        103 => [
            2 => 1,
            4 => 3,
            6 => 4
        ],
        //征服者
        104 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4
        ],
        //复苏者
        105 => [
            2 => 1,
            4 => 3,
            6 => 4
        ],
        //神谕者
        106 => [
            2 => 1,
            4 => 3
        ],
        //骑士
        107 => [
            2 => 1,
            4 => 2,
            6 => 3
        ],
        //法师
        108 => [
            2 => 1,
            4 => 3,
            6 => 4
        ],
        //神盾战士
        109 => [
            3 => 1,
            6 => 3,
            9 => 4
        ],
        //秘术师
        110 => [
            2 => 1,
            3 => 2,
            4 => 3,
            5 => 3
        ],
        //重骑兵
        111 => [
            2 => 1,
            3 => 2,
            4 => 3
        ],
        //大魔王
        112 => [1 => 3],
        //神王
        // 113 => [1=>3],
        //驯龙大师
        114 => [1 => 3],
        //强袭炮手
        115 => [
            2 => 1,
            4 => 2,
            6 => 4
        ],
    ];

    /**
     * https://lol.qq.com/tft/js/util.js?v=20210527
     */
    const contactLevelColor = [
        1 => [3 => 1, 6 => 2, 9 => 3],
        2 => [2 => 1, 4 => 2, 6 => 3, 8 => 4],
        3 => [3 => 3],
        4 => [3 => 1, 5 => 3, 7 => 4],
        5 => [2 => 1, 4 => 3],
        6 => [3 => 1, 4 => 2, 5 => 3],
        7 => [3 => 1, 6 => 3, 9 => 4],
        8 => [2 => 1, 4 => 2, 6 => 3, 8 => 4],
        9 => [2 => 1, 3 => 3],
        10 => [3 => 1, 5 => 3],
        11 => [2 => 1, 3 => 3],
        12 => [1 => 3],
        13 => [2 => 1, 3 => 3],
        101 => [2 => 1, 4 => 2, 6 => 3],
        102 => [2 => 1, 4 => 3],
        103 => [2 => 1, 4 => 3],
        104 => [2 => 1, 4 => 2, 6 => 3, 8 => 4],
        105 => [2 => 1, 4 => 3],
        106 => [2 => 1, 4 => 3],
        107 => [2 => 1, 4 => 2, 6 => 3],
        108 => [2 => 1, 4 => 3],
        109 => [3 => 1, 6 => 3],
        110 => [2 => 1, 3 => 2, 4 => 3],
        111 => [2 => 1, 3 => 2, 4 => 3],
        112 => [1 => 3],
        113 => [1 => 3],
        114 => [1 => 3],
    ];

    /**
     * 计算G羁绊的稀有程度
     */
    static function GidOPLevel($Gid, $count) {
        if ($Gid > 100) {
            return self::jobs[$Gid][$count];
        }
        return self::races[$Gid][$count];
    }
    /**
     * 计算G羁绊对应的有效个数
     */
    // static function Gid2Level($Gid, $count = 0){
    //     if($Gid > 100){
    //         return isset(self::jobs[$Gid][1][$count])? self::jobs[$Gid][1][$count]: self::jobs[$Gid][1][0];
    //     }
    //     return isset(self::races[$Gid][1][$count])? self::races[$Gid][1][$count]: self::races[$Gid][1][0];
    // }
    /*
        var data = '';
        chess.data.forEach((item, index) => {
        data = data + item.price + "\t" + item.chessId + "\t" + item.title + "\t" + item.displayName + "\n";
        });
        console.log(data);
     */

    static public function init() {
    }
}