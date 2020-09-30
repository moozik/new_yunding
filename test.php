<?php
print_r($_SERVER);

//123
echo intval('123,456');

$obj = new stdClass();

$obj->a = '1';
$obj->b = 2;

if(is_int($obj->a)){
    echo 111;
}