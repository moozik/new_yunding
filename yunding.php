<?php
require_once 'conf.php';
SEN::init();
class yunDing{
    //calc herolist中的key
    const K_ID = 0;
    const K_IDVAL = 1;
    const K_GROUP = 2;
    const K_GROUPVAL = 3;
    const K_GROUPTOP = 4;
    const K_TOTAL = 5;
    const K_GROUPMID = 6;
    const K_TRANS = 7;
    const K_TIPS = 8;
    const K_OP = 9;

    const SPLITE_STR = '+';
    const USE_CACHE = true;
    
    //返回阵容的条数
    const RETURN_COUNT = 10;
    
    //支持的最多人数 count($inHero)的最大值为
    const IN_HERO_MAX = 10;
    
    //支持的最多人数 $weapon的最大值为
    const IN_WEAPON_MAX = 10;

    /**
     * input
     */
    private $input;

    function __construct(){
        if(isset($_GET['debug']) && SEN::isMe()){
            $this->isDebug = true;
        }else{
            $this->isDebug = false;
        }
        HERO::init();
        SEN::Log($_GET['action'] .':'. $_GET['data'],  SEN::log_file(SEN::LOG_FILE));
        //英雄列表
        $this->hero = HERO::heroList();
        //羁绊列表
        $this->group = HERO::groupList();
        //权重调节
        // $this->groupValue = HERO::groupValue();
        //武器装备
        $this->weapon = HERO::weaponList();
        //$this->group2hero = HERO::group2hero();
        $this->ret = [
            'errno' => 0,
            'errmsg' => 'ok',
            'data' => [],
        ];
        try{
            $this->validateInput();
        }catch(Exception $e){
            $this->ret['errno'] = $e->getCode();
            $this->ret['errmsg'] = $e->getMessage();
            echo self::encode($this->ret);
            exit;
        }
        
        $ret = $this->execute();
        echo self::encode($ret);
    }
    /**
     * 验证入参
     */
    private function validateInput(){
        $inputData = json_decode($_GET['data'], true);
        $inputData['action'] = $_GET['action'];

        //param,default
        $paramDefault = [
            'action' => 'calc',
            'inHero' => [],
            'banHero' => [],
            'costList' => [1,2,3,4,5],
            'weapon' => [],
            'forCount' => 3
        ];
        foreach($paramDefault as $key => $default) {
            if (empty($inputData[$key])) {
                $inputData[$key] = $default;
            }
        }
        $heroKeys = array_keys($this->hero);
        $weaponKeys = array_keys($this->weapon);

        if(empty($inputData['action']) || (!in_array($inputData['action'], ['calc', 'niceTeam']))) {
            throw new Exception('action error', 500);
        }
        //inHero banHero
        if (self::IN_HERO_MAX < count($inputData['inHero'])) {
            throw new Exception('hero count:' . count($inputData['inHero']) . ' > IN_HERO_MAX.', 500);
        }

        foreach(array_merge($inputData['inHero'], $inputData['banHero']) as $heroId) {
            if(!in_array($heroId, $heroKeys)) {
                throw new Exception('heroId:' . $heroId . ' not exist.', 500);
            }
        }
        foreach($inputData['weapon'] as $weaponId) {
            if(!in_array($weaponId, $weaponKeys)) {
                throw new Exception('weaponId:' . $weaponId . ' not exist.', 500);
            }
        }
        foreach($inputData['costList'] as $cost) {
            if (!in_array($cost, $paramDefault['costList'])) {
                throw new Exception('costList error', 500);
            }
        }
        if (!in_array($inputData['forCount'], [0,1,2,3])) {
            throw new Exception('forCount:' . $inputData['forCount'] . ' error', 500);
        }
        //最多支持10个英雄 重置forCount数量
        if(count($inputData['inHero']) + $inputData['forCount'] >= self::IN_HERO_MAX){
            $inputData['forCount'] = self::IN_HERO_MAX - count($inputData['inHero']);
        }
        $this->input = $inputData;
    }
    /**
     * main function
     *
     * @param [array] $input $_GET
     * @return void
     */
    function execute(){
        $input = $this->input;
        //写推荐阵容
        if('niceTeam' === $input['action']){
            exit;
            if(empty($_POST['niceTeam']) || strlen($_POST['niceTeam']) > 2100){
                header('Location: ' . SEN::$siteUrl);exit;
            }
            $saveData = '';
            $ip = SEN::getIp();
            $ipLocation = new iplocation();
            $country = $ipLocation->getlocation($ip);
            $saveData .= date('Y-m-d H:i:s') . '|' . $ip . '|' . $country['country'] . '|' . $country['area'] . '|' .$_SERVER['HTTP_USER_AGENT']. '|' . $_POST['niceTeam'];
            $f = fopen(SEN::nice_file(), "a+");
            fwrite($f,$saveData."\n");
            fclose($f);
            echo '感谢投稿，添加成功';
            exit;
        }
        //主程序
        if('calc' === $input['action']){
            if(self::USE_CACHE){
                $cacheKey = SEN::cache_dir() . DIRECTORY_SEPARATOR . crc32(json_encode($input)) . '.json';
                //存在缓存文件则直接返回
                if(file_exists($cacheKey)){
                    echo file_get_contents($cacheKey);
                    exit;
                }
            }
            $teamList = $this->calc($input);
            foreach($teamList as &$group){
                // $group[self::K_IDVAL] = [];
                // $group[self::K_GROUPVAL] = [];
                unset($group[self::K_IDVAL]);
                // foreach($group[self::K_ID] as $id){
                //     $group[self::K_IDVAL][] = $this->hero[$id][HERO::name];
                // }
                unset($group[self::K_GROUPVAL]);
                // foreach($group[self::K_GROUP] as $groupid=>$num){
                //     $group[self::K_GROUPVAL][$this->group[$groupid][HERO::g_name]] = $num;
                // }

                //排序
                ksort($group[self::K_GROUP]);
                ksort($group[self::K_GROUPTOP]);
            }
            $this->ret['data'] = $teamList;
            if(self::USE_CACHE && 0 == $this->ret['errno']){
                file_put_contents($cacheKey, self::encode($this->ret));
            }
            return $this->ret;
        }
    }

    /**
     * 判断羁绊等级
     *
     * @param [array] $glevel
     * @param [int] $count
     * @return int
     */
    function checkLevel($glevel, $count){
        //法师2468特殊情况
        if(is_array($glevel[2])) {
            foreach($glevel[2] as $co) {
                if ($count >= $co){
                    return self::K_GROUPTOP;
                }
            }
        }else if(0 != $glevel[2] && $count >= $glevel[2]){
            return self::K_GROUPTOP;
        }

        if(0 != $glevel[1] && $count >= $glevel[1]){
            return self::K_GROUPMID;
        }
        if(0 != $glevel[0] && $count >= $glevel[0]){
            return self::K_GROUP;
        }
        return false;
    }

    /**
     * 计算主函数
     * $input['inHero'], $input['costList'], $input['banHero'],$input['forCount'], $input['weapon']
     * @param [array] $inHero 传入英雄
     * @param [array] $costList 英雄价格
     * @param [array] $banHero ban掉的英雄
     * @param [int] $forCount 推荐个数
     * @param [array] $weapon 转职装备
     * 
     * @return array
     */
    function calc($input){
        foreach($input as $key => $val) {
            ${$key} = $val;
        }
        $this->showDebug($inHero, '$inHero');
        //输出人数
        $retCount = count($inHero) + $forCount;

        //传入完整英雄列表
        $herolist = [];
        foreach($this->hero as $item){
            //忽略传入英雄
            if(in_array($item[HERO::id],$inHero))
                continue;
            //忽略费用过高英雄
            if(!in_array($item[HERO::cost], $costList))
                continue;
            //忽略ban掉的英雄
            if(in_array($item[HERO::id],$banHero))
                continue;
            
            $herolist[] = $item[HERO::id];
        }
        $this->showDebug($herolist, '$herolist');
        //循环forCount层 这段代码很乱，想办法做一下重构 todo
        $teamList = [];
        foreach($herolist as $id_1){
            //判断拉克丝和奇亚娜
            //if($this->elementCheck($id_1,0))continue;
            if($forCount == 1){
                $teamList[][self::K_ID] = array_merge($inHero,[$id_1]);
                continue;
            }
            foreach($herolist as $id_2){
                if($id_2 <= $id_1)continue;
                //判断拉克丝和奇亚娜
                //if($this->elementCheck($id_1,$id_2))continue;
                if($forCount == 2){
                    $teamList[][self::K_ID] = array_merge($inHero,[$id_1,$id_2]);
                    continue;
                }
                foreach($herolist as $id_3){
                    if($id_3 <= $id_2)continue;
                    //判断拉克丝和奇亚娜
                    //if($this->elementCheck($id_3,$id_2))continue;
                    $teamList[][self::K_ID] = array_merge($inHero,[$id_1,$id_2,$id_3]);
                }
            }
        }
        $this->showDebug($teamList, '$teamList');

        //转职装备预处理
        if(!empty($weapon)){
            //处理转职装备
            if(count($weapon) > self::IN_WEAPON_MAX){
                //超过最大值的装备被删除
                $weapon = array_slice($weapon, 0, self::IN_WEAPON_MAX);
            }
            //groupid2count映射
            $weaponGroup2Count = [];
            // $waponTransMax = [];
            foreach($weapon as $groupId){
                // $waponTransMax[$groupId] = 0;
                if(isset($weaponGroup2Count[$groupId])){
                    $weaponGroup2Count[$groupId]++;
                }else{
                    $weaponGroup2Count[$groupId] = 1;
                }
            }
            $this->showDebug($weaponGroup2Count, '$weaponGroup2Count');
        }

        //存储队伍价格到数量的映射，用于快速筛选有价值的阵容
        $valueLog = [];
        //筛选出羁绊价值最高的组合
        foreach($teamList as $k => &$v){
            //$this->showDebug($v, '$teamList>$v');
            //计算羁绊 棋子价值
            $v[self::K_IDVAL] = 0;
            //初始化羁绊 一级羁绊
            $v[self::K_GROUP] = [];
            //计算阵容价值
            $v[self::K_GROUPVAL] = 0;
            //中等质量羁绊 二级羁绊
            $v[self::K_GROUPMID] = [];
            //高等质量羁绊 顶级羁绊
            $v[self::K_GROUPTOP] = [];
            //装备
            $v[self::K_TRANS] = [];
            //提示
            $v[self::K_TIPS] = ['',''];
            //阵容强度
            $v[self::K_OP] = 0;
            
            
            foreach($v[self::K_ID] as $id){
                //棋子价值
                $v[self::K_IDVAL] += $this->hero[$id][HERO::cost];
                //阵营id
                $groupIdList = $this->hero[$id][HERO::group];
                //给当前英雄的所有羁绊计数
                foreach($groupIdList as $groupId){
                    if(isset($v[self::K_GROUP][$groupId])){
                        $v[self::K_GROUP][$groupId]++;
                    }else{
                        $v[self::K_GROUP][$groupId] = 1;
                    }
                }
            }
            //处理转职装备
            if(!empty($weapon)){
                //遍历转职装备 并计算能用到的最大装备数量到 $v[self::K_TRANS][$groupId]
                foreach($weaponGroup2Count as $groupId => $count){
                    if(isset($v[self::K_GROUP][$groupId])){
                        $v[self::K_TRANS][$groupId] = $retCount - $v[self::K_GROUP][$groupId];
                    }else{
                        $v[self::K_TRANS][$groupId] = $retCount;
                    }
                    if($v[self::K_TRANS][$groupId] < $count){
                        //装备过多，可用装备等于可装备人数
                        $count = $v[self::K_TRANS][$groupId];
                        $v[self::K_TIPS][0] = $this->group[$groupId][HERO::g_name] . ',';
                    }
                    //羁绊数量经过转职装备修正
                    if(isset($v[self::K_GROUP][$groupId])){
                        $v[self::K_GROUP][$groupId] += $count;
                    }else{
                        $v[self::K_GROUP][$groupId]=$count;
                    }
                }
                if(!empty($v[self::K_TIPS][0])){
                    $v[self::K_TIPS][0] = '剩余装备:' . trim($v[self::K_TIPS][0],',').';';
                }
            }
            
            $this->showDebug($v, '$teamList>$v');

            foreach($v[self::K_GROUP] as $groupId=>$count){
                
                //形成羁绊的有效英雄个数 类似3剧毒和4剧毒中，3个是有效的 $groupValue=3 $count=4
                $g_level = &$this->group[$groupId][HERO::g_level];
                $g_name = &$this->group[$groupId][HERO::g_name];
                $groupValue = $g_level[$count];
                //如果没有形成羁绊，那么删除当前阵营
                if(0 == $groupValue){
                    //$this->showDebug([$groupId, $count], 'delGroup');
                    unset($v[self::K_GROUP][$groupId]);
                    if(0 != $g_level[$count + 1]){
                        //如果数量+1buff不为0，则加入tips
                        $v[self::K_TIPS][1] .= '['.($count+1).$g_name.'],';
                    }
                    continue;
                }
                
                //使用$this->groupValue计算权重 额外人工配置的权重
                //$v[self::K_GROUPVAL] += $this->groupValue[$groupId][$groupValue];

                //顶级羁绊加入 K_GROUPTOP 删除原group中的数据
                //mid级羁绊加入 K_GROUPMID
                $KgroupLevel = $this->checkLevel($this->group[$groupId][HERO::g_glevel], $count);
                if(self::K_GROUP != $KgroupLevel){
                    $v[$KgroupLevel][$groupId] = $count;
                    unset($v[self::K_GROUP][$groupId]);
                    if(self::K_GROUPTOP == $KgroupLevel){
                        //top
                        if(1 == $count){
                            //星舰龙神 佣兵
                            $v[self::K_GROUPVAL] += 1;
                        }else{
                            //其他顶级羁绊
                            $v[self::K_GROUPVAL] += $groupValue * 4;
                        }
                    }else{
                        //中级羁绊
                        $v[self::K_GROUPVAL] += $groupValue * 2;
                    }
                }else{
                    //普通羁绊
                    //计算阵营价值，3换形师+3，4换形师+4 low buff原样加count
                    $v[self::K_GROUPVAL] += $groupValue;
                }
            }
            if(!empty($v[self::K_TIPS][1])){
                $v[self::K_TIPS][1] = '即将成型:' . trim($v[self::K_TIPS][1], ',') .';';
            }
            //当人数多于2，则遇到空buff直接跳出
            if($retCount > 2){
                //如果普通羁绊和顶级羁绊都为空 那么跳出 20191109修复bug：之判断了普通羁绊没判断顶级羁绊
                if(count($v[self::K_GROUP]) + count($v[self::K_GROUPTOP]) + count($v[self::K_GROUPMID]) == 0){
                    unset($teamList[$k]);
                    continue;
                }
            }
            //当人数多于4
            if($retCount > 4){
                //如果普通羁绊和顶级羁绊都为空 那么跳出 20191109修复bug：之判断了普通羁绊没判断顶级羁绊
                if(count($v[self::K_GROUP]) + count($v[self::K_GROUPTOP]) + count($v[self::K_GROUPMID]) < 2){
                    unset($teamList[$k]);
                    continue;
                }
            }

            //给阵营价值加上羁绊的个数，提高羁绊权重
            //$v[self::K_GROUPVAL] += count($v[self::K_GROUP]);
            
            //阵容价值=棋子价值+羁绊价值
            $v[self::K_TOTAL] = (string)round($v[self::K_IDVAL] + $v[self::K_GROUPVAL],2);
            
            if(isset($valueLog[$v[self::K_TOTAL]])){
                $valueLog[$v[self::K_TOTAL]]++;
            }else{
                $valueLog[$v[self::K_TOTAL]] = 1;
            }
            $v[self::K_TIPS] = implode('',$v[self::K_TIPS]);
            $v[self::K_OP] = (string)round($v[self::K_TOTAL] / $retCount,2);
            
            // echo self::encode($this->debug($v));
            // exit;
        }
        $this->showDebug($valueLog, '$valueLog', false);
        krsort($valueLog);
        //取头部阵容
        $teamCount = 0;
        $divLine = 0;
        foreach($valueLog as $val=>$num){
            // $this->showDebug([$val, $num], '$valueLog item', false);
            $teamCount += $num;
            //若总和大于预定数量则跳出
            if($teamCount >= self::RETURN_COUNT){
                $divLine = $val;
                break;
            }
        }
        //取待排序阵容
        $sortTeamList = [];
        foreach($teamList as $team){
            if($team[self::K_TOTAL] >= $divLine){
                $sortTeamList[] = $team;
            }
        }
        //排序
        $sortedTeamList = $this->array_sort($sortTeamList, self::K_TOTAL);
        return array_slice($sortedTeamList, 0, self::RETURN_COUNT);
    }

    /**
     * 排序函数
     *
     * @param [array] $arr
     * @param [mixed] $keys
     * @param string $orderby
     * @return array
     */
    function array_sort($arr, $keys, $orderby = 'desc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v){
            $keysvalue[$k] = $v[$keys];
        }
        if($orderby == 'asc'){
            asort($keysvalue);
        }else if($orderby == 'desc'){
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v){
            $new_array[] = $arr[$k];
        }
        return $new_array;
    }
    /**
     * 调试函数
     *
     * @param [array] $arr
     * @param string $text
     * @param boolean $flag
     * @return void
     */
    function showDebug($arr, $text='', $flag = true){
        if($this->isDebug == false)
            return;
        if(is_string($arr)){
            $ret = $arr;
        }else{
            $ret = $flag ? $this->debug($arr) : $arr;
        }
        //echo $text.' ['.serialize($ret)."]<br>\n";
        $de = debug_backtrace();
        echo '<span style="color:blue">'.$text.'</span> <span style="color:red">line:'.$de[0]['line'].'</span> ['.self::encode($ret)."]<br>\n";
    }
    /**
     * 递归转化英雄id
     *
     * @param [array] $arr
     * @return void
     */
    function debug($arr){
        $count = count($arr);
        $ret = [];
        $fun = function($v){
            if($v > 700){
                //group
                $v = HERO::G_MAP[$v];
            }else if($v >= 200){
                //hero
                $v = HERO::$h_map[$v][HERO::name];
            }
            return $v;
        };
        foreach($arr as $k=>$v){
            // if($count<100)$k = $fun($k);
            if(is_array($v)){
                $ret[$k] = $this->debug($v);
            }else if(is_numeric($v)){ 
                $ret[$k] = $fun($v);
            }else{
                $ret[$k] = $v;
            }
        }
        return $ret;
    }

    static function encode($arr){
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }
}
new YunDing;