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
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?8aca806cf882e7c0a56ac614d0fc724e";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <link rel="stylesheet" href="<?= SEN::static_url('css') ?>" charset="utf-8">
</head>

<body style="background-color: lightgray;">
    <div class="container" id="app" style="padding-top:10px;height:1500px;">
        <div class="row clearfix">
            <div class="col-md-12 column">
                <div class="jumbotron" style="padding: 2rem 2rem;">
                    <h1><?= SEN::SITE['title'] ?></h1>
                    <p>
                        指标意义：阵容分数 = 羁绊价值+英雄价值。强度 = 分数/人数。强度代表平均到每个英雄的价值，代表了当前人数的强度值。
                    </p>
                    <p>
                        使用方法：1.在英雄池选择棋子 2.（可选）禁用英雄，转职装备，调整价格，调整计算个数 3. 点击计算
                    </p>
                    <p>
                        手机端长按图标可添加到禁用英雄.<span style="color:red;">建议pc端浏览器使用</span> <a href="https://moozik.cn/archives/807/">给我建议</a>
                    </p>
                </div>
            </div>
            <div class="col-md-12 column">

                <a href="https://101.qq.com/tft/index.shtml?ADTAG=cooperation.glzx.tft&type=items" target="_blank"><button type="button" class="btn btn-primary btn-lg">装备合成表</button></a>

                <a href="https://101.qq.com/tft/index.shtml?ADTAG=cooperation.glzx.tft&type=strategy" target="_blank"><button type="button" class="btn btn-primary btn-lg">云顶10.6更新细节</button></a>

                <a href="http://101.qq.com/tft/" target="_blank"><button type="button" class="btn btn-primary btn-lg">官方攻略中心</button></a>
                <?php
                if (SEN::isMe()) {
                    // echo '<a href="/yunding/tools.php"><button type="button" class="btn btn-warning btn-lg">管理</button></a>';
                }
                ?>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-md-4 column" style="height:410px;">
                <div class="synergies-box">
                    <div class="type">
                        <span class="group_span" style="float: left;">
                            <img src="https://game.gtimg.cn/images/lol/act/img/tft/origins/3101.png" class="group_span_img">
                        </span>
                        <p>星之守护者</p>
                        <p>【星之守护】在施放技能时会为其他【星之守护】提供法力值。（在他们当中传播）
                        </p>
                    </div>
                    <div class="content">
                        <p><span>3</span><span>共提供40法力值</span></p>
                        <p><span>6</span><span>共提供60法力值</span></p>

                    </div>
                </div>
                <div class="clearfix pop pop1" id="pop1">
                </div>
            </div>

            <div class="col-md-4 column">
                <span class="large-title">特质</span>
                <div class="group-list">
                    <div v-for="(group,index) in groupArr" v-if="group.id < 800" v-on:click.left="clickGroup(group)" class="groupBtn btn-choose" :class="isGroupHover(group)" :title="group.name" :data-id="group.id">
                        <span class="group_span"><img class="group_span_img" :src="groupImg(group.id)" /></span>
                        <span class="group_name">{{group.name}}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 column">
                <span class="large-title">职业</span>
                <div class="group-list">
                    <div v-for="(group,index) in groupArr" v-if="group.id > 800" v-on:click.left="clickGroup(group)" class="groupBtn btn-choose" :class="isGroupHover(group)" :title="group.name" :data-id="group.id">
                        <span class="group_span"><img class="group_span_img" :src="groupImg(group.id)" /></span>
                        <span class="group_name">{{group.name}}</span>
                    </div>
                </div>
            </div>

        </div>
        <div class="row clearfix">
            <div class="col-md-3 column">
                <span class="large-title">已选阵容</span><span>(英雄池左键添加，再次点击取消)</span>
                <div class="lineTwo" style="min-height: 50px;">
                    <div class="hero-list">
                        <div :title="hero.name" v-for="hero in inHeroList" v-on:click="clickHero(hero)" :class="'hi_'+hero.img">
                            <!-- <a href="javascript:"></a> -->
                            <!--<img :src="heroImg(hero.img)" />-->
                        </div>
                    </div>
                </div>
                <span class="large-title">禁用英雄</span><span>(英雄池右键添加，再次点击取消)</span>
                <div class="lineTwo" style="min-height: 50px;">
                    <div class="hero-list">
                        <div :title="hero.name" v-for="hero in heroBanList" v-on:click="banHero(hero)" :class="'hi_'+hero.img">
                            <!-- <a href="javascript:"></a> -->
                            <!--<img :src="heroImg(hero.img)" />-->
                        </div>
                    </div>
                </div>
                <span class="large-title">已选装备</span><span>(将计算转职装备，再次点击取消)</span>
                <div class="lineTwo">
                    <div class="hero-list">
                        <div :title="weapon.title" v-for="(weapon,index) in weaponList" v-on:click="delWeapon(index)">
                            <img :src="weaponImg(weapon.imgId)" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9 column">
                <span class="large-title">英雄池</span>
                <p style="font-size:14px;">左键添加到'已选阵容'，右键添加到'禁用英雄'。再次点击可以取消选择或取消禁用。</p>
                <div style="min-height:270px">
                    <div class="hero-list" v-for="(heroList,index) in val2hero">
                        <div :title="hero.title" v-for="hero in heroList" v-if="checkGroupHero(hero)" v-on:click.left="clickHero(hero)" @contextmenu.prevent="banHero(hero)" :data-id="hero.id" class="heroBtn" :class="'hi_'+hero.img">
                        </div>
                    </div>
                </div>
                <span class="large-title">转职装备</span>
                <p style="font-size:14px;">装备可以重复选择，点击左侧'已选装备'可以取消选择。</p>
                <div class="hero-list">
                    <div :title="weapon.title" v-for="(weapon,index) in weaponArr" v-on:click.left="clickWeapon(weapon)">
                        <img :src="weaponImg(weapon.imgId)" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-lg" id="runBtn">计算</button>
                    <button type="button" class="btn btn-secondary btn-lg" v-on:click="clearBtn()">清空</button>
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="col-md-4 column">
                <span class="large-title">筛选价格</span>
                <div class="btn-group btn-group-toggle" data-toggle="buttons"></div>
                <button type="button" class="btn" v-bind:class="{'btn-primary':heroValue[1]}" v-on:click="valBtn(1)">1金</button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':heroValue[2]}" v-on:click="valBtn(2)">2金</button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':heroValue[3]}" v-on:click="valBtn(3)">3金</button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':heroValue[4]}" v-on:click="valBtn(4)">4金</button>
                <button type="button" class="btn" v-bind:class="{'btn-primary':heroValue[5]}" v-on:click="valBtn(5)">5金</button>

                <span class="large-title">筛选英雄个数</span>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-primary" v-on:click="forCountFun(1)">
                        <input type="radio" name="options" v-model="forCount" value="1"> 一个
                    </label>
                    <label class="btn btn-primary" v-on:click="forCountFun(2)">
                        <input type="radio" name="options" v-model="forCount" value="2"> 两个
                    </label>
                    <label class="btn btn-primary active" v-on:click="forCountFun(3)">
                        <input type="radio" name="options" v-model="forCount" value="3" checked> 三个
                    </label>
                </div>
                <span> {{ forCount }}个</span>
            </div>

            <div class="col-md-8 column">
                <?php if (false) { ?>
                    <span class="large-title" onclick="$('#armyDiv').toggle();" style="cursor: pointer;">官方推荐阵容(隐藏)</span>
                    <div style="display: block;" id="armyDiv">
                        <div v-for="(army,index) in chickenArmyPlus" class="armyPlus">
                            <span onclick="$('#'+$(this).data('id')).toggle();" :data-id="army.idStr">{{army.line_name}}</span>
                            <div style="display: none;" v-bind:id="army.idStr">
                                <!--
                            early_info 前期过渡
                            enemy_info 克制分析
                            equipment_info 装备分析
                            location_info 站位分析
                            -->
                                <!--<h6>版本：{{army.edition}}</h6>-->
                                <b>简介：</b>
                                <p>{{army.characteristic}}</p>
                                <b>前期：</b>
                                <p>{{army.early_point}}</p>
                                <div class="hero-list">
                                    <div v-for="heroItem in army.early_heroes">
                                        <img :src="heroImg(heroItem)" />
                                    </div>
                                </div>
                                <b>中期：</b>
                                <p>{{army.metaphase_point}}</p>
                                <div class="hero-list">
                                    <div v-for="heroItem in army.metaphase_heroes">
                                        <img :src="heroImg(heroItem)" />
                                    </div>
                                </div>
                                <b>后期阵容：</b>
                            </div>
                            <div class="hero-list">
                                <div v-for="heroItem in army.line_hero">
                                    <img :src="heroImg(heroItem)" />
                                </div>
                            </div>

                        </div>
                    </div>
                <?php } ?>
                <span class="large-title">最优阵容</span>
                <div v-for="army in chickenArmy" class="traits">
                    <!--英雄列表-->
                    <div class="hero-list result">
                        <div :title="heroItem.title" v-for="heroItem in army.hero" :class="'hi_'+heroItem.img">
                        </div>
                    </div>
                    <div class="hero-list result">
                        <button type="button" class="btn btn-secondary" disabled="disabled">分数:{{army.score}}</button>
                        <button type="button" class="btn btn-info" disabled="disabled">强度:{{army.op}}</button>
                        <button v-if="army.tips" type="button" class="btn btn-success" disabled="disabled">{{army.tips}}</button>
                    </div>
                    <div class="result-jiban result">
                        <div v-for="(item,index) in army.group" :class="item.classStr">
                            <img :src="groupImg(item.id)" />
                            <span>{{item.count}}{{item.name}}</span>
                        </div>
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
                <img src="{{d_img}}" class="group_span_img">
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
            <p><span>{{title}} {{displayName}}</span><span>{{races}},{{jobs}}</span><span>{{price}}金币</span></p>
        </div>
        {{if equip}}
        <div class="recommend">
            <p class="title">推荐装备</p>
            <div class="champions">
                {{each equip as value index}}
                <img src="//game.gtimg.cn/images/lol/act/img/tft/equip/{{value}}.png" alt="" />
                {{/each}}
            </div>
        </div>
        {{/if}}
        <div class="skill">
            <p class="title">技能</p>
            <div class="info">
                <img src="{{skillImage}}" alt="" />
                <p class="name"><span>{{skillName}}</span><span>{{skillType}}</span></p>
                <p class="description">{{skillIntroduce}}</p>
            </div>
        </div>
    </script>
    <!-- jQuery (Bootstrap 的 JavaScript 插件需要引入 jQuery) -->
    <script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
    <!-- 包括所有已编译的插件 -->
    <script src="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.bootcss.com/vue/2.6.10/vue.min.js"></script>
    <!--模板框架-->
    <script src="//ossweb-img.qq.com/images/js/ArtTemplate.js"></script>
    <!-- <script src="https://lol.qq.com/act/AutoCMS/publish/LOLAct/TFTlinelist/TFTlinelist.js"></script> -->
    <!--阵容推荐-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTLineup_V3/TFTLineup_V3.js"></script>-->
    <!--所有英雄列表-->
    <!--<script src="http://game.gtimg.cn/images/lol/act/img/js/heroList/hero_list.js"></script>-->
    <!--元素效果-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTrace/TFTrace.js"></script>-->
    <!--职业效果-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTjob/TFTjob.js"></script>-->
    <!--英雄-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTHeroesData_V3/TFTHeroesData_V3.js"></script>-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTHeroesData/TFTHeroesData.js"></script>-->

    <!--装备-->
    <!--<script src="//lol.qq.com/act/AutoCMS/publish/LOLAct/TFTequipment_V3/TFTequipment_V3.js"></script>-->
    <!--配置-->
    <script src="<?= SEN::static_url('define') ?>"></script>
    <!--vue-->
    <script src="<?= SEN::static_url('frame') ?>"></script>
    <script>
        $.getJSON({
            url: '/yunding/default.json',
            success: function(ret) {
                displayPage(ret['data']);
            },
        });
    </script>
</body>
</html>