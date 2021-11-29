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
        var levelId = 0;
        levelMap = Object.keys(item.level).map(function(item){return "    "+item+" => "+(++levelId)+",\n";}).join('');
        data = data + "//" + item.name + "\n" + item.raceId + " => [\n" + levelMap + "],\n";
    });
    console.log(data);

    var data = '';
    jobs.data.forEach((item, index) => {
        var levelId = 0;
        levelMap = Object.keys(item.level).map(function(item){return "    "+item+" => "+(++levelId)+",\n";}).join('');
        data = data + "//" + item.name + "\n" + (parseInt(item.jobId) + 100) + " => [\n" + levelMap + "],\n";
    });
    console.log(data);
    */
    /**
     * races jobs
     */
    const racesJobs = [
        //raceId  展示级别 数字级别
        //战斗学院
        1 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4,
        ],
        //炼金科技
        2 => [
            3 => 1,
            5 => 2,
            7 => 3,
            9 => 4,
        ],
        //精密发条
        3 => [
            2 => 1,
            4 => 3,
            6 => 4,
        ],
        //执法官
        4 => [
            2 => 2,
            4 => 3,
        ],
        //变异战士
        5 => [
            3 => 2,
            5 => 3,
        ],
        //帝国
        6 => [
            3 => 2,
            5 => 3,
        ],
        //赏金猎人
        7 => [
            3 => 2,
            5 => 3,
            7 => 4,
        ],
        //极客
        8 => [
            2 => 1,
            4 => 3,
            6 => 4,
        ],
        //社交名流
        9 => [
            1 => 1,
            2 => 3,
            3 => 4,
        ],
        //辛迪加
        10 => [
            3 => 1,
            5 => 3,
            7 => 4,
        ],
        //约德尔人
        11 => [
            3 => 2,
            6 => 3,
        ],
        //姐妹
        12 => [
            2 => 4,
        ],
        //食神
        13 => [
            1 => 3,
        ],
        //猫猫
        14 => [
            1 => 3,
        ],
        //约德尔大王
        15 => [
            1 => 4,
        ],

        //jobId  展示级别 数字级别
        //黑魔法师
        101 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4,
        ],
        //刺客
        102 => [
            2 => 1,
            4 => 3,
            6 => 4,
        ],
        //格斗家
        103 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4,
        ],
        //保镖
        104 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4,
        ],
        //巨像
        105 => [
            1 => 2,
            2 => 3,
        ],
        //挑战者
        106 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4,
        ],
        //白魔法师
        107 => [
            2 => 1,
            4 => 2,
            6 => 3,
            8 => 4,
        ],
        //发明家
        108 => [
            3 => 1,
            5 => 3,
            7 => 4,
        ],
        //圣盾使
        109 => [
            2 => 1,
            3 => 2,
            4 => 3,
            5 => 4,
        ],
        //学者
        110 => [
            2 => 1,
            4 => 3,
            6 => 3,
        ],
        //狙神
        111 => [
            2 => 1,
            4 => 3,
            6 => 4,
        ],
        //枪手
        112 => [
            2 => 1,
            4 => 3,
            6 => 4,
        ],
        //未来守护者
        113 => [
            1 => 3,
        ],
    ];

    /**
     * 计算G羁绊的稀有程度
     */
    static function GidOPLevel($Gid, $count) {
        return self::racesJobs[$Gid][$count];
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