<?php

namespace app\api\model;

use think\Model;

class Record extends Model
{
    // 表名
    protected $name = 'record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [

    ];
    

    







    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function gift()
    {
        return $this->belongsTo('Gift', 'gift_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
