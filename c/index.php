<?php

class c_index extends lib_controlerBase{
    /**
     * 展示主页
     */
    public function actionIndex(){
        //判断definejs是否存在
        if(!file_exists(SEN::static_path('define'))){
            $obj = new c_tools();
            $obj->update();
        }
        SEN::display_page('index');
    }
}