<?php

class c_index{
    public function __construct()
    {
        // m_dao_race::init();
        // m_dao_job::init();
        // m_dao_chess::init();
        // m_dao_equip::init();
    }
    public function execute(){
        // echo 'hello world.';
        // $team = new m_team();
        // $team->addChess(208);
        // $team->addChess(234);
        // $team->addChess(242);
        // var_dump($team);
        include_once SEN::view_path('index');
    }
}