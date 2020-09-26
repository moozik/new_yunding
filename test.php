<?php

$obj = new stdClass();

$obj->a = '1';
$obj->b = 2;

if(is_int($obj->a)){
    echo 111;
}