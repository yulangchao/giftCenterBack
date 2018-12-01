define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'posts/index',
                    add_url: 'posts/add',
                    edit_url: 'posts/edit',
                    del_url: 'posts/del',
                    multi_url: 'posts/multi',
                    table: 'posts',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title')},
                        {field: 'post_images', title: __('Post_images'), formatter: Table.api.formatter.images},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'address', title: __('Address')},
                        {field: 'phone', title: __('Phone')},
                        {field: 'wechat', title: __('Wechat')},
                        {field: 'email', title: __('Email')},
                        {field: 'city_id', title: __('City_id')},
                        {field: 'views', title: __('Views')},
                        {field: 'if_active_switch', title: __('If_active_switch'), searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'lat', title: __('Lat'), operate:'BETWEEN'},
                        {field: 'lng', title: __('Lng'), operate:'BETWEEN'},
                        {field: 'cities.city_name', title: __('Cities.city_name')},
                        {field: 'user.username', title: __('User.username')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});