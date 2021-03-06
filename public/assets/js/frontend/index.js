define([
    "jquery",
    "bootstrap",
    "backend",
    "addtabs",
    "adminlte",
    "form"
], function($, undefined, Backend, undefined, AdminLTE, Form) {
    Form.api.bindevent($("form"));
    var Controller = {
        index: function() {
            $('input[type="text"],textarea').on("click", function() {
                var target = this;
                if (innerWidth < 768)
                setTimeout(function() {
                    target.scrollIntoView();
                    console.log("scrollIntoViewIfNeeded");
                }, 400);
            });
        },
        add: function() {

            $(".editor").change(function() {
                app.temp.content = $(".note-editable").html();
            });
        },
        edit: function() {

            $(".editor").change(function() {
                app.temp.content = $(".note-editable").html();
            });
        },
        detail: function() {
            var s = document.createElement("script");
            s.type = "text/javascript";
            s.src = "/assets/js/frontend/jquery.swipebox.js";
            // Use any selector
            $("head").append(s);
            (function($) {
                $(".swipebox").swipebox();
            })($);

        }
    };

    return Controller;
});
