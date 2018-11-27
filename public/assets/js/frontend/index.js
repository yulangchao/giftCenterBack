define(['jquery', 'bootstrap', 'backend', 'addtabs', 'adminlte', 'form'], function ($, undefined, Backend, undefined, AdminLTE, Form) {
    Form.api.bindevent($("form"));
    var Controller = {
        index: function () {
            $('input[type="text"],textarea').on('click', function () {
                var target = this;
                setTimeout(function(){
                        target.scrollIntoView();
                        console.log('scrollIntoViewIfNeeded');
                    },400);
                });
        }
    };

    return Controller;
});