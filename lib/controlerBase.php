<?php
/**
 * 控制器基类
 */
abstract class lib_controlerBase{
    /**
     * 入参
     */
    public $arrInput = [];

    /**
     * 结果
     */
    public $result = [];

    public function __construct()
    {
        //初始化dao
        // m_dao_race::init();
        // m_dao_job::init();
        // m_dao_chess::init();
        // m_dao_equip::init();
        $this->init();
    }

    /**
     * 子类初始化
     */
    protected function init(){
        return;
    }

    /**
     * 验证
     * @return bool
     */
    protected function inputCheck(){
        return true;
    }

    /**
     * 子类主流程
     */
    abstract public function doWork();
    
    /**
     * 主流程
     */
    public function execute(){
        $this->arrInput = empty($_POST) ? $_GET: $_POST;
        $this->result = [
            'msg' => 'ok',
            'data' => [],
        ];
        try{
            //验证入参
            $this->inputCheck();
            //dowork
            $this->doWork();
            //display
            $this->display();
        }catch(Exception $e){
            $this->result['msg'] = $e->getMessage();
            SEN::fatalLog('', $e->getMessage());
        }
    }

    /**
     * 展示
     */
    protected function display(){
        echo lib_tools::encode($this->result);
    }
}