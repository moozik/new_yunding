<?php
require_once 'sen.php';
// print_r($_SERVER);exit;
SEN::init();
$route = [
    'index',
    'calc',
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
            $obj = new $controlName();
            $obj->execute();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }else{
        echo 'controler nofound.';
    }
}else{
    echo 'route nofound.';
}