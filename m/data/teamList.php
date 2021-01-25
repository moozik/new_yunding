<?php
class m_data_teamList{
    //棋子
    public $chessArrObj = [];
    //计算羁绊棋子价值
    public $idVal = 0;
    //初始化羁绊一级羁绊
    public $group = [];
    //计算阵容价值
    // public $groupVal = 0;
    //中等质量羁绊二级羁绊
    // public $groupmid = [];
    //高等质量羁绊顶级羁绊
    // public $grouptop = [];
    //装备
    public $weapon = [];
    //提示
    public $tips = ['',''];
    //阵容强度
    public $op = 0;

    //结果
    public $result = null;

    public function __construct($chessArrObj){
        $this->chessArrObj = $chessArrObj;

        $this->result = new stdClass();
        $this->result->group = [
            0 => [],
            1 => [],
            2 => [],
            3 => [],
        ];
        // $this->result->chess = [];
        $this->result->score = 0;
        $this->result->op = 0;
        $this->result->tips = '';
    }

    public function getArr(){
        return [
            'group' => $this->result->group,
            'chess' => array_map(function($chessObj){
                return $chessObj->chessId;
            },$this->chessArrObj),
            'score' => $this->result->score,
            'op' => $this->result->op,
            'tips' => $this->result->tips,
        ];
    }
}