<?php
/**
 * 配置定义
 */
class lib_conf{


    /**
     * 展示个数
     */
    const SHOW_LIMIT = 20;
    /**
     * 支持的装备个数
     */
    // const IN_WEAPON_MAX = 5;
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
        3 => [1,2],
        4 => [1,2,3],
        5 => [1,2,3,4],
        6 => [1,2,3,4],
        7 => [1,2,3,4,5],
        8 => [1,2,3,4,5],
        9 => [1,2,3,4,5],
        10 => [1,2,3,4,5],
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
        1 => [3,6,9],
        //黑夜使者
        2 => [2,4,6,8],
        //魔女
        3 => [0,0,3],
        //小恶魔
        4 => [3,5,0,7],
        //屠龙勇士
        5 => [2,0,4],
        //丧尸
        6 => [3,4,5],
        //圣光卫士
        7 => [3,6,0,9],
        //黎明使者
        8 => [2,4,6,8],
        //神佑之森
        9 => [2,0,3],
        //龙族
        10 => [3,0,5],
        //铁甲卫士
        11 => [2,0,3],
        //永猎双子
        12 => [0,0,1],
        //复生亡魂
        13 => [2,0,4],
    ];
    const jobs = [
        //jobId  展示级别 数字级别
        //刺客
        101 => [2,4,6],
        //游侠
        102 => [2,0,4],
        //斗士
        103 => [2,0,4],
        //征服者
        104 => [2,4,6,8],
        //复苏者
        105 => [2,4,6],
        //神谕者
        106 => [2,0,4],
        //骑士
        107 => [2,4,6],
        //法师
        108 => [2,4,6],
        //神盾战士
        109 => [3,6,0,9],
        //秘术师
        110 => [2,3,4],
        //重骑兵
        111 => [2,3,4],
        //大魔王
        112 => [0,0,1],
        //神王
        113 => [0,0,1],
        //驯龙大师
        114 => [0,0,1],
    ];

    /**
     * https://lol.qq.com/tft/js/util.js?v=20210527
     */
    const contactLevelColor = [
        1=>[ 3=> 1, 6=> 2, 9=> 3 ],
        2=>[ 2=> 1, 4=> 2, 6=> 3, 8=> 4 ],
        3=>[ 3=> 3 ],
        4=>[ 3=> 1, 5=> 3, 7=> 4 ],
        5=>[ 2=> 1, 4=> 3 ],
        6=>[ 3=> 1, 4=> 2, 5=> 3 ],
        7=>[ 3=> 1, 6=> 3, 9=> 4 ],
        8=>[ 2=> 1, 4=> 2, 6=> 3, 8=> 4 ],
        9=>[ 2=> 1, 3=> 3 ],
        10=> [ 3=> 1, 5=> 3 ],
        11=> [ 2=> 1, 3=> 3 ],
        12=> [ 1=> 3 ],
        13=> [ 2=> 1, 3=> 3 ],
        101=> [ 2=> 1, 4=> 2, 6=> 3 ],
        102=> [ 2=> 1, 4=> 3 ],
        103=> [ 2=> 1, 4=> 3 ],
        104=> [ 2=> 1, 4=> 2, 6=> 3, 8=> 4 ],
        105=> [ 2=> 1, 4=> 3 ],
        106=> [ 2=> 1, 4=> 3 ],
        107=> [ 2=> 1, 4=> 2, 6=> 3 ],
        108=> [ 2=> 1, 4=> 3 ],
        109=> [ 3=> 1, 6=> 3 ],
        110=> [ 2=> 1, 3=> 2, 4=> 3 ],
        111=> [ 2=> 1, 3=> 2, 4=> 3 ],
        112=> [ 1=> 3 ],
        113=> [ 1=> 3 ],
        114=> [ 1=> 3 ],
    ];
    /**
     * 计算G羁绊的稀有程度
     */
    static function GidOPLevel($Gid, $count){
        if($Gid > 100){
            return array_search($count, self::jobs[$Gid], true);
        }
        return array_search($count, self::races[$Gid], true);
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
    /**
     * 英雄按照价格排序
     */
    const chess_sort = [
        17	,//迅捷斥候	提莫
        10	,//正义天使	凯尔
        74	,//大发明家	黑默丁格
        86	,//德玛西亚之力	盖伦
        106,//	不灭狂雷	沃利贝尔
        122,//	诺克萨斯之手	德莱厄斯
        203,//	永猎双子	千珏
        234,//	破败之王	佛耶戈
        13	,//符文法师	瑞兹
        24	,//武器大师	贾克斯
        43	,//天启者	卡尔玛
        44	,//瓦洛兰之盾	塔里克
        62	,//铁铠冥魂	莫德凯撒
        119,//	荣耀行刑官	德莱文
        131,//	皎月女神	黛安娜
        161,//	虚空之眼	维克兹
        427,//	翠神	艾翁
        523,//	残月之肃	厄斐琉斯
        526,//	镕铁少女	芮尔
        20	,//雪原双子	努努和威朗普
        22	,//寒冰射手	艾希
        25	,//堕落天使	莫甘娜
        55	,//不祥之刃	卡特琳娜
        56	,//永恒梦魇	魔腾
        64	,//盲僧	李青
        76	,//狂野女猎手	奈德丽
        80	,//不屈之枪	潘森
        92	,//放逐之刃	锐雯
        99	,//光辉女郎	拉克丝
        117,//	仙灵女巫	璐璐
        143,//	荆棘之兴	婕拉
        157,//	疾风剑豪	亚索
        7	,//诡术妖姬	乐芙兰
        16	,//众星之子	索拉卡
        48	,//巨魔之王	特朗德尔
        63	,//复仇焰魂	布兰德
        85	,//狂暴之心	凯南
        110,//	惩戒之箭	韦鲁斯
        111,//	深海泰坦	诺提勒斯
        112,//	机械先驱	维克托
        113,//	北地之怒	瑟庄妮
        120,//	战争之影	赫卡里姆
        134,//	黑暗元首	辛德拉
        412,//	魂锁典狱长	锤石
        875,//	腕豪	瑟提
        8	,//猩红收割者	弗拉基米尔
        19	,//祖安怒兽	沃里克
        67	,//暗夜猎手	薇恩
        75	,//圣锤之毅	波比
        77	,//兽灵行者	乌迪尔
        79	,//酒桶	古拉加斯
        89	,//曙光女神	蕾欧娜
        115,//	爆破鬼才	吉格斯
        121,//	虚空掠夺者	卡兹克
        127,//	冰霜女巫	丽桑卓
        240,//	暴怒骑士	克烈
        266,//	暗裔剑魔	亚托克斯
        429,//	复仇之矛	卡莉丝塔
    ];
    static public function init(){
    }

    /**
     * 种族权重
     */
    // static public function raceValue($id, $count){
    //     return self::races[$id][2][$count];
    // }
    /**
     * 职业权重
     */
    // static public function jobValue($id, $count){
    //     return self::jobs[$id][2][$count];
    // }
}