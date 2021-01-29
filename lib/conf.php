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
    const IN_WEAPON_MAX = 5;
    /**
     * 支持的最多人数 count($inHero)的最大值为
     */
    const IN_HERO_MAX = 10;

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
    /**
     * 羁绊个数=>有效数量
     */
    const BF_1       = [0=>1, 1=>1];
    const BF_1_2     = [0=>2, 1=>1, 2=>2];
    const BF_1_4     = [0=>4, 1=>1, 2=>1 ,3=>1, 4=>4];
    const BF_REN_1_4 = [0=>4, 1=>1, 2=>0 ,3=>0, 4=>4];
    const BF_2       = [0=>2, 1=>0, 2=>2];
    const BF_2_4     = [0=>4, 1=>0, 2=>2, 3=>2, 4=>4];
    const BF_2_3_4   = [0=>4, 1=>0, 2=>2, 3=>3, 4=>4];
    const BF_2_3_4_5 = [0=>5, 1=>0, 2=>2, 3=>3, 4=>4, 5=>5];
    const BF_2_4_6   = [0=>6, 1=>0, 2=>2, 3=>2, 4=>4, 5=>4, 6=>6];
    const BF_2_4_6_8 = [0=>8, 1=>0, 2=>2, 3=>2, 4=>4, 5=>4, 6=>6, 7=>6, 8=>8];
    const BF_3       = [0=>3, 1=>0, 2=>0, 3=>3];
    const BF_3_5     = [0=>3, 1=>0, 2=>0, 3=>3, 4=>3, 5=>5];
    const BF_3_5_7   = [0=>3, 1=>0, 2=>0, 3=>3, 4=>3, 5=>5, 6=>5, 7=>7];
    const BF_3_6     = [0=>6, 1=>0, 2=>0, 3=>3, 4=>3, 5=>3, 6=>6];
    const BF_3_6_9   = [0=>9, 1=>0, 2=>0, 3=>3, 4=>3, 5=>3, 6=>6, 7=>6, 8=>6, 9=>9];

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
        //腥红之月
        1=> [[3,0,6,9],self::BF_3_6_9],
        //天神
        2=> [[2,4,6,8],self::BF_2_4_6_8],
        //永恒之森
        4=> [[3,0,6,9],self::BF_3_6_9],
        //玉剑仙
        5=> [[2,4,6],self::BF_2_4_6],
        //浪人
        6=> [[1,0,2],self::BF_1_2],
        //福星
        7=> [[0,3,0,6],self::BF_3_6],
        //忍者
        9=> [[1,0,4],self::BF_REN_1_4],
        //灵魂莲华明昼
        10=> [[2,0,4],self::BF_2_4],
        //霸王
        11=> [[0,0,1],self::BF_1],
        //三国猛将
        12=> [[3,0,6,9],self::BF_3_6_9],
        //龙魂
        14=> [[3,0,6,9],self::BF_3_6_9],
        //山海绘卷
        15=> [[0,0,3],self::BF_3],
        //主宰
        16=> [[0,0,1],self::BF_1],
    ];
    const jobs = [
        //jobId  展示级别 数字级别
        //宗师
        101=> [[2,3,0,4],self::BF_2_3_4],
        //刺客
        102=> [[2,0,4,6],self::BF_2_4_6],
        //斗士
        103=> [[2,4,6,8],self::BF_2_4_6_8],
        //决斗大师
        105=> [[2,4,6,8],self::BF_2_4_6_8],
        //神盾使
        107=> [[2,0,4,6],self::BF_2_4_6],
        //魔法师
        108=> [[3,5,0,7],self::BF_3_5_7],
        //秘术师
        109=> [[2,0,4,6],self::BF_2_4_6],
        //神射手
        111=> [[2,0,4,6],self::BF_2_4_6],
        //重装战士
        112=> [[2,4,6,8],self::BF_2_4_6_8],
        //枭雄
        113=> [[0,0,1],self::BF_1],
        //神使
        114=> [[2,0,4],self::BF_2_4],
        //战神
        115=> [[3,0,6],self::BF_3_6],
        //裁决使
        116=> [[2,3,4],self::BF_2_3_4],
        //铁匠
        117=> [[0,0,1],self::BF_1],
    ];
    /**
     * 计算G羁绊的稀有程度
     */
    static function GidOPLevel($Gid, $count){
        if($Gid > 100){
            return array_search($count, self::jobs[$Gid][0], true);
        }
        return array_search($count, self::races[$Gid][0], true);
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
        26,//时光守护者	基兰
        50,//诺克萨斯统领	斯维因
        64,//盲僧	李青
        268,//沙漠皇帝	阿兹尔
        360,//沙漠玫瑰	莎弥拉
        516,//山隐之焰	奥恩
        777,//封魔剑魂	永恩
        875,//腕豪	瑟提
        2,//狂战士	奥拉夫
        10,//正义天使	凯尔
        23,//蛮族之王	泰达米尔
        25,//堕落天使	莫甘娜
        31,//虚空恐惧	科加斯
        91,//刀锋之影	泰隆
        98,//暮光之眼	慎
        113,//北地之怒	瑟庄妮
        136,//铸星龙王	奥利瑞安·索尔
        266,//暗裔剑魔	亚托克斯
        498,//逆羽	霞
        15,//战争女神	希维尔
        20,//雪原双子	努努和威朗普
        39,//刀锋舞者	艾瑞莉娅
        45,//邪恶小法师	维迦
        55,//不祥之刃	卡特琳娜
        84,//离群之刺	阿卡丽
        85,//狂暴之心	凯南
        102,//龙血武姬 	希瓦娜
        122,//诺克萨斯之手	德莱厄斯
        203,//永猎双子	千珏
        350,//魔法猫咪	悠米
        429,//复仇之矛	卡莉丝塔
        518,//万花通灵	妮蔻
        1,//黑暗之女	安妮
        8,//猩红收割者	弗拉基米尔
        17,//迅捷斥候	提莫
        24,//武器大师	贾克斯
        40,//风暴之怒	迦娜
        59,//德玛西亚皇子	嘉文四世
        111,//深海泰坦	诺提勒斯
        117,//仙灵女巫	璐璐
        201,//弗雷尔卓德之心	布隆
        238,//影流之主	劫
        254,//皮城执法官	蔚
        497,//幻翎	洛
        555,//血港鬼影	派克
        4,//卡牌大师	崔斯特
        18,//麦林炮手	崔丝塔娜
        57,//扭曲树精	茂凯
        60,//蜘蛛女皇	伊莉丝
        62,//齐天大圣	孙悟空
        63,//复仇焰魂	布兰德
        75,//沙漠死神	内瑟斯
        76,//狂野女猎手	奈德丽
        86,//德玛西亚之力	盖伦
        114,//无双剑姬	菲奥娜
        131,//皎月女神	黛安娜
        157,//疾风剑豪	亚索
        223,//河流之主	塔姆        
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