<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\api\model\Record;
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
        $user = $this->auth->getUser();
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
            if ($user) {
                $gift['if_attend'] = db('record')->where(['user_id' => $user->id, 'gift_id' => $gift['id']])->find() ? true : false;
            } else {
                $gift['if_attend'] = false;
            }

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
        $user = $this->auth->getUser();
        if ($gift) {
            $gift['open_time'] = date("Y/m/d H:i", $gift['open_time']);

            $temp_top = db('record')->alias('r')->join('user u', 'r.user_id = u.id', 'LEFT')->where(['gift_id' => $gift['id']])->order('rate desc')->field('r.*,u.mobile')->limit(0, 10)->select();
            foreach ($temp_top as $k => &$top) {
                $top['mobile'] = substr_replace($top['mobile'], "***", 3, 3);
            }
            $gift['top10'] = $temp_top;
             
            if ($user) {
                $gift['if_attend'] = db('record')->where(['user_id' => $user->id, 'gift_id' => $gift['id']])->find() ? true : false;
            } else {
                $gift['if_attend'] = false;
            }
        }
        $this->success('', $gift);
    }

    /**
     * 抽奖POST
     * @ApiMethod   (POST)
     * @param string $id id
     */
    public function startGift($id, $u = "321")
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->auth->getUser();
            $where['user_id'] = $user->id;
            $where['gift_id'] = $id;
            $record = db('record')->where($where)->find();
            if ($record) {
                $this->success('已经参加，请勿重复参加！', null);
            }
            $refer_user = db('user')->where(['token'=>$u])->find();
            
            if ($refer_user && $refer_user['id'] != $user->id){
                $where['refer_id'] = $refer_user['id'];
                $refer_fitler['user_id'] = $refer_user['id'];
                $refer_fitler['gift_id'] = $id;
                $refer_record = db('record')->where($refer_fitler)->setInc('rate');
            }
            $result = new Record();
            // 过滤post数组中的非数据表字段数据
            $result->allowField(true)->save($where);
            $this->success('参加成功！', ['result'=>$result]);
        }

    }
}
