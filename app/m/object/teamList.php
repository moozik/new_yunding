<?php

class app_m_object_teamList {
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
        1 => [],
        2 => [],
        3 => [],
        4 => [],
    ];

    public $score = 0;
    public $equip = [];

    public function __construct($chessArrObj) {
        $this->chessArrObj = $chessArrObj;
    }
    public function getArr() {
        return [
            'group' => $this->resultGroup,
            'chess' => array_map(function($chessObj){
                return $chessObj->chessId;
            },$this->chessArrObj),
            'equip' => array_map(function($equipObj){
                return $equipObj->equipId;
            },$this->equip),
            'score' => $this->score,
            'tips' => $this->tips,
        ];
    }
}