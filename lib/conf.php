<?php
/**
 * 配置定义
 */
class lib_conf{


    /**
     * 展示个数
     */
    const SHOW_LIMIT = 20;

    const LEVEL2COST = [
        1 => [1],
        2 => [1],
        3 => [1,2,3],
        4 => [1,2,3],
        5 => [1,2,3,4],
        6 => [1,2,3,4],
        7 => [1,2,3,4,5],
        8 => [1,2,3,4,5],
        9 => [1,2,3,4,5],
    ];
    /**
     * 羁绊个数级别
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
    const BF_3_6     = [0=>6, 1=>0, 2=>0, 3=>3, 4=>3, 5=>3, 6=>6];
    const BF_3_6_9   = [0=>9, 1=>0, 2=>0, 3=>3, 4=>3, 5=>3, 6=>6, 7=>6, 8=>6, 9=>9];
    
    /*
    var data = '';
    races.data.forEach((item, index) => {
        groupType = JSON.stringify(Object.keys(item.level).map(function(item){return Number(item);}));
        groupType2 = Object.keys(item.level).join('_');
        data = data + "//" + item.name + "\n" + item.raceId + "=> [" + groupType + ",self::BF_" + groupType2 + "],\n";
    });
    console.log(data);

    var data = '';
    jobs.data.forEach((item, index) => {
        groupType = JSON.stringify(Object.keys(item.level).map(function(item){return Number(item);}));
        groupType2 = Object.keys(item.level).join('_');
        data = data + "//" + item.name + "\n" + item.jobId + "=> [" + groupType + ",self::BF_" + groupType2 + "],\n";
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
        //腥红之月
        1=> [[3,6,9],self::BF_3_6_9],
        //天神
        2=> [[2,4,[6,8]],self::BF_2_4_6_8],
        //灵魂莲华夜幽
        3=> [[2,4,6],self::BF_2_4_6],
        //永恒之森
        4=> [[3,6,9],self::BF_3_6_9],
        //玉剑仙
        5=> [[2,4,6],self::BF_2_4_6],
        //浪人
        6=> [[1,2,0],self::BF_1_2],
        //福星
        7=> [[3,0,6],self::BF_3_6],
        //月神
        8=> [[0,0,3],self::BF_3],
        //忍者
        9=> [[1,0,4],self::BF_1_4],
        //灵魂莲华明昼
        10=> [[2,0,4],self::BF_2_4],
        //霸王
        11=> [[0,0,1],self::BF_1],
        //三国猛将
        12=> [[3,6,9],self::BF_3_6_9],
        //天煞
        13=> [[0,0,1],self::BF_1],
    ];
    const jobs = [
        //jobId  展示级别 数字级别
        //宗师
        1=> [[2,3,4],self::BF_2_3_4],
        //刺客
        2=> [[2,4,6],self::BF_2_4_6],
        //斗士
        3=> [[2,4,[6,8]],self::BF_2_4_6_8],
        //耀光使
        4=> [[2,0,4],self::BF_2_4],
        //决斗大师
        5=> [[2,4,[6,8]],self::BF_2_4_6_8],
        //猎人
        6=> [[2,3,[4,5]],self::BF_2_3_4_5],
        //神盾使
        7=> [[2,4,6],self::BF_2_4_6],
        //魔法师
        8=> [[3,6,9],self::BF_3_6_9],
        //秘术师
        9=> [[2,4,6],self::BF_2_4_6],
        //夜影
        10=> [[2,3,4],self::BF_2_3_4],
        //神射手
        11=> [[2,4,6],self::BF_2_4_6],
        //重装战士
        12=> [[2,4,6],self::BF_2_4_6],
        //枭雄
        13=> [[0,0,1],self::BF_1],
    ];
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
    const hero_sort = [
        26,//5	时光守护者	基兰
        64,//5	盲僧	李青
        81,//5	探险家	伊泽瑞尔
        141,//5	影流之镰	凯隐
        268,//5	沙漠皇帝	阿兹尔
        777,//5	封魔剑魂	永恩
        875,//5	腕豪	瑟提
        876,//5	含羞蓓蕾	莉莉娅

        19,//4	祖安怒兽	沃里克
        22,//4	寒冰射手	艾希
        25,//4	堕落天使	莫甘娜
        69,//4	魔蛇之拥	卡西奥佩娅
        91,//4	刀锋之影	泰隆
        92,//4	放逐之刃	锐雯
        98,//4	暮光之眼	慎
        103,//4	九尾妖狐	阿狸
        113,//4	北地之怒	瑟庄妮
        202,//4	戏命师	烬
        266,//4	暗裔剑魔	亚托克斯

        5,//3	德邦总管	赵信
        20,//3	雪原双子	努努
        28,//3	痛苦之拥	伊芙琳
        39,//3	刀锋舞者	艾瑞莉娅
        45,//3	邪恶小法师	维迦
        55,//3	不祥之刃	卡特琳娜
        84,//3	离群之刺	阿卡丽
        85,//3	狂暴之心	凯南
        99,//3	光辉女郎	拉克丝
        203,//3	永猎双子	千珏
        222,//3	暴走萝莉	金克丝
        350,//3	魔法猫咪	悠米
        429,//3	复仇之矛	卡莉丝塔

        1,//2	黑暗之女	安妮
        17,//2	迅捷斥候	提莫
        24,//2	武器大师	贾克斯
        40,//2	风暴之怒	迦娜
        59,//2	德玛西亚皇子	嘉文四世
        117,//2	仙灵女巫	璐璐
        120,//2	战争之影	赫卡里姆
        238,//2	影流之主	劫
        254,//2	皮城执法官	蔚
        412,//2	魂锁典狱长	锤石
        517,//2	解脱者	塞拉斯
        523,//2	残月之肃	厄斐琉斯
        555,//2	血港鬼影	派克

        4,//1	卡牌大师	崔斯特
        57,//1	扭曲树精	茂凯
        60,//1	蜘蛛女皇	伊莉丝
        62,//1	齐天大圣	孙悟空
        67,//1	暗夜猎手	薇恩
        76,//1	狂野女猎手	奈德丽
        86,//1	德玛西亚之力	盖伦
        114,//1	无双剑姬	菲奥娜
        127,//1	冰霜女巫	丽桑卓
        131,//1	皎月女神	黛安娜
        157,//1	疾风剑豪	亚索
        223,//1	河流之主	塔姆
        267,//1	唤潮鲛姬	娜美
    ];
    static public function init(){
    }
    /**
     * 有效数量
     * @param int $id
     * @param int $count
     * @return int
     */
    static public function raceWorkCount($id, $count){
        // if(!self::$isInit)self::init();
        if(isset(self::races[$id][1][$count])){
            return self::races[$id][1][$count];
        }else{
            //找不到返回顶级羁绊的数量
            return self::races[$id][1][0];
        }
    }
    /**
     * 有效数量
     * @param int $id
     * @param int $count
     * @return int
     */
    static public function jobWorkCount($id, $count){
        // if(!self::$isInit)self::init();
        if(isset(self::jobs[$id][1][$count])){
            return self::jobs[$id][1][$count];
        }else{
            //找不到返回顶级羁绊的数量
            return self::jobs[$id][1][0];
        }
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