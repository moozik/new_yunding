//最大武器个数
const WEAPON_MAX = 10;
//最大输入队伍成员数
const IN_HERO_MAX = 10;
window.DATA_race = {};
window.DATA_job = {};
window.DATA_Ggroup = {};
window.equipId2equip = {};
$.getJSON({
    url: "//game.gtimg.cn/images/lol/act/img/tft/js/chess.js",
    async: false,
    success: function (ret) {
        window.DATA_chess = ret;
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
    url: "//game.gtimg.cn/images/lol/act/img/tft/js/race.js",
    async: false,
    success: function (ret) {
        for (let i in ret.data) {
            ret.data[i].Ggroup = parseInt(ret.data[i].raceId);
            window.DATA_race[ret.data[i].raceId] = ret.data[i];
            window.DATA_Ggroup[ret.data[i].raceId] = ret.data[i];
        }
    },
});
$.getJSON({
    url: "//game.gtimg.cn/images/lol/act/img/tft/js/job.js",
    async: false,
    success: function (ret) {
        for (let i in ret.data) {
            ret.data[i].Ggroup = ret.data[i].jobId + 100;
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
        for (let i in ret.data) {
            if (ret.data[i].equipId < 500)
                continue;
            window.equipId2equip[ret.data[i].equipId] = ret.data[i];
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
            let equip, job, proStatus;
            // Set5.5转职纹章id列表，配置在https://lol.qq.com/tft/js/main.js?v=20210722
            const transJobEquipIdList = [533, 563, 575, 593, 599, 605, 609, 610, 611, 612, 613, 614, 615, 616, 617, 618, 619, 620, 621, 622, 623, 624];
            for (let i in DATA_equip.data) {
                equip = DATA_equip.data[i];
                //排除老版本装备
                if (equip.equipId < 500) {
                    continue;
                }
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
                        formula += window.equipId2equip[e].name + ' ';
                    });
                    equip.title += "\n配方：" + formula + proStatus;
                }
                //是否增强
                proStatus = ('无' === equip.proStatus) ? '' : (equip.proStatus);
                equip.title += "\n版本改动：" + proStatus;

                ret[equip.equipId] = equip;
            }
            return ret;
        }(),

        //当前选中羁绊
        groupCheckedId: 0,
        groupCheckedType: '',

        // theOneRace: 0,
        // theOneJob: 0,
        //当前羁绊组合
        groupList: [],
        //被ban英雄
        chessBanList: [],
        //当前选中英雄
        inChessList: [],
        //价值筛选
        chessValue: {1: true, 2: true, 3: true, 4: false, 5: false},
        //待计算个数
        teamCount: -1,
        //循环层数
        forCount: 3,
        //吃鸡阵容
        chickenArmy: [], //最后结果
        //官方推荐阵容
        // chickenArmyPlus: [],
        //转职装备
        weaponList: [],
        //转职装备 临时存储
        weaponListCache: [],
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
        checkGroupWeapon: function (weapon) {
            //屏蔽掉老版本
            if (weapon.equipId < 500) {
                return false;
            }
            //屏蔽非转职装备
            if (weapon.raceId === '0' && weapon.jobId === '0') {
                return false;
            }
            if (this.groupCheckedType === '') {
                return true;
            } else {
                if (this.groupCheckedType === 'job') {
                    if (weapon.jobId === this.groupCheckedId) {
                        return true;
                    }
                }
                if (this.groupCheckedType === 'race') {
                    if (weapon.raceId === this.groupCheckedId) {
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
            //clickChess
            ret = this.chessInArray(chess, inChessList);
            if (ret !== false) {
                //英雄已存在，删除
                inChessList.splice(ret, 1);
            } else {
                //英雄不存在，添加

                //10个英雄上限
                if (inChessList.length === IN_HERO_MAX) return;

                //添加
                inChessList.push(chess);
            }
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
                chessBanList.push(chess);
            }
        },
        //绑定转职装备
        clickWeapon: function (weapon) {
            if (this.weaponList.length < WEAPON_MAX) {
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
            let teamCount;
            if (this.inChessList.length + this.forCount > 7) {
                teamCount = 7;
            } else {
                teamCount = this.inChessList.length + this.forCount;
            }
            const ret = {1: false, 2: false, 3: false, 4: false, 5: false};
            for (let i in levelArr[teamCount]) {
                ret[levelArr[teamCount][i]] = true;
            }
            this.chessValue = ret;
        },
        //更新费用限制 by 阵容数量
        updateCostByTeamCount: function () {
            // var teamCount;
            // if (this.inChessList.length + this.forCount > 7) {
            //     teamCount = 7;
            // } else {
            //     teamCount = this.inChessList.length + this.forCount;
            // }
            // for(let i in this.chessValue){
            //     this.chessValue[i] = false;
            // }
            const ret = {1: false, 2: false, 3: false, 4: false, 5: false};
            for (let i in levelArr[this.teamCount]) {
                ret[levelArr[this.teamCount][i]] = true;
            }
            this.chessValue = ret;
        },
        // saveNiceTeam: function (chessList, index) {
        //     chessList.weapon = this.weaponListCache;
        //     chessList.teamname = $('.teamname' + index).val();
        //     $.post(
        //         "/yunding/yunding.php?action=niceTeam",
        //         {niceTeam: JSON.stringify(chessList)},
        //         function (result) {
        //             alert(result);
        //         }
        //     );
        // },
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
            // this.chickenArmyPlus.splice(0);
            this.weaponList.splice(0);
            this.groupCheckedId = 0;
            this.groupCheckedType = '';
            this.forCount = 3;
            this.teamCount = -1;
            // this.theOneJob = 0;
            // this.theOneRace = 0;
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
    if (typeof ret.recEquip != "undefined") {
        const tmp = ret.recEquip.split(",");
        tmp.forEach((equipId, index) => {
            ret.equip.push(window.equipId2equip[equipId].imagePath);
        });
    }
    $("#hero-box").html(template("heroTemp", ret));
    $("#hero-box").css("display", "block");
});
/**
 * 鼠标移入武器图标
 */
$(document).on("mouseenter", ".weaponBtn", function () {
    $("#app > div:nth-child(2) > div:nth-child(1) > div").css("display", "none");
    var weaponObj = window.equipId2equip[this.dataset.weaponid];
    weaponObj.formulaArr = [];
    if (weaponObj.formula != "") {
        weaponObj.formula.split(',').forEach((e, i) => {
            weaponObj.formulaArr.push(window.equipId2equip[e]);
        });
    }
    $("#weapon-box").html(template("weaponTemp", weaponObj));
    $("#weapon-box").css("display", "block");
});
$("#runBtn").click(function () {
    //删除原有数据
    vm.chickenArmy.splice(0);
    // vm.chickenArmyPlus.splice(0);
    vm.weaponListCache = vm.weaponList;
    //匹配官方js中的阵容，并压入vm.chickenArmyPlus结果集
    // matchLOL();

    //拼接数据
    const getData = {
        //天选羁绊
        // theOne: (vm.theOneJob) != 0 ? vm.theOneJob : vm.theOneRace,
        //队伍成员个数
        teamCount: parseInt(vm.teamCount),
        forCount: vm.forCount,
        inChess: [],
        banChess: [],
        weapon: [],
        costList: [],
    };
    vm.inChessList.forEach((e) => {
        getData.inChess.push(parseInt(e.chessId));
    });
    vm.chessBanList.forEach((e) => {
        getData.banChess.push(parseInt(e.chessId));
    });
    vm.weaponList.forEach((e) => {
        getData.weapon.push((e.jobId) !== '0' ? (parseInt(e.jobId) + 100) : parseInt(e.raceId));
    });
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
                // if(vm.theOneRace == GId || vm.theOneJob == GId){
                //     classStr += ' choose';
                // }
                group.push({
                    name: window.DATA_Ggroup[GId].name,
                    imagePath: window.DATA_Ggroup[GId].imagePath,
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
            op: teamObj.op,
        });
    });
}
