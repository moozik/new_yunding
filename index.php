<?php
declare(strict_types=1);

require_once 'sen.php';
// print_r($_SERVER);exit;
SEN::init();
$route = [
    'index',
    'teamCalc',
    'tools',
];
// $action = $_SERVER['QUERY_STRING'];
$action = $_SERVER['REDIRECT_PATH_INFO'];
if(empty($action)){
    $action = 'index';
}
if(in_array($action, $route)){
    $controlName = 'c_' . $action;
    if(class_exists($controlName)){
        try{
            //记录路由到的控制器
            define('ROUTE_CONTROLER', $action);
            $obj = new $controlName();
            $obj->execute();
        }catch(lib_fatalException $e){
            //致命异常
            $fatalStr = sprintf("exception occured,errno:[%s], msg:[%s] \n#  %s(%s) \n%s",
            $e->getCode(), $e->getMessage(), $e->getFile(),$e->getLine(),$e->getTraceAsString());
            lib_log::fatal('index', $fatalStr);
        }catch(Exception $e){
            lib_log::trace('index', $e->getMessage());
        }
    }else{
        echo 'controler nofound.';
    }
}else{
    echo 'route nofound.';
}