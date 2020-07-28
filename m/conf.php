<?php
class m_conf{


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
    const BF_1       = [1=>1, 2=>1 ,3=>1, 4=>1, 5=>1];
    const BF_2       = [1=>0, 2=>2 ,3=>2, 4=>2, 5=>2];
    const BF_2_4     = [1=>0, 2=>2, 3=>2, 4=>4, 5=>4, 6=>4, 7=>4, 8=>4];
    //有转职
    const BF_2_4_6   = [1=>0, 2=>2, 3=>2, 4=>4, 5=>4, 6=>6, 7=>6, 8=>6, 9=>6, 10=>6, 11=>6, 12=>6, 13=>6];
    //有转职
    const BF_2_4_6_8 = [1=>0, 2=>2, 3=>2, 4=>4, 5=>4, 6=>6, 7=>6, 8=>8, 9=>8, 10=>8, 11=>8, 12=>8, 13=>8];
    const BF_3       = [1=>0, 2=>0, 3=>3, 4=>3, 5=>3, 6=>3, 7=>3, 8=>3, 9=>3];
    const BF_3_6     = [1=>0, 2=>0, 3=>3, 4=>3, 5=>3, 6=>6 ,7=>6, 8=>6, 9=>6, 10=>6, 11=>6];
    //有转职
    const BF_3_6_9   = [1=>0, 2=>0, 3=>3, 4=>3, 5=>3, 6=>6, 7=>6, 8=>6, 9=>9, 10=>9, 11=>9, 12=>9, 13=>9];

    /**
     * races jobs 强度级别标准
     * 6=>(9-12)
     * 9=>(12-15)
     * 
     */
    const races = [
        //raceId  展示级别     数字级别             强度级别
        //星之守护者 * 有增益
        1 => [[3,6,9],    self::BF_3_6_9,     [3=>3, 4=>4, 5=>5, 6=>9, 7=>10, 8=>11, 9=>15, 10=>16, 11=>17, 12=>18, 13=>19]],

        //银河魔装机神 个数越多 性价比越低
        2 => [[0,0,3],    self::BF_3,         [3=>6, 4=>5, 5=>4, 6=>3]],

        //星神 * 无
        3 => [[2,4,6],    self::BF_2_4_6,     [2=>2, 3=>2, 4=>7, 5=>8, 6=>12, 7=>12, 8=>12, 9=>12, 10=>12]],

        //奥德赛 * 有增益
        4 => [[3,6,9],    self::BF_3_6_9,     [3=>3, 4=>4, 5=>5, 6=>10, 7=>11, 8=>12, 9=>15, 10=>16, 11=>17, 12=>18]],

        //未来战士 无
        5 => [[2,4,[6,8]],self::BF_2_4_6_8,   [2=>2, 3=>2, 4=>6, 5=>7, 6=>10, 7=>10, 8=>14]],

        //虚空 无
        // 6 => [[0,0,3],    self::BF_3,         [3=>6]],
        //太空海盗 有增益
        7 => [[2,0,4],    self::BF_2_4,       [2=>2, 3=>3, 4=>8]],

        //源计划 无
        8 => [[3,0,6],    self::BF_3_6,       [3=>3, 4=>4, 5=>5, 6=>10, 7=>11, 8=>12]],

        //暗星 * 有增益
        9 => [[2,4,[6,8]],self::BF_2_4_6_8,   [2=>2, 3=>3, 4=>6, 5=>7, 6=>10, 7=>11, 8=>15, 9=>16, 10=>17, 11=>18, 12=>19]],

        //女武神 无
        // 10 => [[0,0,2],    self::BF_2,        [2=>5]],
        //战地机甲 * 有增益
        11 => [[2,4,[6,8]],self::BF_2_4_6_8,   [2=>2, 3=>3, 4=>6, 5=>7, 6=>10, 7=>11, 8=>15, 9=>16, 10=>17, 11=>18, 12=>19]],

        //宇航员 无
        13 => [[0,0,3],    self::BF_3,        [3=>6, 4=>7]],
    ];
    const jobs = [
        //jobId  展示级别     数字级别             强度级别
        //剑士 * 有增益
        1 => [[3,6,9],    self::BF_3_6_9,     [3=>3, 4=>4, 5=>5, 6=>9, 7=>10, 8=>11, 9=>14, 10=>15, 11=>16, 12=>17]],
        
        //爆破专家 无
        2 => [[0,0,2],    self::BF_2,         [2=>5, 3=>5]],
        
        //刺客 * 有增益
        3 => [[2,4,6],    self::BF_2_4_6,     [2=>2, 3=>3, 4=>7, 5=>8, 6=>12, 7=>13, 8=>14, 9=>15, 10=>16, 11=>17, 12=>18, 13=>19]],
        
        //斗士 无
        4 => [[2,0,4],    self::BF_2_4,       [2=>2, 3=>3, 4=>8, 5=>10]],
        
        //法师 有增益
        5 => [[2,4,6],    self::BF_2_4_6,     [2=>2, 3=>3, 4=>7, 5=>8, 6=>12, 7=>13, 8=>14, 9=>15, 10=>16, 11=>17]],
        
        //圣盾使 *
        6 => [[2,4,6],    self::BF_2_4_6,     [2=>2, 3=>3, 4=>7, 5=>8, 6=>12, 7=>13, 8=>14, 9=>15, 10=>16, 11=>17, 12=>18, 13=>19]],
        
        //狙神
        7 => [[2,0,4],    self::BF_2_4,       [2=>2, 3=>3, 4=>8, 5=>10]],
        
        //秘术师
        8 => [[2,0,4],    self::BF_2_4,       [2=>2, 3=>3, 4=>8, 5=>10]],
        
        //破法战士
        9 => [[0,0,2],    self::BF_2,         [2=>4, 3=>5]],
        
        //强袭枪手
        10 => [[2,0,4],    self::BF_2_4,      [2=>2, 3=>3, 4=>8, 5=>10]],
        
        //星舰龙神
        11 => [[0,0,1],    self::BF_1,        [1=>3]],
        
        //佣兵
        12 => [[0,0,1],    self::BF_1,        [1=>2, 2=>3]],
        
        //重装战士
        13 => [[2,4,6],    self::BF_2_4_6,      [2=>2, 3=>3, 4=>6, 5=>7, 6=>12, 7=>13, 8=>14, 9=>15, 10=>16, 11=>17, 12=>18, 13=>19]],
        
        //大魔法使
        14 => [[0,0,1],    self::BF_1,        [1=>3]],
    ];

    /**
     * 英雄按照价格排序
     */
    const hero_sort = [
        6,//5 无畏战车 厄加特
        40,//5 风暴之怒 迦娜
        41,//5 海洋之灾 普朗克
        101,//5 远古巫灵 泽拉斯
        117,//5 仙灵女巫 璐璐
        136,//5 铸星龙王 奥瑞利安·索尔
        245,//5 时间刺客 艾克
        412,//5 魂锁典狱长 锤石

        16,//4 众星之子 索拉卡
        17,//4 迅捷斥候 提莫
        39,//4 刀锋舞者 艾瑞莉娅
        62,//4 齐天大圣 孙悟空
        92,//4 放逐之刃 锐雯
        105,//4 潮汐海灵 菲兹
        112,//4 机械先驱 维克托
        150,//4 迷失之牙 纳尔
        202,//4 戏命师 烬
        222,//4 暴走萝莉 金克丝

        11,//3 无极剑圣 易
        22,//3 寒冰射手 艾希
        35,//3 恶魔小丑 萨科
        43,//3 天启者 卡尔玛
        67,//3 暗夜猎手 薇恩
        68,//3 机械公敌 兰博
        69,//3 魔蛇之拥 卡西奥佩娅
        81,//3 探险家 伊泽瑞尔
        126,//3 未来守护者 杰斯
        134,//3 暗黑元首 辛德拉
        254,//3 皮城执法官 蔚
        432,//3 星界游神 巴德
        518,//3 万花通灵 妮蔻

        1,//2 黑暗之女 安妮
        5,//2 德邦总管 赵信
        53,//2 蒸汽机器人 布里茨
        82,//2 铁铠冥魂 莫德凯撒
        96,//2 深渊巨口 克格莫
        98,//2 暮光之眼 慎
        103,//2 九尾妖狐 阿狸
        111,//2 深海泰坦 诺提勒斯
        122,//2 诺克萨斯之手 德莱厄斯
        157,//2 疾风剑豪 亚索
        236,//2 圣枪游侠 卢锡安
        238,//2 影流之主 劫
        497,//2 幻翎 洛

        4,//1 卡牌大师 崔斯特
        51,//1 皮城女警 凯特琳
        54,//1 熔岩巨兽 墨菲特
        56,//1 永恒梦魇 魔腾
        59,//1 德玛西亚皇子 嘉文四世
        78,//1 圣锤之毅 波比
        89,//1 曙光女神 蕾欧娜
        104,//1 法外狂徒 格雷福斯
        114,//1 无双剑姬 菲奥娜
        115,//1 爆破鬼才 吉格斯
        142,//1 暮光星灵 佐伊
        420,//1 海兽祭司 俄洛伊
        498,//1 逆羽 霞
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
        }
        return 0;
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

        }
    }
    /**
     * 种族权重
     */
    static public function raceValue($id, $count){
        return self::races[$id][2][$count];
    }
    /**
     * 职业权重
     */
    static public function jobValue($id, $count){
        return self::jobs[$id][2][$count];
    }
}