layui.config({
    base: baselayui
}).use(['jquery','lay_common'], function () {
    var $ = layui.jquery,
            common = layui.lay_common;
    /*提交排序*/
    $('.sortset').blur(function () {
        common.sortSeting($(this),manageid,"manage");
    });

    /*联动选择子项 (选中父项，自动全选子项，选子项时不关联父项) 删除选择 */
    $('.linkxpath').change(function () {
        var ck = this.checked;
        var xpath = $(this).attr('xpath');
        if (ck) {
            $(":checkbox[xpath^='" + xpath + "']").prop('checked', true);
            $("option[path^='" + xpath + "']").attr('disabled', true);

        } else {
            var arr = xpath.split('|');
            $.each(arr, function (index, value) {
                if (value !== '') {
                    $(":checkbox[value=" + value + "]").prop('checked', false);
                    $("option[value=" + value + "]").removeAttr('disabled');
                }
            });
            $(":checkbox[xpath^='" + xpath + "']").prop('checked', false);
        }
    });

    /*联动选择子项 (选择子项时，自动选中父项) 栏目分配 */
    $('.linkxpathture').change(function () {
        var ck = this.checked;
        var xpath = $(this).attr('xpath');
        if (ck) {
            var arr = xpath.split('|');
            $.each(arr, function (index, value) {
                if (value !== '') {
                    $(":checkbox[value=" + value + "]").prop('checked', true);
                }
            });
            $(":checkbox[xpath^='" + xpath + "']").prop('checked', true);

        } else {
            $(":checkbox[xpath^='" + xpath + "']").prop('checked', false);
        }
    });

    /*全选和反选*/
    $('.selectall').click(function () {
        var ck = this.checked;
        if (ck) {
            $('.tid').prop('checked', true);
        } else {
            $('.tid').prop('checked', false);
        }
    });
});
    