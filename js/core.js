/*
 The MIT License (MIT)
 
 Copyright (c) 2014 Attila Molnár
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.
 */

$(document).ready(function() {
    $('.password_generator').on('click', function() {
        var element = $(this);
        var cb = function(res) {
            var fields = element.attr('rel').split(',');
            var form = element.closest('form');
            form.find('.generated_password').html(res.pw);
            form.find('input[name=password]').val(res.pw);
            $.each(fields, function(i, e) {
                $(form).find('input[name=' + e + ']').val(res.pw);
            });
        };
        ajaxRequest('generate-password', {}, cb);
    });

    $(".datepicker").datepicker({
        "dateFormat": "yy-mm-dd"
    });

    //  MULTI-SELECT LISTENERS
    $(document).on("click", ".input-group .move_left_all", function() {
        moveLeft(this, true);
    });
    $(document).on("click", ".input-group .move_left_sel", function() {
        moveLeft(this, false);
    });
    $(document).on("dblclick", ".input-group .possibles", function() {
        moveLeft(this, false);
    });
    $(document).on("click", ".input-group .move_right_all", function() {
        moveRight(this, true);
    });
    $(document).on("click", ".input-group .move_right_sel", function() {
        moveRight(this, false);
    });
    $(document).on("dblclick", ".input-group .target", function() {
        moveRight(this, false);
    });
});

function moveLeft(t, all) {
    $(".target", $(t).closest(".input-group")).append($(".possibles option" + (all ? "" : ":selected"), $(t).closest(".input-group")));

    var usedValues = {};
    $(".target option", $(t).closest(".input-group")).each(function() {
        if (usedValues[this.text]) {
            $(this).remove();
        } else {
            usedValues[this.text] = this.value;
        }
    });

    var my_options = $(".target option", $(t).closest(".input-group")).attr("selected", false);

    my_options.sort(function(a, b) {
        if (a.text > b.text) {
            return 1;
        } else if (a.text < b.text) {
            return -1;
        } else {
            return 0;
        }
    });

    $(".target", $(t).closest(".input-group")).empty().append(my_options);
}

function moveRight(t, all) {
    $(".possibles", $(t).closest(".input-group")).append($(".target option" + (all ? "" : ":selected"), $(t).closest(".input-group")));

    var usedValues = {};
    $(".possibles option", $(t).closest(".input-group")).each(function() {
        if (usedValues[this.text]) {
            $(this).remove();
        } else {
            usedValues[this.text] = this.value;
        }
    });

    var my_options = $(".possibles option", $(t).closest(".input-group")).attr("selected", false);

    my_options.sort(function(a, b) {
        if (a.text > b.text) {
            return 1;
        } else if (a.text < b.text) {
            return -1;
        } else {
            return 0;
        }
    });

    $(".possibles", $(t).closest(".input-group")).empty().append(my_options);
}


function ajaxRequest(action, data, callback, errorcallback) {
    if (!data) {
        data = {};
    }
    if ((typeof data).toLowerCase() === 'string') {
        data += '&action=' + action;
    } else {
        data.action = action;
    }

    $.ajax({
        type: "POST",
        url: "ajax/actions.ajax.php",
        data: data,
        async: false,
        nocache: true,
        dataType: 'json',
        success: function(result) {
            if (result.status) {
                if (callback) {
                    callback(result);
                } else {
                    alert(result.message);
                }
            } else {
                if (errorcallback) {
                    errorcallback(result);
                } else {
                    alert(result.message);
                }
            }
        },
        unsuccess: function(res) {
            console.log(res);
        }
    });
}