<?php
/**
 * 一个阵容
 */
class m_team{
    public $objChesses = [];
    public $objEquips = [];
    public $objJobs = [];
    public $objRaces = [];

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
     * 输入chessId列表
     * @param array $team
     * @return void
     */
    public function newTeam($team)
    {
        $this->chessCount = count($team);
        //遍历英雄
        foreach($team as $chessId){
            $objChess = new m_chess($chessId);

            //遍历种族 创建模型
            foreach($objChess->raceIds as $raceId){
                if(!isset($this->objRaces[$racesId])){
                    $this->objRaces[$racesId] = new m_races($racesId);
                }
                $this->objRaces[$racesId]->addOne();
            }
            //遍历职业 创建模型
            foreach($objChess->jobIds as $jobId){
                if(!isset($this->objJobs[$jobId])){
                    $this->objJobs[$jobId] = new m_job($jobId);
                }
                $this->objJobs[$jobId]->addOne();
            }
            $this->objChesses[] = $objChess;
        }
        //英雄
        foreach($this->objChesses as &$objChess){
            $this->allValue += $objChess->price;
        }
        //种族
        foreach($this->objRaces as &$objRace){
            if(!$objRace->isWork){
                // if($objRace->featureCount === 0){
                //     unset($objRace);
                // }
            }else{
                $this->allValue += $race->value;
                $this->groupsCount += 1;
            }
        }
        //职业
        foreach($this->objJobs as &$objJob){
            if(!$objJob->isWork){
                // if($objJob->featureCount === 0){
                //     unset($objJob);
                // }
            }else{
                $this->allValue += $objJob->value;
                $this->groupsCount += 1;
            }
        }
    }

    /**
     * @param $chessId 英雄id >200
     * @return void
     */
    // public function addChess($chessId)
    // {
    //     $chess = new m_chess($chessId);
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