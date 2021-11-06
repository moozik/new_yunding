//最大武器个数
const EQUIP_MAX = 10;
//最大输入队伍成员数
const IN_HERO_MAX = 10;
const BAN_HERO_MAX = 10;
const CHESS_PRICE_FALSE = {1: false, 2: false, 3: false, 4: false, 5: false};
const CHESS_PRICE_DEFAULT = {1: true, 2: true, 3: true, 4: false, 5: false};
//不同级别对应能刷出的英雄价格
const LEVEL_MAP={"1":[1],"2":[1],"3":[1,2],"4":[1,2,3],"5":[1,2,3,4],"6":[1,2,3,4],"7":[1,2,3,4,5],"8":[1,2,3,4,5],"9":[1,2,3,4,5],"10":[1,2,3,4,5]};
// Set5.5转职纹章id列表，配置在https://lol.qq.com/tft/js/main.js?v=20210722
const transJobEquipIdList = [6001,6002,6003,6004,6005,6006,6007,6008];
const DATA_race = {};
const DATA_job = {};
const DATA_Ggroup = {};
const DATA_Equip = {};
const GAME_URL = "//game.gtimg.cn/images/lol/act/img/tft/js";
$.getJSON({
    url: GAME_URL + "/chess.js",
    async: false,
    success: function (ret) {
        DATA_chess = ret;
        $.ajax({
            type: "POST",
            url: "index/CheckVersion",
            contentType: "application/json;charset=utf-8",
            data: JSON.stringify(ret),
            dataType: "json",
            success: function (message) {
                console.log(message);
            }
        });
    },
});
$.getJSON({
    url: GAME_URL + "/race.js",
    async: false,
    success: function (ret) {
        for (let i in ret.data) {
            ret.data[i].Ggroup = parseInt(ret.data[i].raceId);
            DATA_race[ret.data[i].raceId] = ret.data[i];
            DATA_Ggroup[ret.data[i].raceId] = ret.data[i];
        }
    },
});
$.getJSON({
    url: GAME_URL + "/job.js",
    async: false,
    success: function (ret) {
        for (let i in ret.data) {
            ret.data[i].Ggroup = ret.data[i].jobId + 100;
            DATA_job[ret.data[i].jobId] = ret.data[i];
            DATA_Ggroup[parseInt(ret.data[i].jobId) + 100] = ret.data[i];
        }
    },
});
$.getJSON({
    url: GAME_URL + "/equip.js",
    async: false,
    success: function (ret) {
        for (let i in ret.data) {
            // if (ret.data[i].equipId < 6000)
            //     continue;
            DATA_Equip[ret.data[i].equipId] = ret.data[i];
        }
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
        chessArr: function () {
            const ret = {};
            let chess = {};
            // var detail;
            let proStatus;
            for (let i in DATA_chess.data) {
                chess = DATA_chess.data[i];
                chess.fullName = chess.title + ' ' + chess.displayName;

                //是否增强
                proStatus = ('无' === chess.proStatus) ? '' : ("\n版本改动：" + chess.proStatus);
                //描述
                chess.description = '名称：' + chess.fullName + "\n职业：" + chess.races + ' ' + chess.jobs + "\n\n技能：" + chess.skillIntroduce + proStatus;
                chess.jobIds = chess.jobIds.split(',');
                chess.raceIds = chess.raceIds.split(',');
                ret[DATA_chess.data[i].chessId] = chess;
            }
            return ret;
        }(),
        //装备
        equipArr: function () {
            const ret = {};
            let equip, job;
            for (let i in DATA_Equip) {
                equip = DATA_Equip[i];
                if (transJobEquipIdList.indexOf(+equip.equipId) === -1) {
                    continue;
                }
                //只要转职装备
                if ((equip.jobId === '0' || equip.jobId == null) &&
                    (equip.raceId === '0' || equip.raceId == null)) {
                    continue;
                }
                equip.title = '名称：' + equip.name;
                //装备id不在阵容列表里，跳出
                if (!DATA_race[equip.raceId] && !DATA_job[equip.jobId]) {
                    continue;
                }
                //转职类型
                if (equip.jobId > 0) {
                    job = DATA_job[equip.jobId].name;
                } else {
                    job = DATA_race[equip.raceId].name;
                }
                equip.title += "\n职业：" + job;
                //装备配方
                if (equip.formula !== "") {
                    var formula = '';
                    equip.formula.split(',').forEach((e, i) => {
                        formula += DATA_Equip[e].name + ' ';
                    });
                    equip.title += "\n配方：" + formula;
                }
                ret[equip.equipId] = equip;
            }
            return ret;
        }(),

        //当前选中羁绊
        groupCheckedId: 0,
        groupCheckedType: '',
        //当前羁绊组合
        groupList: [],
        //被ban英雄
        chessBanList: [],
        //当前选中英雄
        inChessList: [],
        //人口数量
        positionCount : 0,
        //价值筛选
        chessValue: CHESS_PRICE_DEFAULT,
        //待计算个数
        teamCount: -1,
        //循环层数
        forCount: 3,
        //吃鸡阵容
        chickenArmy: [], //最后结果
        //转职装备
        equipList: [],
        //转职装备 临时存储
        // equipListCache: [],
    },
    methods: {
        //判断指定英雄是否属于当前组别
        checkGroupChess: function (chess, price) {
            //金额组别不对
            if (chess.price != price) {
                return false;
            }
            //未筛选羁绊
            if (this.groupCheckedId === 0) {
                return true;
            }
            if (this.groupCheckedType === 'job') {
                if (-1 === chess.jobIds.indexOf(this.groupCheckedId)) {
                    return false;
                }
            }
            if (this.groupCheckedType === 'race') {
                if (-1 === chess.raceIds.indexOf(this.groupCheckedId)) {
                    return false;
                }
            }
            return true;
        },
        //判断指定羁绊是否展示
        checkGroupEquip: function (equip) {
            //屏蔽掉老版本
            if (equip.equipId < 500) {
                return false;
            }
            //屏蔽非转职装备
            if (equip.raceId === '0' && equip.jobId === '0') {
                return false;
            }
            if (this.groupCheckedType === '') {
                return true;
            } else {
                if (this.groupCheckedType === 'job') {
                    if (equip.jobId === this.groupCheckedId) {
                        return true;
                    }
                }
                if (this.groupCheckedType === 'race') {
                    if (equip.raceId === this.groupCheckedId) {
                        return true;
                    }
                }
                return false;
            }
        },
        //判断羁绊筛选按钮是否亮起
        isGroupHover: function (group) {
            if (group.raceId && group.raceId === this.groupCheckedId && 'race' === this.groupCheckedType) {
                return "on";
            }
            if (group.jobId && group.jobId === this.groupCheckedId && 'job' === this.groupCheckedType) {
                return "on";
            }
            return '';
        },
        //点击羁绊按钮 切换英雄筛选
        clickGroup: function (group) {
            if (group.raceId) {
                if (group.raceId === this.groupCheckedId && 'race' === this.groupCheckedType) {
                    this.groupCheckedId = 0;
                    this.groupCheckedType = '';
                } else {
                    this.groupCheckedId = group.raceId;
                    this.groupCheckedType = 'race';
                }
            }
            if (group.jobId) {
                if (group.jobId === this.groupCheckedId && 'job' === this.groupCheckedType) {
                    this.groupCheckedId = 0;
                    this.groupCheckedType = '';
                } else {
                    this.groupCheckedId = group.jobId;
                    this.groupCheckedType = 'job';
                }
            }
        },
        //绑定英雄池左键
        clickChess: function (chess) {
            inChessList = this.inChessList;
            if(chess.races == "约德尔大王"){
                //不允许添加
                return;
            }
            //clickChess
            ret = this.chessInArray(chess, inChessList);
            if (ret !== false) {
                //英雄已存在，删除
                inChessList.splice(ret, 1);
            } else {
                //英雄不存在，添加

                //10个英雄上限
                if (this.positionCount === IN_HERO_MAX) return;

                //添加
                inChessList.push(chess);
            }
            //更新人口
            this.positionCount = this.inChessListLength();
            //刷新金额限制
            if (this.teamCount === -1) {
                this.updateCost();
            }
        },
        //绑定英雄池右键
        banChess: function (chess) {
            let chessBanList = this.chessBanList;
            //banChess
            ret = this.chessInArray(chess, chessBanList);
            if (ret !== false) {
                chessBanList.splice(ret, 1);
            } else {
                if (this.chessBanList.length === BAN_HERO_MAX) return;
                chessBanList.push(chess);
            }
        },
        //绑定转职装备
        clickEquip: function (equip) {
            if (this.equipList.length < EQUIP_MAX) {
                this.equipList.push(equip);
            }
        },
        //删除转职装备
        delEquip: function (index) {
            this.equipList.splice(index, 1);
        },
        //修改金额
        forCountBtn: function (forCount) {
            this.forCount = forCount;
            this.updateCost();
        },
        //更新英雄价格区间
        updateCost: function () {
            let teamCount;
            if (this.positionCount + this.forCount > 7) {
                teamCount = 7;
            } else {
                teamCount = this.positionCount + this.forCount;
            }
            const ret = CHESS_PRICE_FALSE;
            for (let i in LEVEL_MAP[teamCount]) {
                ret[LEVEL_MAP[teamCount][i]] = true;
            }
            this.chessValue = ret;
        },
        //获取英雄占用格子数 特殊考虑巨像
        inChessListLength: function() {
            var ret = this.inChessList.length;
            this.inChessList.forEach(chess => {
                if(chess.jobIds.indexOf("5") != -1){
                    ret++;
                }
            });
            return ret;
        },
        //判断指定英雄是否在指定数组中 不存在返回false，存在返回下标
        chessInArray: function (chess, arr) {
            key = false;
            arr.forEach((chessItem, index) => {
                if (chess.chessId === chessItem.chessId) {
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
            this.chessBanList.splice(0);
            this.chickenArmy.splice(0);
            this.equipList.splice(0);
            this.groupCheckedId = 0;
            this.groupCheckedType = '';
            this.forCount = 3;
            this.teamCount = -1;
            // this.theOneJob = 0;
            // this.theOneRace = 0;
            this.chessValue = CHESS_PRICE_DEFAULT;
            //更新人口
            this.positionCount = 0;
        },

        //更新费用限制 by 阵容数量 debug中的功能
        updateCostByTeamCount: function () {
            ret = CHESS_PRICE_FALSE;
            for (let i in LEVEL_MAP[this.teamCount]) {
                ret[LEVEL_MAP[this.teamCount][i]] = true;
            }
            this.chessValue = ret;
        },
    },
});
/**
 * 鼠标移入羁绊按钮
 */
$(document).on("mouseenter", ".groupBtn", function () {
    let data;
    $("#app > div:nth-child(2) > div:nth-child(1) > div").css("display", "none");
    const id = $(this).attr("data-raceId") || $(this).attr("data-jobId");
    if ($(this).attr("data-raceId")) {
        data = vm.raceArr[id];
    } else {
        data = vm.jobArr[id];
    }
    $("#group-box").html(template("groupTemp", data));
    $("#group-box").css("display", "block");
});
/**
 * 鼠标移入英雄图标
 */
$(document).on("mouseenter", ".chess", function () {
    $("#app > div:nth-child(2) > div:nth-child(1) > div").css("display", "none");
    const chess = DATA_chess.data;
    const ret = vm.chessArr[$(this).attr("data-chessId")];
    ret.equip = [];
    if (typeof ret.recEquip != "undefined" && ret.recEquip != "") {
        const tmp = ret.recEquip.split(",");
        tmp.forEach((equipId, index) => {
            ret.equip.push(DATA_Equip[equipId].imagePath);
        });
    }
    $("#hero-box").html(template("heroTemp", ret));
    $("#hero-box").css("display", "block");
});
/**
 * 鼠标移入武器图标
 */
$(document).on("mouseenter", ".equipBtn", function () {
    $("#app > div:nth-child(2) > div:nth-child(1) > div").css("display", "none");
    var equipObj = DATA_Equip[this.dataset.equipid];
    equipObj.formulaArr = [];
    if (equipObj.formula != "") {
        equipObj.formula.split(',').forEach((e, i) => {
            equipObj.formulaArr.push(DATA_Equip[e]);
        });
    }
    $("#equip-box").html(template("equipTemp", equipObj));
    $("#equip-box").css("display", "block");
});
$("#runBtn").click(function () {
    //删除原有数据
    vm.chickenArmy.splice(0);
    // vm.equipListCache = vm.equipList;

    //拼接数据
    const getData = {
        //天选羁绊
        // theOne: (vm.theOneJob) != 0 ? vm.theOneJob : vm.theOneRace,
        //队伍成员个数
        teamCount: parseInt(vm.teamCount),
        forCount: vm.forCount,
        inChess: [],
        banChess: [],
        equip: [],
        tagPlus: [],
        costList: [],
    };
    vm.inChessList.forEach((e) => {
        getData.inChess.push(parseInt(e.chessId));
    });
    vm.chessBanList.forEach((e) => {
        getData.banChess.push(parseInt(e.chessId));
    });
    vm.equipList.forEach((e) => {
        getData.equip.push(parseInt(e.equipId));
        if (e.jobId !== '0' && e.jobId !== null) {
            getData.tagPlus.push(parseInt(e.jobId) + 100);
        } else {
            getData.tagPlus.push(parseInt(e.raceId));
        }
    });
    for (let i in vm.chessValue) {
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
            if ('ok' === ret["msg"]) {
                displayPage(ret["data"]);
            } else {
                alert(ret["msg"]);
            }
        },
    });
});

function displayPage(teamArrObj) {
    //删除原有数据
    vm.chickenArmy.splice(0);
    let chess = [];
    let group = [];
    let classStr = '';
    teamArrObj.forEach((teamObj, index) => {
        chess = [];
        group = [];
        //chess
        teamObj.chess.forEach((chessId) => {
            chess.push(vm.chessArr[chessId]);
        });
        //group
        for (let key = 4; key >= 1; key--) {
            for (const GId in teamObj.group[key]) {
                classStr = 'grade' + key;
                group.push({
                    name: DATA_Ggroup[GId].name,
                    imagePath: DATA_Ggroup[GId].imagePath,
                    gid: GId,
                    id: (GId > 100) ? (GId - 100) : GId,
                    count: teamObj.group[key][GId],
                    classStr: classStr,
                });
            }
        }
        vm.chickenArmy.push({
            chess: chess,
            group: group,
            score: teamObj.score,
            tips: teamObj.tips,
        });
    });
}
