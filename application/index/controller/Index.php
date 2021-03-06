<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Posts;

class Index extends Frontend
{

    protected $noNeedLogin = ['index','detail','getInfo','getCities','search_result'];
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
        $user = $this->auth->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = json_decode(file_get_contents('php://input'), true);
            // dump($data);
            // $temp_images = array_map(function ($v) {
            //     return($v['response']['data']['url']);
            // }, $data['images']);
            $data['post_images'] = implode(",",$data['images']);
            $data['city_id'] = getCityIdFromCityName($data['city_name']);
            $data['user_id'] = $user->id;
            $result = new Posts();
            // 过滤post数组中的非数据表字段数据
            $result->allowField(true)->save($data);
            if($result){
                return json(['success'=>true,'result'=>$result]);
            }else{
                return json(['success'=>false]);
            }
        }else{
            return $this->view->fetch();
        }
        
    }
    public function delete($id)
    {
        $post = db('posts')->find($id);
        $user = $this->auth->getUser();
        if ($post['user_id'] != $user->id){
            $this->error(__('你没有权限删除这个信息。'));
        }
        if ($id) {
            $list = $this->model->where('id', $id)->delete();

            if($list){
                $this->success(__('删除成功'), url('/index/index/myposts'));
            }else{
                $this->error(__('删除失败'));
            }
        }
    }

    public function edit($id)
    {
        $post = db('posts')->find($id);
        $user = $this->auth->getUser();
        if ($post['user_id'] != $user->id){
            $this->error(__('你没有权限编辑这个信息。'));
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = json_decode(file_get_contents('php://input'), true);
            // $temp_images = array_map(function ($v) {
            //     return($v['response']['data']['url']);
            // }, $data['images']);
            $data['post_images'] = implode(",",$data['images']);
            $data['city_id'] = getCityIdFromCityName($data['city_name']);
            $data['id'] = $id;
            unset( $data['images']);
            unset( $data['city_name']);
            $result = $this->model->update($data);
            if($result){
                return json(['success'=>true,'result'=>$result]);
            }else{
                return json(['success'=>false]);
            }
            
        }else{
            
            $post['city_name'] = get_city_name($post['city_id']);
            // $post['post_images'] = explode(",",$post['post_images']);
            $this->assign('post',$post);
            return $this->view->fetch();
        }
        
    }

    public function detail($id)
    {
        db('posts')->where('id', $id)->setInc('views');
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
        if ($type == "me") {
                $where['user_id'] = $user->id;
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
           $post['post_images'] = Explode(',',$post['post_images']);
           $post['createtime'] = time_elapsed_string("@".$post['createtime']);
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
    
    public function myposts()
    {
        return $this->view->fetch();
    }

    public function test($text = "温哥华知道吧crab")
    {
        $condition = search();
        dump($condition);
    }

}
