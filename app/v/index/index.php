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
    <link rel="stylesheet" href="<?php echo SEN::static_url('css').'?t='.$timeStamp ?>" charset="utf-8">
    <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?23890e3d71489efc6db65a67bdfe0759";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
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
                        使用方法：1.在英雄池选择棋子 2.禁用不想选用的英雄，选择当前转职装备，调整目标价格，调整计算个数 3. 点击计算
                    </p>
                    <p>
                        <span style="color:red;">建议pc端浏览器使用</span> <a href="https://moozik.cn/archives/807/">给我建议</a>
                    </p>

                    <!-- http://www.fontawesome.com.cn/faicons/ -->
                    <a href="https://lol.qq.com/tft/#/equipment" target="_blank"><button type="button" class="btn btn-primary btn-lg"><i class="fa fa-book"></i> 装备合成表</button></a>
                    <a href="https://lol.qq.com/tft/#/index" target="_blank"><button type="button" class="btn btn-primary btn-lg"><i class="fa fa-qq"></i> 官方阵容推荐</button></a>
                    <a href="https://lol.qq.com/tft/#/overview" target="_blank"><button type="button" class="btn btn-primary btn-lg"><i class="fa fa-eye"></i> 版本资料</button></a>
                </div>
            </div>
        </div>
        <div class="row clearfix">

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

            <div class="col-md-4 column" style="height:340px;">
                <div id="group-box" style="display: none;">
                </div>
                <div id="hero-box" style="display: none;">
                </div>
                <div id="equip-box" style="display: none;">
                </div>
            </div>


        </div>
        <div class="row clearfix">
            <div class="col-md-8 column">
                <span class="large-title" title="左键添加到'已选阵容'，右键添加到'禁用英雄'。再次点击可以取消选择或取消禁用。">英雄池</span>
                <div style="min-height:270px">
                    <div class="chess-list" v-for="price in 6">
                        <div v-for="chess in chessArr" v-if="checkGroupChess(chess, price)" class="chess_head" :class="'price_' + chess.price" v-on:click.left="pickChess(chess)" @contextmenu.prevent="banChess(chess)">
                            <div class="chess" :style="headImage(chess.TFTID)" :data-chessId="chess.chessId" :title="chess.description">
                            </div>
                            <div class="cost_tag">{{chess.price}}</div>
                        </div>
                    </div>
                </div>
                <span class="large-title" title="装备可以重复选择，点击左侧'已选装备'可以取消选择。">转职装备</span>
                <div class="chess-list">
                    <div class="equipBtn equip" :title="equip.title" :data-equipId="equip.equipId" v-for="(equip,index) in equipArr" v-if="equip.raceId != null && equip.raceId != 0 && checkGroupEquip(equip)" v-on:click.left="clickEquip(equip)">
                        <img :src="equip.imagePath" />
                    </div>
                </div>
                <div class="chess-list">
                    <div class="equipBtn equip" :title="equip.title" :data-equipId="equip.equipId" v-for="(equip,index) in equipArr" v-if="equip.jobId != null && equip.jobId != 0 && checkGroupEquip(equip)" v-on:click.left="clickEquip(equip)">
                        <img :src="equip.imagePath" />
                    </div>
                </div>

                <span class="large-title" style="color:darkcyan;">海克斯科技</span>
                <span>海克斯之心(羁绊+1):</span>
                <select v-model="hexType1" class="form-control">
                    <option value="">-</option>
                    <option v-for="(item,index) in hexArr" :value="item.name" :title="item.description" v-if="item.type == 1">{{item.name}}</option>
                </select>
                <span>海克斯之魂(羁绊+2):</span>
                <select v-model="hexType3" class="form-control">
                    <option value="">-</option>
                    <option v-for="(item,index) in hexArr" :value="item.name" :title="item.description" v-if="item.type == 3">{{item.name}}</option>
                </select>
            </div>

            <div class="col-md-4 column">
                <span class="large-title">已选英雄(人口:{{positionCount}})</span><span>(英雄池左键添加，再次点击取消)</span>
                <div class="lineTwo" style="min-height: 50px;">
                    <div class="chess-list">
                        <!-- 选用英雄列表 -->
                        <div v-for="chess in inChessList" class="chess_head" :class="'price_' + chess.price" v-on:click.left="pickChess(chess)">
                            <div class="chess" :style="headImage(chess.TFTID)" :data-chessId="chess.chessId" :title="chess.description">
                            </div>
                            <div class="cost_tag">{{chess.price}}</div>
                        </div>
                    </div>
                </div>

                <span class="large-title">禁用英雄</span><span>(英雄池右键添加，再次点击取消)</span>
                <div class="lineTwo" style="min-height: 50px;">
                    <div class="chess-list">
                        <!-- 禁用英雄列表 -->
                        <div v-for="chess in chessBanList" class="chess_head" :class="'price_' + chess.price" v-on:click.left="banChess(chess)">
                            <div class="chess" :style="headImage(chess.TFTID)" :data-chessId="chess.chessId" :title="chess.description">
                            </div>
                            <div class="cost_tag">{{chess.price}}</div>
                        </div>
                    </div>
                </div>

                <span class="large-title">已选装备</span><span>(将计算转职装备，再次点击取消)</span>
                <div class="lineTwo" style="min-height: 50px;">
                    <div class="chess-list">
                        <div class="equip" :title="equip.title" v-for="(equip,index) in equipList" v-on:click="delEquip(index)">
                            <img :src="equip.imagePath" />
                        </div>
                    </div>
                </div>

                <span class="large-title">可选价格</span>
                <div class="btn-group btn-group-toggle" data-toggle="buttons"></div>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[1]}" v-on:click="valBtn(1)">1 <i class="fa fa-rmb"></i></button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[2]}" v-on:click="valBtn(2)">2 <i class="fa fa-rmb"></i></button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[3]}" v-on:click="valBtn(3)">3 <i class="fa fa-rmb"></i></button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[4]}" v-on:click="valBtn(4)">4 <i class="fa fa-rmb"></i></button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':chessValue[5]}" v-on:click="valBtn(5)">5 <i class="fa fa-rmb"></i></button>
                
                <span class="large-title">计算英雄个数</span>

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
                    <!-- <span class="large-title">阵容棋子数</span>
                <input type="range" class="form-control-range" v-model="teamCount" max="9" min="-1" step="1" v-on:mousemove="updateCostByTeamCount()">
                <span> {{ teamCount }}个</span> -->
                <?php }?>
                <div class="modal-body">
                    <button type="button" class="btn btn-primary btn-lg" id="runBtn"><i class="fa fa-bomb"></i> 计算</button>
                    <button type="button" class="btn btn-secondary btn-lg" v-on:click="clearBtn()"><i class="fa fa-trash-o fa-lg"></i> 清空</button>
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="col-md-12 column">
                <span class="large-title">最优阵容</span>
                <div v-for="army in chickenArmy" class="team_group">
                    <!--英雄列表-->
                        <div class="chess-list" style="min-width: 570px;">>
                            <div v-for="chess in army.chess" class="chess_head" :class="'price_' + chess.price">
                                <div class="chess" :style="headImage(chess.TFTID)" :data-chessId="chess.chessId" :title="chess.description">
                                </div>
                                <div class="cost_tag">{{chess.price}}</div>
                            </div>
                        </div>
                        <div class="result-jiban" style="min-width: 570px;">
                            <div v-for="(item,index) in army.group" :class="item.classStr">
                                <img :src="item.imagePath" />
                                <span>{{item.count}}{{item.name}}</span>
                            </div>
                        </div>
                        <div class="chess-list" style="min-width: 570px;">
                            <button type="button" class="btn btn-secondary" disabled="disabled">分数:{{army.score}}</button>
                            <button v-if="army.tips" type="button" class="btn btn-success" disabled="disabled">{{army.tips}}</button>
                        </div>
                </div>
            </div>

        </div>

    </div>
    <!--app-->

    <!-- 阵容特质详情模板 -->
    <script id="groupTemp" type="text/html">
        <div class="type">
            <span class="group_span" style="float: left;">
                <img src="{{imagePath}}" class="group_span_img">
            </span>
            <p>{{name}}</p>
            <p>{{introduce}}</p>
        </div>
        <div class="content">
            {{each level as v i}}
            <p style="margin-top:0px;"><span>{{i}}</span><span>{{v}}</span></p>
            {{/each}}
        </div>
    </script>
    <!-- 英雄详情模板 -->
    <script id="heroTemp" type="text/html">
        <div class="details">
            <div style="background: url(//game.gtimg.cn/images/lol/act/img/tft/champions/{{TFTID}}.png);background-size: cover;"></div>
            <p><span>{{title}} {{displayName}}<span class="glyphicon glyphicon-sort-by-order-alt"></span></span><span>{{races}},{{jobs}}</span></p>
        </div>
        {{if equip}}
        <div class="recommend">
            <p class="title">推荐装备</p>
            <div class="champions">
                {{each equip as value index}}
                <img class="equipBox" src="{{value}}" />
                {{/each}}
            </div>
        </div>
        {{/if}}
        <div class="skill">
            <p class="title">技能</p>
            <div class="info">
                <img src="{{skillImage}}" alt="" />
                <div class="name">
                    <span>{{skillName}}</span>
                </div>
                <!-- <p class="description">{{skillDetail}}</p> -->
            </div>
        </div>
    </script>
    <!-- 转职装备详情模板 -->
    <script id="equipTemp" type="text/html">
        <p class="title">装备名称</p>
            <p style="color:#fff;">{{name}}</p>
        <p class="title">装备配方</p>
            <div class="champions">
                {{each formulaArr as value index}}
                <img class="equipBox" src="{{value.imagePath}}" title="{{value.name}}" />
                {{/each}}
            </div>
    </script>
    <!-- jQuery (Bootstrap 的 JavaScript 插件需要引入 jQuery) -->
    <script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
    <!-- 包括所有已编译的插件 -->
    <script src="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <?php if(SEN::isDevelop()){?>
        <script src="https://unpkg.com/vue@2.6.14/dist/vue.js"></script>
    <?php } else {?>
        <script src="https://unpkg.com/vue@2.6.14/dist/vue.min.js"></script>
    <?php }?>
    <!--模板框架-->
    <script src="//ossweb-img.qq.com/images/js/ArtTemplate.js"></script>
    <!--配置-->
    <!-- <script src="<?php echo SEN::static_url('define').'?t='.$timeStamp ?>"></script> -->
    <!--vue-->
    <script src="<?php echo SEN::static_url('frame').'?t='.$timeStamp ?>"></script>
</body>
</html>