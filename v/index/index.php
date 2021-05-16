<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title><?= SEN::SITE['title'] ?></title>
    <link rel="icon" type="image/ico" href="<?= SEN::ico_url() ?>">
    <meta name="description" content="<?= SEN::SITE['description'] ?>" />
    <meta name="keywords" content="<?= SEN::SITE['keywords'] ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta data-n-head="true" data-hid="charset" charset="utf-8">
    <!-- 引入 Bootstrap -->
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <script>
        // var _hmt = _hmt || [];
        // (function() {
        //     var hm = document.createElement("script");
        //     hm.src = "https://hm.baidu.com/hm.js?8aca806cf882e7c0a56ac614d0fc724e";
        //     var s = document.getElementsByTagName("script")[0];
        //     s.parentNode.insertBefore(hm, s);
        // })();
    </script>
    <link rel="stylesheet" href="<?= SEN::static_url('css') ?>" charset="utf-8">
    <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
</head>
<!-- 
https://lol.qq.com/client/v2/index.html#/
https://lol.qq.com/act/a20200917tftset4/index.html
 -->
<body style="background-color: lightgray;">
    <div class="container" id="app" style="padding-top:10px;height:1500px;">
        <div class="row clearfix">
            <div class="col-md-12 column">
                <div class="jumbotron" style="padding: 2rem 2rem;">
                    <h1><i class="fa fa-gamepad"></i> <?= SEN::SITE['title'] ?></h1>
                    <h5 class="version">赛季：{{season}}</h5>
                    <h5 class="version">版本：{{version}}</h5>
                    <h5 class="version">更新时间：{{time}}</h5>

                    <p>
                        使用方法：1.在英雄池选择棋子 2.（可选）禁用英雄，转职装备，调整价格，调整计算个数 3. 点击计算
                    </p>
                    <p>
                        <span style="color:red;">建议pc端浏览器使用</span> <a href="https://moozik.cn/archives/807/">给我建议</a>
                    </p>
                </div>
            </div>
            <div class="col-md-12 column">

                <a href="https://lol.qq.com/tft/#/equipment" target="_blank"><button type="button" class="btn btn-primary btn-lg"><i class="fa fa-book"></i> 装备合成表</button></a>
                <a href="https://lol.qq.com/tft/#/index" target="_blank"><button type="button" class="btn btn-primary btn-lg"><i class="fa fa-qq"></i> 官方阵容推荐</button></a>
                <!-- <a href="/yunding/niceTeam.php"><button type="button" class="btn btn-primary btn-lg">推荐阵容</button></a> -->

                <?php
                if (IS_MANAGER) {
                    echo '<a href="./tools"><button type="button" class="btn btn-warning btn-lg">管理</button></a>';
                }
                ?>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-md-4 column" style="height:410px;">
                <div id="synergies-box">

                </div>
                <div class="clearfix pop pop1" id="pop1">
                </div>
            </div>

            <div class="col-md-4 column">
                <span class="large-title">特质</span>
                <div class="group-list">
                    <div v-for="(group,index) in raceArr" v-on:click.left="clickGroup(group)" class="groupBtn btn-choose" :class="isGroupHover(group)" :title="group.name" :data-raceId="group.raceId" data-type="race">
                        <span class="group_span"><img class="group_span_img" :src="group.imagePath" /></span>
                        <span class="group_name">{{group.name}}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 column">
                <span class="large-title">职业</span>
                <div class="group-list">
                    <div v-for="(group,index) in jobArr" v-on:click.left="clickGroup(group)" class="groupBtn btn-choose" :class="isGroupHover(group)" :title="group.name" :data-jobId="group.jobId" data-type="job">
                        <span class="group_span"><img class="group_span_img" :src="group.imagePath" /></span>
                        <span class="group_name">{{group.name}}</span>
                    </div>
                </div>
            </div>

        </div>
        <div class="row clearfix">
            <div class="col-md-4 column">
                <span class="large-title">已选阵容</span><span>(英雄池左键添加，再次点击取消)</span>
                <div class="lineTwo" style="min-height: 50px;">
                    <div class="chess-list">
                        <div :title="chess.description" v-for="chess in inChessList" v-on:click="clickChess(chess)" :class="'hi_'+chess.TFTID">
                        </div>
                    </div>
                </div>

                <span class="large-title">禁用英雄</span><span>(英雄池右键添加，再次点击取消)</span>
                <div class="lineTwo" style="min-height: 50px;">
                    <div class="chess-list">
                        <div :title="chess.description" v-for="chess in chessBanList" v-on:click="banChess(chess)" :class="'hi_'+chess.TFTID">
                        </div>
                    </div>
                </div>

                <span class="large-title">已选装备</span><span>(将计算转职装备，再次点击取消)</span>
                <div class="lineTwo" style="min-height: 50px;">
                    <div class="chess-list">
                        <div :title="weapon.title" v-for="(weapon,index) in weaponList" v-on:click="delWeapon(index)">
                            <img :src="weapon.imagePath" />
                        </div>
                    </div>
                </div>

                <span class="large-title" style="color:darkcyan;">天选之人(羁绊+1)</span>
                <span>特质:</span>
                <select v-model="theOneRace" class="form-control" v-on:change="theOneJob = 0">
                    <option value="0">-</option>
                    <option v-for="(group,index) in raceArr" :value="parseInt(group.raceId)">{{group.name}}</option>
                </select>
                <span>职业:</span>
                <select v-model="theOneJob" class="form-control" v-on:change="theOneRace = 0">
                    <option value="0">-</option>
                    <option v-for="(group,index) in jobArr" :value="parseInt(group.jobId) + 100">{{group.name}}</option>
                </select>
            </div>

            <div class="col-md-8 column">
                <span class="large-title">英雄池</span>
                <p style="font-size:14px;">左键添加到'已选阵容'，右键添加到'禁用英雄'。再次点击可以取消选择或取消禁用。</p>
                <div style="min-height:270px">
                    <div class="chess-list" v-for="price in 5">
                        <div :title="chess.description" v-for="chess in chessArr" v-if="checkGroupChess(chess, price)" v-on:click.left="clickChess(chess)" @contextmenu.prevent="banChess(chess)" :data-chessId="chess.chessId" class="chessBtn" :class="'hi_'+chess.TFTID">
                        </div>
                    </div>
                </div>
                <span class="large-title">转职装备</span>
                <p style="font-size:14px;">装备可以重复选择，点击左侧'已选装备'可以取消选择。</p>
                <div class="chess-list">
                    <div :title="weapon.title" v-for="(weapon,index) in equipArr" v-if="weapon.TFTID > 174 && weapon.raceId != 0" v-on:click.left="clickWeapon(weapon)">
                        <img :src="weapon.imagePath" />
                    </div>
                </div>
                <div class="chess-list">
                    <div :title="weapon.title" v-for="(weapon,index) in equipArr" v-if="weapon.TFTID > 174 && weapon.jobId != 0" v-on:click.left="clickWeapon(weapon)">
                        <img :src="weapon.imagePath" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-lg" id="runBtn"><i class="fa fa-bomb"></i> 计算</button>
                    <button type="button" class="btn btn-secondary btn-lg" v-on:click="clearBtn()"><i class="fa fa-trash-o fa-lg"></i> 清空</button>
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="col-md-4 column">
                <span class="large-title">筛选价格</span>
                <div class="btn-group btn-group-toggle" data-toggle="buttons"></div>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[1]}" v-on:click="valBtn(1)">1 <i class="fa fa-rmb"></i></button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[2]}" v-on:click="valBtn(2)">2 <i class="fa fa-rmb"></i></button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[3]}" v-on:click="valBtn(3)">3 <i class="fa fa-rmb"></i></button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[4]}" v-on:click="valBtn(4)">4 <i class="fa fa-rmb"></i></button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[5]}" v-on:click="valBtn(5)">5 <i class="fa fa-rmb"></i></button>
                
                <span class="large-title">筛选英雄个数</span>

                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-primary" v-on:click="forCountBtn(0)">
                        <input type="radio" name="options" v-model="forCount" value="0"> 原样输出
                    </label>
                    <label class="btn btn-primary" v-on:click="forCountBtn(1)">
                        <input type="radio" name="options" v-model="forCount" value="1"> 一个
                    </label>
                    <label class="btn btn-primary" v-on:click="forCountBtn(2)">
                        <input type="radio" name="options" v-model="forCount" value="2"> 两个
                    </label>
                    <label class="btn btn-primary active" v-on:click="forCountBtn(3)">
                        <input type="radio" name="options" v-model="forCount" value="3" checked> 三个
                    </label>
                </div>
                <span> {{ forCount }}个</span>
                <?php if(SEN::isDevelop()){?>
                    <span class="large-title">阵容棋子数</span>
                <input type="range" class="form-control-range" v-model="teamCount" max="9" min="-1" step="1" v-on:mousemove="updateCostByTeamCount()">
                <span> {{ teamCount }}个</span>
                <?php }?>
            </div>

            <div class="col-md-8 column">
                <span class="large-title">最优阵容</span>
                <div v-for="army in chickenArmy" class="traits">
                    <!--英雄列表-->
                    <div class="chess-list result">
                        <div :title="chessItem.description" v-for="chessItem in army.chess" :class="'hi_'+chessItem.TFTID">
                        </div>
                    </div>
                    <div class="result-jiban result">
                        <div v-for="(item,index) in army.group" :class="item.classStr">
                            <img :src="item.imagePath" />
                            <span>{{item.count}}{{item.name}}</span>
                        </div>
                    </div>
                    <div class="chess-list result">
                        <!-- <button type="button" class="btn btn-secondary" disabled="disabled">分数:{{army.score}}</button> -->
                        <button type="button" class="btn btn-info" disabled="disabled">强度:{{army.op}}</button> -->
                        <button v-if="army.tips" type="button" class="btn btn-success" disabled="disabled">{{army.tips}}</button>
                    </div>

                </div>
            </div>

        </div>

    </div>
    <!--app-->

    <!-- 阵容特质详情模板 -->
    <script id="jobPopTemp" type="text/html">
        <div class="type">
            <span class="group_span" style="float: left;">
                <img src="{{imagePath}}" class="group_span_img">
            </span>
            <p>{{name}}</p>
            <p>{{introduce}}</p>
        </div>
        <div class="content">
            {{each level as v i}}
            <p><span>{{i}}</span><span>{{v}}</span></p>
            {{/each}}
        </div>
    </script>
    <!-- 英雄详情模板 -->
    <script id="ChampionPop2" type="text/html">
        <div class="details">
            <div class="hi_{{TFTID}}" style="background-size: cover;"></div>
            <p><span>{{title}} {{displayName}}<span class="glyphicon glyphicon-sort-by-order-alt"></span></span><span>{{races}},{{jobs}}</span><span>{{price}}金币</span></p>
        </div>
        <!-- {{if equip}}
        <div class="recommend">
            <p class="title">推荐装备</p>
            <div class="champions">
                {{each equip as value index}}
                <img src="//game.gtimg.cn/images/lol/act/img/tft/equip/{{value}}.png" alt="" />
                {{/each}}
            </div>
        </div>
        {{/if}} -->
        <div class="skill">
            <p class="title">技能</p>
            <div class="info">
                <img src="{{skillImage}}" alt="" />
                <div class="name">
                    <span>{{skillName}}</span>
                    <!-- <span>{{skillType}}</span> -->
                </div>
                <p class="description">{{skillIntroduce}}</p>
            </div>
        </div>
    </script>
    <!-- jQuery (Bootstrap 的 JavaScript 插件需要引入 jQuery) -->
    <script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
    <!-- 包括所有已编译的插件 -->
    <script src="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- <script src="https://cdn.bootcss.com/vue/2.6.10/vue.min.js"></script> -->
    <script src="https://cdn.staticfile.org/vue/2.4.2/vue.min.js"></script>
    <!-- <script src="https://cdn.staticfile.org/vue-resource/1.5.1/vue-resource.min.js"></script> -->
    <!--模板框架-->
    <script src="//ossweb-img.qq.com/images/js/ArtTemplate.js"></script>
    <!-- <script src="https://lol.qq.com/act/AutoCMS/publish/LOLAct/TFTlinelist/TFTlinelist.js"></script> -->
    <!--阵容推荐-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTLineup_V3/TFTLineup_V3.js"></script>-->
    <!--所有英雄列表-->
    <!--<script src="http://game.gtimg.cn/images/lol/act/img/js/chessList/chess_list.js"></script>-->
    <!--元素效果-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTrace/TFTrace.js"></script>-->
    <!--职业效果-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTjob/TFTjob.js"></script>-->
    <!--英雄-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTChessesData_V3/TFTChessesData_V3.js"></script>-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTChessesData/TFTChessesData.js"></script>-->

    <!--装备-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTequipment_V3/TFTequipment_V3.js"></script>-->
    <!--配置-->
    <script src="<?= SEN::static_url('define') ?>"></script>
    <!--vue-->
    <script src="<?= SEN::static_url('frame') ?>"></script>
    <script>
        // $.getJSON({
        //     url: '/yunding/default.json',
        //     success: function(ret) {
        //         displayPage(ret['data']);
        //     },
        // });
    </script>
</body>
</html>