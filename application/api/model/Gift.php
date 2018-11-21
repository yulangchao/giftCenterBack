<?php

namespace app\api\model;

use think\Model;

class Gift extends Model
{
    // 表名
    protected $name = 'gift';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'open_time_text'
    ];
    

    



    public function getOpenTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['open_time']) ? $data['open_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setOpenTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
