<?php

class m_data_teamList {
    //棋子
    public $chessArrObj = [];
    //计算羁绊棋子价值
    public $idVal = 0;
    //初始化羁绊一级羁绊
    public $group = [];
    //提示
    public $tips = ['', ''];


    /**
     * @var array[]
     */
    public $resultGroup = [
        0 => [],
        1 => [],
        2 => [],
        3 => [],
    ];

    public $score = 0;
//    public $op = 0;
    public $weapon = [];

    public function __construct($chessArrObj) {
        $this->chessArrObj = $chessArrObj;
    }
    public function getArr() {
        return [
            'group' => $this->resultGroup,
            'chess' => array_map(function($chessObj){
                return $chessObj->chessId;
            },$this->chessArrObj),
            'score' => $this->score,
//            'op' => $this->op,
            'tips' => $this->tips,
        ];
    }
}