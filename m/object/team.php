<?php
/**
 * 一个阵容
 */
class m_object_team{
    //=======数据对象 不做更改
    //阵容包括的英雄
    public $objChesses = [];
    // public $objEquips = [];
    public $objJobs = [];
    public $objRaces = [];

    /**
     * 羁绊总个数
     * @var int
     */
    public $groups = [];
    /**
     * 生效羁绊总个数
     * @var int
     */
    public $groupsWork = [];
    /**
     * 英雄个数
     * @var integer
     */
    public $chessCount = 0;
    /**
     * 阵容总价值
     * @var integer
     */
    public $allValue = 0;
    
    function __construct($input = [])
    {
        if(!empty($input)){
            $this->addTeam($input);
        }
    }
    /**
     * 添加一个新英雄
     * @return bool
     */
    public function addChess($chessId){
        if(array_key_exists($chessId, $this->objChesses)){
            return false;
        }
        $chessObj = m_data_Factory::get(lib_def::chess, $chessId);
        $this->objChesses[$chessId] = $chessObj;

        //棋子数量
        $this->chessCount += 1;
        //总价值
        $this->allValue += $chessObj->price;
        //遍历职业 更新职业数量
        foreach(array_merge($chessObj->jobIds,$chessObj->raceIds) as $jobId => $jobObj){
            if(array_key_exists($jobId, $this->groups)){
                $this->groups[$jobId]++;
                $workCount = $jobObj->workCount($this->groups[$jobId]);
                if(0 != $workCount){
                    if($workCount != $this->groupsWork[$jobId]){
                        //新羁绊
                        $this->allValue += $workCount;
                    }
                    $this->groupsWork[$jobId] = $workCount;
                }
            }
        }
        return true;
    }
    /**
     * 刷新阵容价值
     */
    // public function frashValue(){
    //     //英雄
    //     foreach($this->objChesses as &$objChess){
    //         $this->allValue += $objChess->price;
    //     }

    // }
    /**
     * 输入chessId列表
     * @param array $team
     * @return void
     */
    public function addTeam($team)
    {
        //遍历英雄
        foreach($team as $chessId){
            $this->addChess($chessId);
        }
        // //种族
        // foreach($this->objRaces as &$objRace){
        //     if(!$objRace->isWork){
        //         // if($objRace->featureCount === 0){
        //         //     unset($objRace);
        //         // }
        //     }else{
        //         $this->allValue += $objRace->value;
        //         $this->groupsCount += 1;
        //     }
        // }
        // //职业
        // foreach($this->objJobs as &$objJob){
        //     if(!$objJob->isWork){
        //         // if($objJob->featureCount === 0){
        //         //     unset($objJob);
        //         // }
        //     }else{
        //         $this->allValue += $objJob->value;
        //         $this->groupsCount += 1;
        //     }
        // }
    }

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