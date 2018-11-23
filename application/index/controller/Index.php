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
    }

    public function index()
    {
        return $this->view->fetch();
    }

    public function getInfo()
    {

        $user = $this->auth->getUser();
        $keyword = $this->request->request('keyword');
        $city_id = $this->request->request('city_id');
        $condition = search($keyword);
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


        $posts = db('posts')->where('city_id','in',$city_id)->whereRaw($condition['where'])->field('*,'.$condition['order'].'as weight')->order('weight desc, id desc')->limit($offset, $limit)->select();
        if ($posts){
            $posts[0]['keywords'] = $condition['keywords'];
        }
        $data = [
            "code"=> 1,
            "data"=> $posts,
            "msg"=> "",
            "keywords"=> $condition['keywords'],
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
