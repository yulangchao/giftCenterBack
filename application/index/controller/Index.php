<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = 'default';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Posts;
    }

    public function index()
    {
        return $this->view->fetch();
    }
    
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            dump($data = json_decode(file_get_contents('php://input'), true));
            $temp_images = array_map(function ($v) {
                return($v['response']['image_id']);
            }, $data['images']);
            $data['post_images'] = implode(",",$temp_images);
        }else{
            return $this->view->fetch();
        }
        
    }

    public function detail($id)
    {
        $detail = $this->model
        ->with(['cities'])
        ->find($id);
        $detail = $detail->toArray();
        $this->view->assign('detail', $detail);
        return $this->view->fetch();
    }

    public function getInfo()
    {

        $user = $this->auth->getUser();
        $keyword = $this->request->request('keyword');
        $city_id = $this->request->request('city_id');
        $condition = search($keyword);
        $where = [];
        $type = $this->request->request('type');
        if (isset($type)) {
            if ($type <= 1) {

            } else if ($type == 2) {
                // $records = db('record')->where(['user_id' => $user->id])->field('gift_id')->select();
                // $record_ids = array_map(function($r){
                //     return $r['gift_id'];
                // },$records);
                // $where['id'] = ['in',$record_ids];
                
            }
        }

        $page = $this->request->request('page') ? $this->request->request('page') : 1;
        $limit = $this->request->request('limit') ? $this->request->request('limit') : 10;
        $offset = ($page - 1) * $limit;
        
        $city_id && $where['city_id'] = ['in',$city_id];
        $posts = db('posts')->where($where)->whereRaw($condition['where'])->field('*,'.$condition['order'].'as weight')->order('weight desc, id desc')->limit($offset, $limit)->select();

        if ($posts){
            $posts[0]['keywords'] = isset($condition['keywords'])?$condition['keywords']: [];
        }
        foreach ($posts as $key => &$post) {
           $post['content'] = strip_tags($post['content']);
           $post['city_name'] = get_city_name($post['city_id']);
        }
        $data = [
            "code"=> 1,
            "data"=> $posts,
            "msg"=> "",
            "time"=> time()
        ];
        return json($data);
        
    }

    public function getCities($city_name="")
    {
        $where['city_name'] = ['like',"%".$city_name.'%'];
        
        $cities = db('cities')->where($where)->select();
        
        return json($cities);
        
    }


    public function search_result()
    {
        convert_city_id_to_name(1);
        return $this->view->fetch();
    }

    public function test($text = "温哥华知道吧crab")
    {
        $condition = search();
        dump($condition);
    }

}
