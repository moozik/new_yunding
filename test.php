<?php
require_once 'sen.php';
SEN::init();

$inArr = [
    [[1],[2],[3]],
    [['a'],['b'],['c']]
];

foreach(lib_tools::choseIteratorArr($inArr) as $item){
    print_r($item);
}

exit;

class test2{
    use lib_decorator;
    protected $isDecoratorEnable = true;

    public $result = 123;
    function beforeAction(&$method, &$params){
        echo '（bef）';
        lib_number::addCount('call_'.$method);
        lib_timer::start($method);
    }
    function afterAction(&$method, &$params, &$res){
        echo '（aft）';
        lib_timer::stop($method);
    }
    function name($a, &$b){
        echo memory_get_usage()."\n";
        $b++;
        return [$a, $b];
    }
}

$obj = new test2;
$a = 234;
$b = & $a;

$c = '123';

print_r($obj->de_name($c, $obj->result));
echo $obj->result;
exit;

$a = 123;
xdebug_debug_zval('a');
$b = $a;
xdebug_debug_zval('a');
unset($b);
xdebug_debug_zval('a');
exit;

class test{
    function __call($method, $params){
        echo $method;
        echo $params;
        echo __FUNCTION__;
    }
}

$obj = new test();
$obj->unfunc();


exit;

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