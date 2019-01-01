layui.config({
    base: baselayui
}).use(['jquery', 'form', 'laypage', 'layer', 'lay_common', 'laytpl','flow'], function () {
    var $ = layui.jquery,
            form = layui.form,
            flow = layui.flow,
            laypage = layui.laypage,
            layer = layui.layer,
            laytpl = layui.laytpl,
            common = layui.lay_common;

    userPageList(1);

    /**查询*/
    $(".userSearchList_btn").click(function () {
        //监听提交
        form.on('submit(userSearchFilter)', function (data) {
            userPageList(1);
        });
    });

    /**打开模态窗口*/
    $("body").on("click", ".diag_modal", function () {
        var dataurl = $(this).attr("data-url");
        var datatitle = $(this).attr("data-title");
        common.cmsLayOpen(datatitle, dataurl, '850px', '500px');
    });

    /**删除*/
    $("body").on("click", ".diag_del", function () {
        var dataurl = $(this).data("url");
        var datatitle = $(this).data("title");
        var userStatus = $(this).data("status");
        var message = $(this).data("message");
        if (message.length == 0) {
            message = "确定删除数据吗";
        }
        var param = {userId: userStatus};
        common.ajaxFunConfirm(datatitle, message, dataurl, param, userPageList, curentpage);   
    });

    /**批量（删除）处理*/
    $("body").on("click", ".diag_deleteall", function () {
        var dataurl = $(this).attr("data-url");
        var datatitle = $(this).attr("data-title");
        var message = $(this).attr("data-message");
        if (message.length == 0) {
            message = "数据删除后将不将恢复，确定要删除吗？";
        }
        var ids = "";
        $("#dateTable tbody input[type='checkbox']:checked").each(function () {
            ids += this.value + ',';
        });
        if (ids == '') {
            common.cmsLayErrorMsg("没有选择数据");
            return;
        }
        var suffix=$("#suffix").val();
        var param = {idlist: ids,suffix:suffix};
        common.ajaxFunConfirm(datatitle, message, dataurl, param, userPageList, curentpage);
    });

    // 表格选中
    $('#dateTable tbody').on('click', 'tr input[type="checkbox"]', function () {
        var obj = $(this).parent().parent();
        if (this.checked) {
            obj.addClass('selected');
        } else {
            obj.removeClass('selected');
        }
    });

// 全选和反选
    $('#dateTable thead').on('click', 'tr input[type="checkbox"]', function () {
        var obj = $("#dateTable tbody input[type='checkbox']:checkbox");
        var allTr = $("#dateTable tbody tr");
        if (this.checked) {
            obj.prop("checked", true);
            allTr.addClass('selected');
        } else {
            obj.prop("checked", false);
            allTr.removeClass('selected');
        }
    });

    var tipindex;
    $(".tips").hover(function () {
        var content = $(this).attr("tips");
        if (content == '')
            return;
        var that = this;
        tipindex = layer.tips(content, that, {
            tips: [1, '#3595CC'],
        });
    }, function () {
        layer.close(tipindex);
    });

    form.on('select(province)', function (data) {
        $("#citylist").remove();
        $("#regionlist").remove();
        if (data.value.length == 0) {
            return false;
        }
        var val = data.value.replace(/0{4}$/g, '');
        $('#province').parent().after('<div class="layui-input-inline" id="citylist" style="width: 100px;"><input type="hidden" id="regioncode" name="regioncode" value=""/><select id="changecity" lay-filter="changecity"></select></div>');
        $("#regioncode").val(data.value);
        var postdata = {action: 'regionlist', regioncode: val};
        postdata[tokenname] = $("meta[name='" + tokenname + "']").attr('content');
        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: postdata,
            success: function (data, textStatus, request) {
                var token = request.getResponseHeader(tokenname);
                $("meta[name='" + tokenname + "']").attr('content', token);
                $("input[name='" + tokenname + "']").attr('value', token);
                if (data.status > 0) {
                    $(data.list).each(function (index, element) {
                        if (index === 0 && data.list.length > 1) {
                            $("#changecity").append("<option value='" + this.gbcode + "'>选择城市</option>");
                        } else {
                            if (data.list.length === 1) {
                                $("#changecity").append("<option value='" + val + "0100'>" + this.city + "</option>");
                            } else {
                                $("#changecity").append("<option value='" + this.gbcode + "'>" + this.city + "</option>");
                            }
                        }
                    });
                } else {
                    console.log(data.msg);
                }
                form.render('select'); //刷新select选择框渲染
            }
        });
    });

    form.on('select(changecity)', function (data) {
        $("#regionlist").remove();
        $("#regioncode").val(data.value);
        var val = data.value.replace(/0{2}$/g, '');
        var postdata = {action: 'regionlist', regioncode: val};
        postdata[tokenname] = $("meta[name='" + tokenname + "']").attr('content');
        $('#citylist').after('<div class="layui-input-inline" id="regionlist" style="width: 100px;"><select id="changeregion" lay-filter="changeregion"></select></div>');
        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: postdata,
            success: function (data, textStatus, request) {
                var token = request.getResponseHeader(tokenname);
                $("meta[name='" + tokenname + "']").attr('content', token);
                $("input[name='" + tokenname + "']").attr('value', token);
                if (data.status > 0) {
//                    console.log(data.list);
                    $(data.list).each(function (index, element) {
                        if (index === 0) {
                            $("#changeregion").append("<option value='" + this.gbcode + "'>选择区</option>");
                        } else {
                            $("#changeregion").append("<option value='" + this.gbcode + "'>" + this.region + "</option>");
                        }
                    });
                } else {
                    console.log(data.msg);
                }
                form.render('select'); //刷新select选择框渲染
            }
        });
    });

    form.on('select(changeregion)', function (data) {
        $("#regioncode").val(data.value);
    });
    
    //数据表切换
    form.on('select(suffix)', function (data) {
        userPageList(1);
    });

    $('.sortset').blur(function () {
        common.sortSeting($(this), manageid, "manage");
    });

    /*属性管理*/
    $("body").on("click", ".showby-tab", function () {
        $("#tab-handel", window.parent.document).attr("data-url", $(this).attr("data-url"));
        $("#tab-handel", window.parent.document).attr("data-id", $(this).attr("data-id"));
        $("#tab-handel", window.parent.document).attr("data-title", $(this).attr("data-title"));
        $('#tab-handel', window.parent.document).trigger("click");
    });


    var curentpage = 1;//当前页码
    /**加载数据**/
    function userPageList(curr) {
        var pageLoading = layer.load(2);
        var fmdata = $("#userSearchForm").serialize() + "&" + $.param({p: curr || 1});
        $.ajax({
            url: self_url,
            type: 'post',
            data: fmdata,
            success: function (data) {
                if (data != "") {
                    $("#view").html('');
                    var getTpl = $("#htmltemplates").html();
                    laytpl(getTpl).render(data, function (html) {
                        $("#view").html(html);
                    });

                    $('.layui-table-cell-over').on('click', function () {
                        if (this.offsetWidth < this.scrollWidth) {
                            var content = $(this).html();
                            layer.alert(content, {icon: 7});
                        }
                    });

                    form.render();

                    if (data.list.length == 0 && curentpage > 1) {
                        userPageList(curentpage - 1);
                        return;
                    }

                    //分页
                    laypage.render({
                        elem: 'userPage',
                        count: data.totalSize,
                        limit: pagesize,
                        curr: curr || 1, //当前页
                        groups: 8, //连续显示分页数
                        skip: true,
                        jump: function (obj, first) { //触发分页后的回调
                            if (!first) { //点击跳页触发函数自身，并传递当前页：obj.curr
                                curentpage = obj.curr;
                                userPageList(obj.curr);
                            }
                        }
                    });

                    //调用示例
                    layer.photos({
                        photos: '.imgview'
                        , anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                    });
                    
                    flow.lazyimg(); 
                }
                layer.close(pageLoading);
            }
        });
    }
});