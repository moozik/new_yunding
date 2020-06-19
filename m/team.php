<?php
/**
 * 一个阵容
 */
class m_team{
    public $chess = [];
    public $equips = [];
    public $jobs = [];
    public $races = [];

    /**
     * 羁绊总个数
     *
     * @var integer
     */
    public $groupsCount = 0;
    /**
     * 英雄个数
     *
     * @var integer
     */
    public $chessCount = 0;
    /**
     * 总价值
     *
     * @var integer
     */
    public $allValue = 0;
    function __construct($input = [])
    {
        if(!empty($input)){
            $this->newTeam($input);
        }
    }
    /**
     * 输入heroid列表
     * @param array $team
     * @return void
     */
    public function newTeam($team)
    {
        $this->chessCount = count($team);
        //遍历英雄
        foreach($team as $heroId){
            $chess = new m_chess($heroId);
    
            if(!isset($this->races[$chess->raceIds])){
                $this->races[$chess->raceIds] = new m_race($chess->raceIds);
            }
            $this->races[$chess->raceIds]->addOne();
    
            foreach($chess->jobIds as $jobId){
                if(!isset($this->jobs[$jobId])){
                    $this->jobs[$jobId] = new m_job($jobId);
                }
                $this->jobs[$jobId]->addOne();
            }
            $this->chess[] = $chess;
        }
        //英雄
        foreach($this->chess as &$chess){
            $this->allValue += $chess->price;
        }
        //种族
        foreach($this->races as &$race){
            if(!$race->isWork){
                // if($race->featureCount === 0){
                //     unset($race);
                // }
            }else{
                $this->allValue += $race->value;
                $this->groupsCount += 1;
            }
        }
        //职业
        foreach($this->jobs as &$job){
            if(!$job->isWork){
                // if($job->featureCount === 0){
                //     unset($job);
                // }
            }else{
                $this->allValue += $job->value;
                $this->groupsCount += 1;
            }
        }
    }
    /**
     * 英雄组合生成器
     * @param array $inArr
     * @param int $count
     * @return yield
     */
    static function heroCombine($inArr, $count){
        $inArrLen = count($inArr);
        if($inArrLen < $count){
            throw new Exception('inArr count error.');
        }
        if($inArrLen === $count){
            yield $inArr;
            return;
        }
        //二进制组合
        $position = array_fill(0, $inArrLen, 0);
        //前count个填充1
        for($i = 0; $i < $count; $i++){
            $position[$i] = 1;
        }
        //返回第一个组合
        yield self::genReturn($inArr, $position);
        while(self::nextCombine($position, $inArrLen)){
            yield self::genReturn($inArr, $position);
        }
    }
    static private function nextCombine(&$position, &$inArrLen){
        $count_1 = 0;
        //搜索 1 0
        for($o = 0; $o < $inArrLen - 1; $o++){
            //1
            if(1 === $position[$o]){
                //0
                if(0 === $position[$o + 1]){
                    break;
                }
                $count_1++;
                //置0
                $position[$o] = 0;
            }
        }
        //截止点
        if($o === $inArrLen - 1 || 1 != $position[$o] || 0 != $position[$o + 1]){
            return false;
        }
        //交换10
        $position[$o] = 0;
        $position[$o + 1] = 1;
        //1移到左边
        for($o = 0; $o < $count_1; $o++){
            $position[$o] = 1;
        }
        return true;
    }
    static function genReturn(&$inArr, &$position){
        static $ret = [];
        if($ret != []){
            $ret = [];
        }
        foreach($position as $key => &$flag){
            if($flag){
                $ret[] = $inArr[$key];
            }
        }
        return $ret;
    }

    /**
     * @param $heroId 英雄id >200
     * @return void
     */
    // public function addChess($heroId)
    // {
    //     $chess = new m_chess($heroId);
    //     $this->chessCount++;

    //     if(!isset($this->races[$chess->raceIds])){
    //         $this->races[$chess->raceIds] = new m_race($chess->raceIds);
    //     }
    //     $this->races[$chess->raceIds]->addOne();

    //     foreach($chess->jobIds as $jobId){
    //         if(!isset($this->jobs[$jobId])){
    //             $this->jobs[$jobId] = new m_job($jobId);
    //         }
    //         $this->jobs[$jobId]->addOne();
    //     }
    //     $this->chess[] = $chess;
    // }

    /**
     * 计算阵容价值
     * @return void
     */
    // public function value(){
    //     foreach($this->chess as &$chess){
    //         $this->allValue += $chess->price;
    //     }
    //     foreach($this->races as &$race){
    //         $this->allValue += $race->price;
    //     }
    //     foreach($this->jobs as &$job){
    //         $this->allValue += $job->price;
    //     }
    // }
}