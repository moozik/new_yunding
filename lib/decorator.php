<?php
/**
 * 装饰器插件类
 */
trait lib_decorator{
    /**
     * 装饰器方法名前缀
     */
    protected $decoratorTag = 'de_';
    //装饰器入口
    public function __call($method, $params){
        lib_log::debug('__call', sprintf("method:%s,params:%s",$method,lib_string::encode($params)));
        if($this->decoratorTag !== substr($method, 0, 3)){
            throw new lib_fatalException(sprintf("function name no found:%s", $method));
        }
        if(false === $this->isDecoratorEnable){
            return call_user_func_array([$this, substr($method, 3)], $params);
        }
        $method = substr($method, 3);
        $this->beforeAction($method, $params);
        $res = call_user_func_array([$this, $method], $params);
        $this->afterAction($method, $params, $res);
        return $res;
    }
    /**
     * 装饰前方法
     */
    abstract protected function beforeAction($method, $params);
    /**
     * 装饰后方法
     */
    abstract protected function afterAction($method, $params, $res);
}