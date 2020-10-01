<?php
require_once 'sen.php';
SEN::init();

$aaa=['123','34vvvv','000999',555,0];

print_r(lib_tools::arrIntval($aaa));

exit;

//123
echo intval('123,456');

$obj = new stdClass();

$obj->a = '1';
$obj->b = 2;

function aa($obj){
    $obj->b=444;
}
print_r($obj);
aa($obj);
print_r($obj);