<?php
require_once 'sen.php';
SEN::init();

echo ROOT_DIR;

function te(){
    lib_log::debug('bb','123');
}

te();

exit;

echo lib_array::sumBykey([
    ['a'=>3],
    ['a'=>3],
    ['a'=>3],
    ['a'=>3],
    ['b'=>3],
],'b');

exit;
$Gcombination = [1,2,3];
foreach(lib_tools::choseIterator($Gcombination, 2) as $GcomItem){
    print_r($GcomItem);
}

exit;

m_dao_race::init();
m_dao_job::init();
m_dao_chess::init();
m_dao_equip::init();

$chess = m_data_Factory::get(lib_def::chess, 39);
var_dump($chess);
exit;

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