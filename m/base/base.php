<?php
/**
 * 英雄 阵营 武器 基类
 */
abstract class m_base_base
{
    /**
     * id
     * @var int
     */
    public $id;

    /**
     * 输入数据
     * @var array
     */
    protected $inParam = [];
    function __construct($obj)
    {
        foreach($this->inParam as $key => $value)
        {
            if(is_numeric($obj->{$value})){
                $this->{$key} = (int)$obj->{$value};
            }else{
                $this->{$key} = $obj->{$value};
            }
        }
    }
    /**
     * 获取当前数据
     * @return array
     */
    abstract public function getArray();
}