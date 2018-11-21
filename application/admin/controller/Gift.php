<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 *
 *
 * @icon fa fa-gift
 */
class Gift extends Backend
{

    /**
     * Gift模型对象
     * @var \app\admin\model\Gift
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Gift;

    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            foreach ($list as $k => &$gift) {
                $gift['open_time'] = $gift['open_time_text'];
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function openGift()
    {

        $current_time = time();
        $where['open_time'] = ['elt', $current_time - 54000];
        $where['if_open_switch'] = 0;
        $where['if_active_switch'] = 1;
        $gifts = db('gift')->where($where)->select();
        dump($gifts);
        foreach ($gifts as $k => $gift) {

            $records = db('record')->where(['gift_id' => $gift['id']])->select();
            dump($records);
            $pools = [];
            foreach ($records as $k => $record) {
                for ($i = 0; $i < $record['rate']; $i++) {
                    $pools[] = $record['user_id'];
                }

            }
            dump($pools);
            for ($j = 0; $j < $gift['item_number']; $j++) {
                if ($j == 0) {
                    $lucky_number[] = $pools[rand(0, sizeof($pools) - 1)];
                    dump("第" . $j);
                    dump($lucky_number);
                } else {
                    $temp_user_id = $pools[rand(0, sizeof($pools) - 1)];
                    while (in_array($temp_user_id, $lucky_number)) {
                        $temp_user_id = $pools[rand(0, sizeof($pools) - 1)];
                    }
                    dump("第" . $j);
                    $lucky_number[] = $temp_user_id;
                    dump($lucky_number);

                }

            }
            foreach ($lucky_number as $k => $user_id) {
                db('record')->where(['user_id' => $user_id, 'gift_id' => $gift['id']])->update(['if_get_switch' => 1]);
            }
            model('gift')->update(['id' => $gift['id'], 'if_open_switch' => 1]);

        }
    }

}
