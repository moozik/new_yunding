<?php

/**
 * 控制器基类
 */
abstract class frame_controlerBase {
    /**
     * 入参
     */
    public $arrInput = [];

    /**
     * 结果
     */
    public $result = [];

    public function __construct() {
    }

    /**
     * 验证
     * @return void
     */
    protected function inputCheck() {
    }

    /**
     * 异常处理
     *
     * @return void
     */
    protected function exceptionWork(Exception $e) {
        $this->result['msg'] = $e->getMessage();
        echo lib_string::encode($this->result);
        return;
    }
    /**
     * 子类主流程
     */
    // abstract public function doWork();

    /**
     * 主流程
     * @throws frame_fatalException
     */
    public function execute() {
        $this->arrInput = empty($_POST) ? $_GET : $_POST;
        $this->result = [
            'msg' => 'ok',
            'data' => [],
        ];
        try {
            //验证入参
            $this->inputCheck();
            //dowork
            if (!is_callable([$this, ROUTE_ACTION])) {
                throw new Exception("action no found.");
            }
            $this->{ROUTE_ACTION}();
        } catch (frame_fatalException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->exceptionWork($e);
            lib_log::fatal('frame_controlerBase', $e->getMessage());
        }
    }
}