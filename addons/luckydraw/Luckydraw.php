<?php

namespace addons\luckydraw;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Luckydraw extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'luckydraw',
                'title'   => '抽奖管理',
                'icon'    => 'fa fa-empire',
                'sublist' => [
                    ['name' => 'luckydraw/index', 'title' => '查看'],
                    ['name' => 'luckydraw/del', 'title' => '删除'],
                    ['name' => 'luckydraw/multi', 'title' => '批量更新'],
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('luckydraw');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('luckydraw');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('luckydraw');
        return true;
    }

    /**
     * 实现钩子方法
     * @return mixed
     */
    public function testhook($param)
    {
        // 调用钩子时候的参数信息
        print_r($param);
        // 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
        print_r($this->getConfig());
        // 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
        //return $this->fetch('view/info');
    }

}
