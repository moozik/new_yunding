<?php

class c_index extends lib_controlerBase{
    /**
     * 展示主页
     */
    public function actionIndex(){
        SEN::display_page('index');
    }
}