<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 抽奖接口
 */
class Gift extends Api
{

    protected $noNeedLogin = ['index', 'detail'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 拿取抽奖List
     *
     * @param string $keyword 关键词
     * @param string $offset offset
     * @param string $limit 限制多少
     */
    public function index()
    {
        $keyword = $this->request->request('keyword');
        $type = $this->request->request('type');
        if (isset($type)) {
            if ($type <= 1) {
                $where['if_open_switch'] = $type;
            } else if ($type == 2) {

            }
        }

        $page = $this->request->request('page') ? $this->request->request('page') : 1;
        $limit = $this->request->request('limit') ? $this->request->request('limit') : 10;
        $offset = ($page - 1) * $limit;

        $where['item_title|company_name|company_content'] = ['like', '%' . $keyword . '%'];

        $gifts = db('gift')->where($where)->limit($offset, $limit)->select();
        foreach ($gifts as $k => &$gift) {
            $gift['open_time'] = date("Y/m/d H:i", $gift['open_time']);
        }
        $this->success('', $gifts);
    }

    /**
     * 抽奖详情页
     *
     * @param string $id id
     */
    public function detail($id)
    {
        $gift = db('gift')->find($id);
        if ($gift){
            $gift['open_time'] = date("Y/m/d H:i", $gift['open_time']);
        }
        $this->success('', $gift);
    }
}
