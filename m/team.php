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