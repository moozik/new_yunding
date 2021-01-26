var regNumber = /^\d+$/;
window.DATA_race = {};
window.DATA_job = {};
window.DATA_Ggroup = {};
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
        for(let i in ret.data){
            window.DATA_race[ret.data[i].raceId] = ret.data[i];
            window.DATA_Ggroup[ret.data[i].raceId] = ret.data[i];
        }
    },
});
$.getJSON({
    url: "//game.gtimg.cn/images/lol/act/img/tft/js/job.js",
    async: false,
    success: function (ret) {
        for(let i in ret.data){
            window.DATA_job[ret.data[i].jobId] = ret.data[i];
            window.DATA_Ggroup[parseInt(ret.data[i].jobId) + 100] = ret.data[i];
        }
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
                //装备id不在阵容列表里，跳出
                if(!DATA_race[equip.raceId] && !DATA_job[equip.jobId]){
                    continue;
                }
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
        chessBanList: [],
        //当前选中英雄
        inChessList: [],
        //价值筛选
        chessValue: { 1: true, 2: true, 3: true, 4: false, 5: false },
        //待计算个数
        teamCount: 9,
        //循环层数
        forCount: 3,
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
        checkGroupChess: function (chess, price) {
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
        clickChess: function (chess) {
            inChessList = this.inChessList;
            //clickChess
            ret = this.chessInArray(chess, inChessList);
            if (ret !== false) {
                //英雄已存在，删除
                inChessList.splice(ret, 1);
            } else {
                //英雄不存在，添加

                //10个英雄上限
                if (inChessList.length === 10) return;

                //添加
                inChessList.push(chess);
            }
            //刷新阵容数量
            //this.updateTeamCountByClickChess();
            //刷新金额限制
            //this.updateCost();
        },
        //绑定英雄池右键
        banChess: function (chess) {
            chessBanList = this.chessBanList;
            //banChess
            ret = this.chessInArray(chess, chessBanList);
            if (ret !== false) {
                chessBanList.splice(ret, 1);
            } else {
                chessBanList.push(chess);
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
        forCountBtn: function (forCount) {
            this.forCount = forCount;
            this.updateCost();
        },
        //更新英雄价格区间
        updateCost: function () {
            var teamCount;
            if (this.inChessList.length + this.forCount > 7) {
                teamCount = 7;
            } else {
                teamCount = this.inChessList.length + this.forCount;
            }
            var ret = { 1: false, 2: false, 3: false, 4: false, 5: false };
            for (let i in levelArr[teamCount]) {
                ret[levelArr[teamCount][i]] = true;
            }
            this.chessValue = ret;
        },
        //更新阵容数量 by 棋子选择
        // updateTeamCountByClickChess: function() {
        //     if(this.inChessList.length >= 6){
        //         this.teamCount = 9;
        //     }else{
        //         this.teamCount + 3;
        //     }
        // },
        //更新费用限制 by 阵容数量
        updateCostByTeamCount: function() {
            // var teamCount;
            // if (this.inChessList.length + this.forCount > 7) {
            //     teamCount = 7;
            // } else {
            //     teamCount = this.inChessList.length + this.forCount;
            // }
            // for(let i in this.chessValue){
            //     this.chessValue[i] = false;
            // }
            var ret = { 1: false, 2: false, 3: false, 4: false, 5: false };
            for (let i in levelArr[this.teamCount]) {
                ret[levelArr[this.teamCount][i]] = true;
            }
            this.chessValue = ret;
        },
        saveNiceTeam: function (chessList, index) {
            chessList.weapon = this.weaponListCache;
            chessList.teamname = $('.teamname'+index).val();
            $.post(
                "/yunding/yunding.php?action=niceTeam",
                { niceTeam: JSON.stringify(chessList) },
                function (result) {
                    alert(result);
                }
            );
        },
        //判断指定英雄是否在指定数组中 不存在返回false，存在返回下标
        chessInArray: function (chess, arr) {
            key = false;
            arr.forEach((chessItem, index) => {
                if (chess.chessId == chessItem.chessId) {
                    key = index;
                }
            });
            return key;
        },
        valBtn: function (val) {
            if (this.chessValue[val]) {
                //true
                this.chessValue[val] = false;
            } else {
                //false
                this.chessValue[val] = true;
            }
        },
        clearBtn: function () {
            this.inChessList.splice(0);
            //this.chessBanList.splice(0);
            this.chickenArmy.splice(0);
            this.chickenArmyPlus.splice(0);
            this.groupChecked = 0;
            this.forCount = 3;
            this.weaponList.splice(0);
            this.chessValue = {
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
$(document).on("mouseenter", ".chessBtn", function () {
    $(".synergies-box").css("display", "none");
    var chess = DATA_chess.data;
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
$("#runBtn").click(function () {
    //删除原有数据
    vm.chickenArmy.splice(0);
    vm.chickenArmyPlus.splice(0);
    vm.weaponListCache = vm.weaponList;
    //匹配官方js中的阵容，并压入vm.chickenArmyPlus结果集
    // matchLOL();

    //拼接数据
    var getData = {
        //天选羁绊
        theOne: (vm.theOneJob) != 0 ? vm.theOneJob : vm.theOneRace,
        //队伍成员个数
        teamCount: vm.teamCount,
        forCount: vm.forCount,
        inChess: new Array(),
        banChess: new Array(),
        weapon: new Array(),
        costList: new Array(),
    };
    vm.inChessList.forEach((e) => {
        getData.inChess.push(parseInt(e.chessId));
    });
    vm.chessBanList.forEach((e) => {
        getData.banChess.push(parseInt(e.chessId));
    });
    vm.weaponList.forEach((e) => {
        getData.weapon.push((e.jobId) != '0' ? (parseInt(e.jobId) + 100) : parseInt(e.raceId));
    });
    // console.log(vm.chessValue);
    for (var i in vm.chessValue) {
        if (vm.chessValue[i]) {
            getData.costList.push(parseInt(i));
        }
    }
    //请求接口
    $.getJSON({
        url: "teamCalc",
        data: {
            data: JSON.stringify(getData),
        },
        success: function (ret) {
            if ('ok' == ret["msg"]) {
                displayPage(ret["data"]);
            } else {
                alert(ret["msg"]);
            }
        },
    });
});
function displayPage(teamArrObj){
    var chess = [];
    var group = [];
    teamArrObj.forEach((teamObj, index) => {
        chess = [];
        group = [];
        //chess
        teamObj.chess.forEach((chessId) => {
            chess.push(vm.chessArr[chessId]);
        });
        //group
        for(var key = 3; key >= 0; key--){
            for(var GId in teamObj.group[key]){
                group.push({
                    name: window.DATA_Ggroup[GId].name,
                    imagePath: window.DATA_Ggroup[GId].imagePath,
                    gid: GId,
                    id: (GId > 100) ? (GId - 100): GId,
                    count: teamObj.group[key][GId],
                    icoLevel: key + 1,
                });
            }
        }
        vm.chickenArmy.push({
            chess: chess,
            group: group,
            score: teamObj.score,
            tips: teamObj.tips,
            op: teamObj.op,
        });
    });
}
