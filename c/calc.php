<?php

class c_calc{
    private $usedHero = [];
    public function __construct()
    {
        //初始化dao
        m_dao_race::init();
        m_dao_job::init();
        m_dao_chess::init();
        m_dao_equip::init();
    }
    /**
     * 主函数
     */
    public function execute(){

        $this->inputCheck();

        //获取可用英雄
        $heroList = $this->canUseHero();
        //可用数量
        $heroCount = count($heroList);

        // echo self::m_chose_n(50, 4);exit;
        /**
         * 2（55，2）
         * 找2个，最多个数为1485
         * 3（54，3）
         * 找3个，最多个数为24804
         * 4（53，4）
         * 找4个，最多个数为292825
         */

        $resultCount = self::m_chose_n($heroCount, $this->input->forCount);
        if($this->input->forCount > 3 && $resultCount > 30000){
            //如果符合条件，那么拆分再遍历
            $rangeData = [
                4 => [3, 1],
                5 => [3, 2],
                6 => [3, 3],
                7 => [3, 3, 1],
                8 => [3, 3, 2],
                9 => [3, 3, 3],
            ][$this->input->forCount];
        }else{
            $rangeData = [
                $this->input->forCount
            ];
        }
        $count = array_fill(0,count($rangeData),0);
        //保存已使用的棋子
        $usedHero = [];
        foreach($rangeData as $key => $loopCount){
            foreach(m_team::heroCombine($this->canUseHero($usedHero), $loopCount) as $item){
                $count[$key]++;
            }
        }
        print_r($count);
    }
    /**
     * m选n结果集个数
     * @param int $m
     * @param int $n
     * @return int
     */
    static function m_chose_n($m, $n){
        return (self::fn($m) / (self::fn($n) * self::fn($m - $n)));
    }
    /**
     * 阶乘
     * @param int $n
     * @return void
     */
    static function fn($n){
        if($n == 0) return 1;
        $fn = 1;
        for($i = 1; $i <= $n; $i++){
            $fn *= $i;
        }
        return $fn;
    }
    /**
     * 获取可用英雄
     * @param array $usedHero
     * @return array
     */
    private function canUseHero($usedHero = []){
        $ret = [];
        foreach(m_dao_chess::$data as $chess){
            //inHero banHero
            if(in_array($chess->chessId, $this->input->inHero)
                || in_array($chess->chessId, $this->input->banHero)){
                continue;
            }
            //costList
            if(!in_array($chess->price, $this->input->costList)){
                continue;
            }
            //usedHero
            if(in_array($chess->chessId, $usedHero)){
                continue;
            }
            $ret[] = $chess->chessId;
        }
        return $ret;
    }
    /**
     * 入参校验
     * @return void
     */
    private function inputCheck(){
        //{"inHero":[215,213,201],"costList":[1,2,3,4],"banHero":[],"forCount":3,"weapon":[]}
        if(empty($_GET['data'])){
            throw new Exception('param data empty.');
        }
        $this->input = json_decode($_GET['data']);
    }

}