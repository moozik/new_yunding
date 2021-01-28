<?php
declare(strict_types=1);
require_once 'sen.php';
SEN::init();
$routeInfo = SEN::getRoute();
if(file_exists($routeInfo[0])){
    if(class_exists($routeInfo[1])){
        try{
            //记录路由到的控制器
            define('ROUTE_CONTROLER', $routeInfo[1]);
            define('ROUTE_ACTION', $routeInfo[2]);
            $className = $routeInfo[1];
            lib_log::debug("ROUTE_CONTROLER", ROUTE_CONTROLER);
            lib_log::debug("ROUTE_ACTION", ROUTE_ACTION);
            $obj = new $className();
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
    echo 'file nofound.';
}
