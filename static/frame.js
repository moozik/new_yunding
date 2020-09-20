var regNumber = /^\d+$/;
$.getJSON({
    url: "//game.gtimg.cn/images/lol/act/img/tft/js/chess.js",
    async: false,
    success: function (ret) {
        window.DATA_chess = ret;
    },
});
$.getJSON({
    url: "//game.gtimg.cn/images/lol/act/img/tft/js/race.js",
    async: false,
    success: function (ret) {
        var retData = {};
        for(let i in ret.data){
            retData[ret.data[i].raceId] = ret.data[i];
        }
        window.DATA_race = retData;
    },
});
$.getJSON({
    url: "//game.gtimg.cn/images/lol/act/img/tft/js/job.js",
    async: false,
    success: function (ret) {
        var retData = {};
        for(let i in ret.data){
            retData[ret.data[i].jobId] = ret.data[i];
        }
        window.DATA_job = retData;
    },
});
$.getJSON({
    url: "//game.gtimg.cn/images/lol/act/img/tft/js/equip.js",
    async: false,
    success: function (ret) {
        window.DATA_equip = ret;
    },
});
var vm = new Vue({
    el: "#app",
    data: {
        season: DATA_chess.season,
        time: DATA_chess.time,
        version: DATA_chess.version,
        //特性
        raceArr: DATA_race,
        //职业
        jobArr: DATA_job,
        //英雄
        chessArr: function(){
            var ret = {};
            var chess = {};
            // var detail;
            for (let i in DATA_chess.data) {
                chess = DATA_chess.data[i];
                chess.fullName = chess.title + ' ' + chess.displayName;

                //是否增强
                proStatus = ('无' == chess.proStatus) ? '' : ("\n版本改动：" + chess.proStatus);
                //描述
                chess.description = '名称：' + chess.fullName + "\n职业：" + chess.races + ' ' + chess.jobs + "\n价格：" + chess.price + "\n技能：" + chess.skillIntroduce + proStatus;
                chess.jobIds = chess.jobIds.split(',');
                

                ret[DATA_chess.data[i].chessId] = chess;
            }
            return ret;
        }(),
        //装备
        equipArr: function(){
            var ret = {};
            var equip,job,proStatus;
            for (let i in DATA_equip.data) {
                //排除老版本装备
                if(DATA_equip.data[i].equipId < 400){
                    continue;
                }
                //只要转职装备
                if(
                    (DATA_equip.data[i].jobId == '0' || DATA_equip.data[i].jobId == null) &&
                    (DATA_equip.data[i].raceId == '0' || DATA_equip.data[i].raceId == null)
                ){
                    continue;
                }
                
                equip = DATA_equip.data[i];
                //转职类型
                if(equip.jobId > 0){
                    job = DATA_job[equip.jobId].name;
                }else{
                    job = DATA_race[equip.raceId].name;
                }
                //是否增强
                proStatus = ('无' == equip.proStatus) ? '' : ("\n版本改动：" + equip.proStatus);
                equip.title = '名称：' + equip.name + "\n职业：" + job + "\n关键字：" + equip.keywords + proStatus;
                
                ret[DATA_equip.data[i].equipId] = equip;
            }
            return ret;
        }(),
        //级别到英雄价格关系
        // level2cost: levelArr,
        //==以下为动态

        //当前选中羁绊
        groupCheckedId: 0,
        groupCheckedType: 'job or race',

        theOneRace: 0,
        theOneJob: 0,
        //当前羁绊组合
        groupList: [],
        //被ban英雄
        heroBanList: [],
        //当前选中英雄
        inHeroList: [],
        //价值筛选
        heroValue: { 1: true, 2: true, 3: true, 4: true, 5: true },
        //待计算个数
        teamCount: 9,
        //吃鸡阵容
        chickenArmy: [], //最后结果
        //官方推荐阵容
        chickenArmyPlus: [],
        //转职装备
        weaponList: [],
        //转职装备 临时存储
        weaponListCache: [],
    },
    methods: {
        //判断指定英雄是否属于当前组别
        checkGroupHero: function (chess, price) {
            //金额组别不对
            if(chess.price != price){
                return false;
            }

            //未筛选羁绊
            if (this.groupCheckedId == 0) {
                return true;
            }
            if(this.groupCheckedType == 'job'){
                if(-1 == chess.jobIds.indexOf(this.groupCheckedId)){
                    return false;
                }
            }
            if(this.groupCheckedType == 'race'){
                if(this.groupCheckedId != chess.raceIds){
                    return false;
                }
            }
            return true;
        },
        //判断羁绊筛选按钮是否亮起
        isGroupHover: function (group) {
            //groupCheckedType
            //groupCheckedId
            if (group.raceId && group.raceId == this.groupCheckedId && 'race' == this.groupCheckedType) {
                return "on";
            }
            if (group.jobId && group.jobId == this.groupCheckedId && 'job' == this.groupCheckedType) {
                return "on";
            }
            return '';
        },
        //点击羁绊按钮 切换英雄筛选
        clickGroup: function (group) {
            if (group.raceId) {
                if(group.raceId == this.groupCheckedId && 'race' == this.groupCheckedType){
                    this.groupCheckedId = 0;
                    this.groupCheckedType = '';
                }else{
                    this.groupCheckedId = group.raceId;
                    this.groupCheckedType = 'race';
                }
            }
            if (group.jobId){
                if(group.jobId == this.groupCheckedId && 'job' == this.groupCheckedType) {
                    this.groupCheckedId = 0;
                    this.groupCheckedType = '';
                }else{
                    this.groupCheckedId = group.jobId;
                    this.groupCheckedType = 'job';
                }
            }
        },
        //绑定英雄池左键
        clickHero: function (hero) {
            inHeroList = this.inHeroList;
            //clickHero
            ret = this.heroInArray(hero, inHeroList);
            if (ret !== false) {
                //英雄已存在，删除
                inHeroList.splice(ret, 1);
            } else {
                //英雄不存在，添加

                //10个英雄上限
                if (inHeroList.length === 10) return;

                //添加
                inHeroList.push(hero);
            }
            //刷新阵容数量
            //this.updateTeamCountByClickHero();
            //刷新金额限制
            //this.updateCost();
        },
        //绑定英雄池右键
        banHero: function (hero) {
            heroBanList = this.heroBanList;
            //banHero
            ret = this.heroInArray(hero, heroBanList);
            if (ret !== false) {
                heroBanList.splice(ret, 1);
            } else {
                heroBanList.push(hero);
            }
        },
        //绑定转职装备
        clickWeapon: function (weapon) {
            if (this.weaponList.length < 10) {
                this.weaponList.push(weapon);
            }
        },
        //删除转职装备
        delWeapon: function (index) {
            this.weaponList.splice(index, 1);
        },
        //修改金额
        // forCountBtn: function (forCount) {
        //     this.forCount = forCount;
        //     this.updateCost();
        // },
        //更新阵容数量 by 棋子选择
        // updateTeamCountByClickHero: function() {
        //     if(this.inHeroList.length >= 6){
        //         this.teamCount = 9;
        //     }else{
        //         this.teamCount + 3;
        //     }
        // },
        //更新费用限制 by 阵容数量
        updateCostByTeamCount: function() {
            // var teamCount;
            // if (this.inHeroList.length + this.forCount > 7) {
            //     teamCount = 7;
            // } else {
            //     teamCount = this.inHeroList.length + this.forCount;
            // }
            // for(let i in this.heroValue){
            //     this.heroValue[i] = false;
            // }
            var ret = { 1: false, 2: false, 3: false, 4: false, 5: false };
            for (let i in levelArr[this.teamCount]) {
                ret[levelArr[this.teamCount][i]] = true;
            }
            this.heroValue = ret;
        },
        saveNiceTeam: function (heroList, index) {
            heroList.weapon = this.weaponListCache;
            heroList.teamname = $('.teamname'+index).val();
            $.post(
                "/yunding/yunding.php?action=niceTeam",
                { niceTeam: JSON.stringify(heroList) },
                function (result) {
                    alert(result);
                }
            );
        },
        //判断指定英雄是否在指定数组中 不存在返回false，存在返回下标
        heroInArray: function (hero, arr) {
            key = false;
            arr.forEach((heroItem, index) => {
                if (hero.chessId == heroItem.chessId) {
                    key = index;
                }
            });
            return key;
        },
        valBtn: function (val) {
            if (this.heroValue[val]) {
                //true
                this.heroValue[val] = false;
            } else {
                //false
                this.heroValue[val] = true;
            }
        },
        clearBtn: function () {
            this.inHeroList.splice(0);
            //this.heroBanList.splice(0);
            this.chickenArmy.splice(0);
            this.chickenArmyPlus.splice(0);
            this.groupChecked = 0;
            this.forCount = 3;
            this.weaponList.splice(0);
            this.heroValue = {
                1: true,
                2: true,
                3: true,
                4: false,
                5: false,
            };
        },
    },
});
/**
 * 鼠标移入羁绊按钮
 */
$(document).on("mouseenter", ".groupBtn", function () {
    $("#pop1").css("display", "none");
    var id = $(this).attr("data-raceId") || $(this).attr("data-jobId");
    if ($(this).attr("data-raceId")) {
        var data = vm.raceArr[id];
    } else {
        var data = vm.jobArr[id];
    }
    $(".synergies-box").html(template("jobPopTemp", data));
    $(".synergies-box").css("display", "block");
});
/**
 * 鼠标移入英雄图标
 */
$(document).on("mouseenter", ".heroBtn", function () {
    $(".synergies-box").css("display", "none");
    var hero = DATA_chess.data;
    var ret = vm.chessArr[$(this).attr("data-chessId")];
    ret.equip = [];
    if (typeof ret.recEquip != "undefined") {
        var tmp = ret.recEquip.split(",");
        tmp.forEach((item, index) => {
            if(item < 1000){
                ret.equip.push(item);
            }
        });
    }
    $("#pop1").html(template("ChampionPop2", ret));
    $("#pop1").css("display", "block");
});
//数组取交集
// Array.intersect = function () {
//     var result = new Array();
//     var obj = {};
//     for (var i = 0; i < arguments.length; i++) {
//         for (var j = 0; j < arguments[i].length; j++) {
//             var str = arguments[i][j];
//             if (!obj[str]) {
//                 obj[str] = 1;
//             } else {
//                 obj[str]++;
//                 if (obj[str] == arguments.length) {
//                     result.push(str);
//                 }
//             } //end else
//         } //end for j
//     } //end for i
//     return result;
// };
//提前处理官方js
// Object.keys(TFTLineup_V3_List).forEach((k) => {
//     TFTLineup_V3_List[k].idStr = 'detail' + k;
//     TFTLineup_V3_List[k].early_heroes = TFTLineup_V3_List[k].early_heroes.split(',');
//     TFTLineup_V3_List[k].metaphase_heroes = TFTLineup_V3_List[k].metaphase_heroes.split(',');
//     TFTLineup_V3_List[k].line_hero = TFTLineup_V3_List[k].line_hero.split(',');
// });
//匹配官方阵容
/*function matchLOL() {
    //匹配js中的阵容
    var heroImgArr = new Array();
    vm.inHeroList.forEach((hero) => {
        heroImgArr.push(hero.img);
    });
    var early_heroes, result;
    Object.keys(TFTLineup_V3_List).forEach((k) => {
        console.log(TFTLineup_V3_List[k]);

        //early_heroes 匹配前期阵容
        // if(heroImgArr.length <= 4){
        //     result = Array.intersect(heroImgArr, TFTLineup_List[k].early_heroes);
        //     if(result.length === 3){
        //         vm.chickenArmyPlus.push(TFTLineup_List[k]);
        //         return true;
        //     }
        // }
        //匹配中期 metaphase_heroes 中期阵容
        if (heroImgArr.length >= 4) {
            result = Array.intersect(
                heroImgArr,
                TFTLineup_V3_List[k].metaphase_heroes
            );
            if (heroImgArr.length - result.length <= 1) {
                vm.chickenArmyPlus.push(TFTLineup_V3_List[k]);
                return true;
            }
        }
        //匹配后期 line_hero 成型阵容
        if (heroImgArr.length >= 4) {
            result = Array.intersect(
                heroImgArr,
                TFTLineup_V3_List[k].line_hero
            );
            if (heroImgArr.length - result.length <= 2) {
                vm.chickenArmyPlus.push(TFTLineup_V3_List[k]);
                return true;
            }
        }
    });
}*/
$("#runBtn").click(function () {
    //删除原有数据
    vm.chickenArmy.splice(0);
    vm.chickenArmyPlus.splice(0);
    vm.weaponListCache = vm.weaponList;
    //匹配官方js中的阵容，并压入vm.chickenArmyPlus结果集
    // matchLOL();

    //拼接数据
    var getData = {
        theOne: (vm.theOneJob) != 0 ? vm.theOneJob : vm.theOneRace,
        inHero: new Array(),
        costList: new Array(),
        banHero: new Array(),
        forCount: vm.teamCount - vm.inHeroList.length,
        weapon: new Array(),
    };
    vm.inHeroList.forEach((e) => {
        getData.inHero.push(e.chessId);
    });
    vm.heroBanList.forEach((e) => {
        getData.banHero.push(e.chessId);
    });
    vm.weaponList.forEach((e) => {
        getData.weapon.push((e.jobId) != '0' ? e.jobId : e.raceId);
    });
    // console.log(vm.heroValue);
    for (var i in vm.heroValue) {
        if (vm.heroValue[i]) {
            getData.costList.push(parseInt(i));
        }
    }
    //请求接口
    $.getJSON({
        url: "calc",
        data: {
            action: "calc",
            data: JSON.stringify(getData),
        },
        success: function (ret) {
            if (0 == ret["errno"]) {
                displayPage(ret["data"]);
            } else {
                alert(ret["errmsg"]);
            }
        },
    });
});
function displayPage(ret) {
    // console.log(ret);
    var hero = [];
    var group = [];
    var groupArr = vm.groupArr;
    ret.forEach((item, index) => {
        hero = [];
        group = [];
        //组合英雄对象
        item[0].forEach((hId) => {
            // console.log(hId);
            hero.push({
                name: heroArr[hId][0],
                img: heroArr[hId][3],
                title: heroArr[hId][7],
            });
        });
        //组合羁绊对象 组合羁绊图片对象
        //后端判定为顶级羁绊存在item[4]
        $.each(item[4], function (groupId, count) {
            //压入羁绊结果集
            group.push({
                //title: groupArr[groupId].name+':'+count,
                name: groupArr[groupId].name,
                id: groupId,
                count: count,
                classStr: "grade3 ", //+groupArr[groupId].timg
            });
        });
        //中级羁绊item[6]
        $.each(item[6], function (groupId, count) {
            //压入羁绊结果集
            group.push({
                //title: groupArr[groupId].name+':'+count,
                name: groupArr[groupId].name,
                id: groupId,
                count: count,
                classStr: "grade2 ", //+groupArr[groupId].timg
            });
        });
        //后端判定为普通羁绊存在item[2]
        $.each(item[2], function (groupId, count) {
            group.push({
                //title: groupArr[groupId].name+':'+count,
                name: groupArr[groupId].name,
                id: groupId,
                count: count,
                classStr: "grade1 ", //+groupArr[groupId].timg
            });
        });
        //压入结果集，展示结果
        vm.chickenArmy.push({
            hero: hero,
            group: group,
            score: item[5],
            tips: item[8],
            op: item[9],
        });
        // console.log(hero,group);
    });
}
