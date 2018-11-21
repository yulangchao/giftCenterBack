<?php
/**
 * Created by PhpStorm.
 * User: Gaoy
 * Date: 2017/12/22
 * Time: 16:13
 */
namespace app\common\behavior;
//跨域
use think\Response;

class Cors {

    public function appInit(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');

        if(request()->isOptions()){
            exit();
        }
    }

}