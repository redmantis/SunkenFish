layui.config({
    base: baselayui
}).use(['form', 'lay_common', 'layedit', 'table', 'laydate', 'element', 'upload', 'jquery'], function () {
    var $ = layui.jquery
            , form = layui.form
//            , element = layui.element
            , layer = layui.layer
            , upload = layui.upload
            , common = layui.lay_common
            , table = layui.table
//            , layedit = layui.layedit
            , laydate = layui.laydate;

    var tokenname = '__rdmshkjhash__';

    layer.ready(function () {
        var t = $("#page-name").text();
        var obj = $(".userInterfaceCon-nav").find("[data-title='" + t + "']").attr("data-parent");
        if (typeof (obj) !== "undefined") {
            var html = "<span> &gt;</span>" + obj;
            $("#page-home").append(html);
        }
    });

    var loadindex;

    /**
     * 显示tips
     */
    $(".tips").hover(function () {
        var content = $(this).attr("tips");
        if (content == '')
            return;
        var that = this;
        layer.tips(content, that, {
            tips: 1,
            shift: 5,
            time: 0,
            skin: 'grey-white'
        });
    },
            function () {
                layer.closeAll();
            }
    );



    /**
     * 监听密码找回事件
     */
    form.on("submit(forget)", function (data) {
        //return false;
    });

    /**监听提交*/
    form.on("submit(submit1)", function (data) {
         common.ajaxSubmit(data,api_url,tokenname);
    });

    /**
     * 开启编辑模式
     */
    $(".change_edit").on("click", function () {
        var handle = $(this).attr("data-handle");
        if ($(this).text() == '编辑') {
            $("#" + handle + "-show").hide();
            $("#" + handle + "-panlel").show();
            $(this).text("取消");
        } else {
            $("#" + handle + "-show").show();
            $("#" + handle + "-panlel").hide();
            $(this).text("编辑");
        }
    });

    /**
     * 页面编辑脚本
     */
    $(".change_prompt").on("click", function () {
        var handle = $(this).attr("data-handle");
        var title = $(this).attr('data-title');
        var formhandle = $(this).attr("data-form");
        var content = " <form method=\"post\" class=\"layui-form\" >" + $("#" + handle + "-panlel").html() + "<button type=\"submit\" id=\"prompt-form\" lay-submit=\"\" lay-filter=\"submit1\" style=\"display: none\">提交</button></form>";
        layer.open({
            title: title,
            type: 1,
            area: '500px',
            skin: 'layui-layer-demo', //样式类名
            closeBtn: 0, //不显示关闭按钮
            anim: 0,
            offset: '150px',
            shadeClose: true, //开启遮罩关闭
            btn: ['确定', '取消'],
            btnAlign: 'c',
            content: content
            , success: function (layero, index) {
                $(".sendemail").on('click', function () {
                    sendemail(this);
                });

                $(".sendsmsl").on('click', function () {
                    sendsms(this);
                });

                form.on("submit(submit1)", function (data) {
                    var data = $("#" + formhandle).serializeArray();
                    $(layero).find(("input")).each(function () {
                        var name = $(this).attr('name');
                        var value = $(this).val();
                        //$("#" + name).text(value);
                        data.push({name: name, value: value});
                    });
                    var lindex;
                    $.ajax({
                        url: api_url,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        beforeSend: function (request) {
                            lindex = layer.load();
                        },
                        success: function (data, textStatus, request) {
                            var token = request.getResponseHeader(tokenname);
                            $("meta[name='" + tokenname + "']").attr('content', token);
                            $("input[name='" + tokenname + "']").attr('value', token);
                            layer.close(lindex);
                            if (data.status > 0) {
                                $(layero).find(("input")).each(function () {
                                    var name = $(this).attr('name');
                                    var value = $(this).val();
                                    $("#" + name).text(value);
                                    $("#" + handle + "-panlel").find("input[name='" + name + "']").attr('value', value);
                                });

                                layer.msg(data.msg, {icon: 6});
                                layer.close(pindex);
                            } else {
                                layer.msg(data.msg, {icon: 5});
                            }
                        }
                    });
                    return false;
                });
            }
            , yes: function (index, layero) {
                pindex = index;
                $("#prompt-form").click();

            }
            , cancel: function () {
                //右上角关闭回调

                //return false 开启该代码可禁止点击该按钮关闭
            }
        });
    });

    function sendemail(obj) {
        var data = new Array();
        var name = $(obj).attr("data-handle");
        var lindex;

        var mailto = $(obj).parent().parent().find("input[name='" + name + "']").val();
        data.push({name: 'action', value: 'checkmail'});
        data.push({name: 'mailtype', value: 'crcakmail'});
        data.push({name: 'username', value: $(obj).attr("data-username")});
        data.push({name: 'mailtitle', value: $(obj).attr("data-title")});
        data.push({name: 'mailto', value: mailto});
        data.push({name: 'sid_id', value: sid_id});
        data.push({name: tokenname, value: $("meta[name='" + tokenname + "']").attr('content')});

        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: data,
            beforeSend: function (request) {
                lindex = layer.load();
            },
            success: function (data, textStatus, request) {
                var token = request.getResponseHeader(tokenname);
                $("meta[name='" + tokenname + "']").attr('content', token);
                $("input[name='" + tokenname + "']").attr('value', token);
                layer.close(lindex);
                if (data.status > 0) {

                    layer.msg(data.msg, {icon: 6});
//                    layer.close(pindex);
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            }
        });
    }

    function sendsms(obj) {
        var data = new Array();
        var name = $(obj).attr("data-handle");
        var lindex;

        var mailto = $(obj).parent().parent().find("input[name='" + name + "']").val();
        data.push({name: 'action', value: 'checksms'});
        data.push({name: 'smstype', value: 'crcaksms'});
        data.push({name: 'mobile', value: mailto});
        data.push({name: 'mailtitle', value: $(obj).attr("data-title")});
        data.push({name: 'sid_id', value: sid_id});
        data.push({name: tokenname, value: $("meta[name='" + tokenname + "']").attr('content')});

        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: data,
            beforeSend: function (request) {
                lindex = layer.load();
            },
            success: function (data, textStatus, request) {
                var token = request.getResponseHeader(tokenname);
                $("meta[name='" + tokenname + "']").attr('content', token);
                $("input[name='" + tokenname + "']").attr('value', token);
                layer.close(lindex);
                if (data.status > 0) {

                    layer.msg(data.msg, {icon: 6});
//                    layer.close(pindex);
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            }
        });
    }

    /**
     * 验证码更新
     */
    $("#vcodeImg").click(function () {
        var time = new Date().getTime();
        $(this).attr({"src": verifyURL + "/?" + time});
    });

    function deletefile(obj) {
        var layindex = layer.confirm("文件删除后将不能恢复，确定要删除吗？", {
            title: "删除图片",
            resize: false,
            btn: ['确定', '取消'],
            btnAlign: 'c',
            anim: 1,
            icon: 3
        }, function () {
            var name = $(obj).attr('imgval');
            var handid = $(obj).attr('handid');
            var btnid = handid.replace('val', "#demo");
            $.ajax({
                url: delfile,
                type: 'post',
                async: false,
                data: {"filepath": name, usermodel: 'pcusers'},
                success: function (data, textStatus, request) {
               
                    layer.close(layindex);
//                    var token = request.getResponseHeader(tokenname);
//                    $("meta[name='" + tokenname + "']").attr('content', token);
//                    $("input[name='" + tokenname + "']").attr('value', token);
                    if (data.status == 0) {
                        common.cmsLayErrorMsg(data.msg);
                    } else {
                        var parent = $(obj).parent().parent();
                        $(btnid).attr('src', "/Public/images/select.png");
                        $(obj).attr("imgval", '');
                        //多图时直接移除
                        if ($(parent).hasClass('gridly')) {
                            $(obj).parent().remove();
                        }
                        var imglist = getimglist(parent);
                        $("#" + handid).val(imglist);
                        $(".auto-save").click();
                    }
                }
            });
        });
    }


    /**删除*/
    $("body").on("click", ".diag_del", function () {
        var data = $(".layui-form").serializeArray();
        var action = $(this).attr("data-action");
        var dataid = $(this).attr("data-id");
        data.push({name: 'action', value: action});
        data.push({name: 'id', value: dataid});

        var datatitle = $(this).attr("data-title");
        var message = $(this).attr("data-message");
        if (message.length == 0) {
            message = "确定删除数据吗";
        }
        common.ajaxCmsConfirm(datatitle, message, api_url, data);
    });

    $(".switchpannle").on("click", function () {
        var vname = $(this).attr("id");
        if ($('#handle-' + vname).is(':hidden')) {
            $('#handle-' + vname).show(400);
            $('#' + vname).html('&#xe61a;');
        } else {
            $('#handle-' + vname).hide(400);
            $('#' + vname).html('&#xe619;');
        }
    });
    
    $(".startchecked").on('click',function(){
        var id = $(this).attr("data-id");
        var dataratingvalue = $(this).attr("data-rating-value");
        $("#star-" + id).find('a').removeClass('br-selected');
        $("#star-" + id).find('a').removeClass('br-current');
        $("#star-" + id).find('a').each(function () {
            if (dataratingvalue >= $(this).attr("data-rating-value")) {
                $(this).addClass("br-selected");
            }
        });
        $(this).addClass("br-current");
        $("#"+id).val($(this).attr("data-rating-value"));      
    });

    /*修改属性*/
    $("body").on("click", ".rotationattr", function () {
        var data = $(".layui-form").serializeArray();
        var action = $(this).attr("data-action");
        var dataid = $(this).attr("data-id");
        var thand = $(this).attr("data-thand");
        var format = $(this).attr("data-format");
        var obj = $(this);
        if (thand !== 'self') {
            obj = $(this).parent().find(thand);
        }
        data.push({name: 'action', value: action});
        data.push({name: 'id', value: dataid});
        if (format !== '') {
            data.push({name: 'format', value: $(this).attr("data-format")});
        }
        var datatitle = $(this).attr("data-title");
        var message = $(this).attr("data-message");
        if (message.length == 0) {
            message = "确定删除数据吗";
        }

        layer.confirm(message, {
            title: datatitle,
            resize: false,
            btn: ['确定', '取消'],
            btnAlign: 'c',
            anim: 1,
            icon: 3
        }, function (index) {
            $.ajax({
                url: api_url,
                type: 'post',
                async: false,
                data: data,
                success: function (data, textStatus, request) {
                    var token = request.getResponseHeader(tokenname);
                    $("meta[name='" + tokenname + "']").attr('content', token);
                    $("input[name='" + tokenname + "']").attr('value', token);
                    layer.close(index);
                    if (data.status == 1) {
                        //top.layer.msg(data.msg, {icon: 6});
                        //location.reload();
                        $(obj).html(data.curenttext);
                    } else {
                        top.layer.msg(data.msg, {icon: 5});
                    }
                }
            });
        });
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
        var param = {idlist: ids};
        common.ajaxFunConfirm(datatitle, message, dataurl, param, userPageList, curentpage);
    });

    /**删除*/
    $("body").on("click", ".photolistoption", function () {
        var id = $(this).attr("data-id");
        var value = $(this).attr("data-value");
        $("#" + id).val(value);
        $(this).parent().find('li').removeClass('select');
        $(this).addClass('select');
    });

    /**打开模态窗口*/
    $("body").on("click", ".diag_modal", function () {
        var dataurl = $(this).attr("data-url");
        var datatitle = $(this).attr("data-title");
        var width = $(this).attr("data-width");
        var height = $(this).attr("data-height");
        if (typeof (width) === 'undefined') {
            width = "840px";
        }
        if (typeof (height) === 'undefined') {
            height = "600px";
        }
        common.cmsLayOpen(datatitle, dataurl, width, height);
    });

    /**
     * 分配房号
     */
    $(".select_ft").on("click", function () {
        $("#roomftid", window.parent.document).val($(this).attr("data-id"));
        $("#roomft_no", window.parent.document).html($(this).attr("data-titleno"));
        close_diagmodal();
    });
        
    var bhlayerindex=0;
    $(".layui-table-cell-over").on("click", function () {
//        if (this.offsetWidth < this.scrollWidth) {
            layer.close(bhlayerindex);
            var content = $(this).html();
            content = '<div class="notice-content"><ul class="layer_notice layui-layer-wrap" style="display: block;">' + content + '</div>';
            bhlayerindex = layer.open({
                type: 1,
                shade: false,
                title: false, //不显示标题
                content: content,
            });
//        }
    });

    form.on('radio(status)', function (data) {
//        console.log(data.elem); //得到radio原始DOM对象
//        console.log(data.value); //被点击的radio的value值
        $(".status-pannel").hide();
        $("#status-pannel-" + data.value).show();
    });

    /**
     * 业主保存定单
     */
    form.on("submit(submit_order)", function (data) {
        var field = data.field;
        var reload = $(this).attr('data-reload');
        var showmsg = $('.auto-save').attr('data-noshowmsg');
        var jumpurl = $(this).attr('lay-jump');

        field.usermodel = 'pcusers';
        if (typeof (field.status) === 'undefined') {
            layer.msg("请选择定单类型");
            return false;
        }
        switch (field.status) {
            case "2":
                var cntmemo = field.memo;
                if (cntmemo.length < 4) {
                    layer.msg("必须填写多于4个字的备注");
                    return false;
                }
                break;
            case "1":
                if (field.roomftid === "") {
                    layer.msg("请为客户分配房号");
                    return false;
                }
                break;
        }

        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: field,
            beforeSend: function (request) {
                index = layer.load();
            },
            success: function (data, textStatus, request) {
                var token = request.getResponseHeader(tokenname);
                $("meta[name='" + tokenname + "']").attr('content', token);
                $("input[name='" + tokenname + "']").attr('value', token);
                layer.close(index);
                if (data.status > 0) {
                    if (reload === '2') {
                        layer.msg(data.msg, {icon: 6});
                        location.reload(); //刷新父页面               
                    } else if (reload === '1') {
                        layer.msg(data.msg, {icon: 6});
                        parent.layer.close(index); //再执行关闭    
                        parent.location.reload(); //刷新父页面

                    } else {
                        if (typeof (data.location) !== "undefined" || typeof (jumpurl) !== "undefined") {
                            layer.msg(data.msg, {icon: 6, time: 500}, function () {
                                if (typeof (jumpurl) !== "undefined") {
                                    parent.location.href = jumpurl;
                                } else {
                                    parent.location.href = data.location;
                                }
                            });
                        } else {
                            if (showmsg === '1') {
                                //静默提交                    
                            } else {
                                layer.msg(data.msg, {icon: 6});
                            }
                        }
                    }
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
            error: function (data) {
            }
        });
        return false;
    });
    
        /**
     * 会员保存定单
     */
    form.on("submit(submit_myorder)", function (data) {
        var field = data.field;
        var reload = $(this).attr('data-reload');
        var showmsg = $('.auto-save').attr('data-noshowmsg');
        var jumpurl = $(this).attr('lay-jump');

        field.usermodel = 'pcusers';

        switch (field.status) {
            case "4":
                var cntmemo = field.memo;
                if (cntmemo.length < 4) {
                    layer.msg("必须填写多于4个字的备注");
                    return false;
                }
                break;
            case "5":
                var cntmemo = field.memo;
                if (cntmemo.length < 4) {
                    layer.msg("必须填写多于4个字的备注");
                    return false;
                }
                break;
        }

        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: field,
            beforeSend: function (request) {
                index = layer.load();
            },
            success: function (data, textStatus, request) {
                var token = request.getResponseHeader(tokenname);
                $("meta[name='" + tokenname + "']").attr('content', token);
                $("input[name='" + tokenname + "']").attr('value', token);
                layer.close(index);
                if (data.status > 0) {
                    if (reload === '2') {
                        layer.msg(data.msg, {icon: 6});
                        location.reload(); //刷新父页面               
                    } else if (reload === '1') {
                        layer.msg(data.msg, {icon: 6});
                        parent.layer.close(index); //再执行关闭    
                        parent.location.reload(); //刷新父页面

                    } else {
                        if (typeof (data.location) !== "undefined" || typeof (jumpurl) !== "undefined") {
                            layer.msg(data.msg, {icon: 6, time: 500}, function () {
                                if (typeof (jumpurl) !== "undefined") {
                                    parent.location.href = jumpurl;
                                } else {
                                    parent.location.href = data.location;
                                }
                            });
                        } else {
                            if (showmsg === '1') {
                                //静默提交                    
                            } else {
                                layer.msg(data.msg, {icon: 6});
                            }
                        }
                    }
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
            error: function (data) {
            }
        });
        return false;
    });

    

    /**
     * 关闭对话框
     * @returns {undefined}
     */
    function close_diagmodal() {
        var windindex = parent.layer.getFrameIndex(window.name);
        parent.layer.close(windindex)
    }

    /**
     * 联系人管理
     */
    $(".linkmanselect").on('click', function () {
        var layerindex;
        var postdata = {usermodel: 'pcusers', action: 'get_linkman', size: 0, sid_id: sid_id, userid: userid};
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
                if (data.status == 1) {
                    layerindex = layer.open({
                        title: '联系人管理',
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['820px', '440px'], //宽高
                        content: "<div><table id=\"tablist\" lay-filter=\"mytablist\"></table><a class=\"layui-btn \" href=\"/Ucent/linkman/index.html\" target=\"_blank\" style='float:right;margin-right:10px;'>管理联系方式</a></div>"
                                + "<script type=\"text/html\" id=\"barDemo\">"
                                + "{{#  if(d.id > 0){ }}<a class=\"layui-btn layui-btn layui-btn-xs\" lay-event=\"select\">选择</a>"
//                                + "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>"
                                + "{{#  } else { }}"
                                + "<a class=\"layui-btn layui-btn-xs\" lay-event=\"add\">添加</a>{{#  } }}"
//                              + "<a class=\"layui-btn layui-btn-xs\" lay-event=\"thumb\">头像</a>"
                                + "<\/script>"
                                + " <script type=\"text/html\" id=\"switchTpl\">"
                                + "{{#  if(d.id > 0){ }} <input type=\"checkbox\" name=\"is_default\" value=\"{{d.id}}\" lay-skin=\"switch\" lay-text=\"是|否\" lay-filter=\"is_default\" {{ d.is_default == 1 ? 'checked' : '' }}>{{#  } }}"
                                + "<\/script>"
                                + " <script type=\"text/html\" id=\"imgTpl\">"
                                + "{{#  if(d.thumb == ''){ }}"
                                + " <img class=\"layui-upload-img2\" style=\"width:100%;\" handid=\"valfupthumb{{d.LAY_TABLE_INDEX}}\" name=\"thumb\" id='demofupthumb{{d.LAY_TABLE_INDEX}}' src=\"/Public/images/select.png\"  >"
                                + "{{#  } else { }}"
                                + " <img class=\"layui-upload-img2\" style=\"width:100%;\" handid=\"valfupthumb{{d.LAY_TABLE_INDEX}}\" name=\"thumb\" id='demofupthumb{{d.LAY_TABLE_INDEX}}' src=\"{{d.thumb}}\"  >"
                                + "{{#  } }}"
                                + " <input type=\"hidden\" class=\"thumbchange\" id=\"valfupthumb{{d.LAY_TABLE_INDEX}}\" name=\"thumb\" value=\"{{d.thumb}}\" >"
                                + "<\/script>",
                        success: function () {
                            var dlist = data.list;
//                            dlist.push({id: '','thumb':''});
                            table.render({
                                elem: '#tablist'
                                , width: 810
                                , data: dlist
                                , cellMinWidth: 80
                                , page: false //开启分页
                                , cols: [[//表头
                                        {field: 'id', title: 'ID', sort: true, fixed: 'left', width: 60}
                                        , {field: 'title', title: '联系人', width: 100}
                                        , {field: 'mobile', title: '手机号码', width: 120}
//                                , {field: 'tel', title: '固定电话'}
//                                , {field: 'hottel', title: '热线电话'}
                                        , {field: 'address', title: '联系地址'}
                                        , {field: 'is_default', title: '默认', templet: '#switchTpl', unresize: true, width: 100}
                                        , {field: 'thumb', title: '头像', templet: '#imgTpl', unresize: true, width: 140}
                                        , {field: '', title: '操作', toolbar: '#barDemo', width: 80}
                                    ]]
                                , done: function (obj) {
                                    $('.layui-upload-img2').unbind('click');
                                    $('.layui-upload-img2').click(function () {
                                        //  imgcoper(this, 1);
                                    });
                                }
                            });

                            //监听单元格编辑
                            table.on('edit(mytablist)', function (obj) {
                                var value = obj.value //得到修改后的值
                                        , data = obj.data //得到所在行所有键值
                                        , field = obj.field; //得到字段
                                if (data.id === '') {
                                    return;
                                }
                                var cdata = ({usermodel: 'pcusers', action: 'creat_linkman', sid_id: sid_id, userid: userid, id: data.id});
                                cdata[field] = value;
                                cdata[tokenname] = $("meta[name='" + tokenname + "']").attr('content');
                                $.ajax({
                                    url: api_url,
                                    type: 'post',
                                    dataType: 'json',
                                    data: cdata,
                                    success: function (data, textStatus, request) {
                                        var token = request.getResponseHeader(tokenname);
                                        $("meta[name='" + tokenname + "']").attr('content', token);
                                        $("input[name='" + tokenname + "']").attr('value', token);
                                        if (data.status === 0) {
                                            layer.msg(data.msg);
                                        }
                                    }});
                            });

                            /**
                             * 工具条单元格
                             */
                            table.on('tool(mytablist)', function (obj) {
                                var data = obj.data;
                                var tr = obj.tr;
                                data['thumb'] = $(tr).find("input[name='thumb']").val();
                                switch (obj.event) {
                                    case 'select':
                                        $("#linkid").val(data.id);
                                        $("#linkman").val(data.title);

                                        $("#lx_address").val(data.address);
                                        $("#lx_email").val(data.email);

                                        $("#lx_mobile").val(data.mobile);
                                        $("#mobile").val(data.mobile);

                                        $("#lx_username_txt").text(data.title);
                                        $("#lx_mobile_txt").text(data.mobile);
                                        $("#lx_address_txt").text(data.address);
                                        layer.close(layerindex);
                                        break;
                                    case 'del':
                                        var cdata = {usermodel: 'pcusers', action: 'del_linkman', sid_id: sid_id, userid: userid, id: data.id};
                                        cdata[tokenname] = $("meta[name='" + tokenname + "']").attr('content');
                                        $.ajax({
                                            url: api_url,
                                            type: 'post',
                                            dataType: 'json',
                                            data: cdata,
                                            success: function (data, textStatus, request) {
                                                rerandtable(request);
                                                if (data.status === 1) {
                                                    layer.msg(data.msg);
                                                }
                                            }});
                                        break;
                                    case 'add':
                                        if (typeof (data.title) == "undefined") {
                                            layer.msg('请输入联系人');
                                            return;
                                        }
//                                        console.log(data);
//                                        return ;
                                        data['usermodel'] = 'pcusers';
                                        data['action'] = 'creat_linkman';
                                        data['sid_id'] = sid_id;
                                        data['userid'] = userid;
                                        data[tokenname] = $("meta[name='" + tokenname + "']").attr('content');
                                        $.ajax({
                                            url: api_url,
                                            type: 'post',
                                            dataType: 'json',
                                            data: data,
                                            success: function (data, textStatus, request) {
                                                rerandtable(request);
                                                if (data.status === 1) {
                                                    layer.msg(data.msg);
                                                }
                                            }});
                                        break;
                                }
                            });

                            //监听默认联系人更换
                            form.on('switch(is_default)', function (obj) {
                                var cdata = ({usermodel: 'pcusers', action: 'setdefault_linkman', sid_id: sid_id, userid: userid, id: this.value});
                                cdata[tokenname] = $("meta[name='" + tokenname + "']").attr('content');
                                $.ajax({
                                    url: api_url,
                                    type: 'post',
                                    dataType: 'json',
                                    data: cdata,
                                    success: function (data, textStatus, request) {
                                        rerandtable(request);
                                    }});
                            });
                        }
                    });
                }
            }
        });
    });

    /**
     * 联系人列表渲染
     * @returns {undefined}
     */
    function rerandtable(request) {
        var postdata = {usermodel: 'pcusers', action: 'get_linkman', size: 0, sid_id: sid_id, userid: userid};
        postdata[tokenname] = request.getResponseHeader(tokenname);
        ;
        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: postdata,
            success: function (data, textStatus, request) {
                var token = request.getResponseHeader(tokenname);
                $("meta[name='" + tokenname + "']").attr('content', token);
                $("input[name='" + tokenname + "']").attr('value', token);
                if (data.status == 1) {
                    var dlist = data.list;
//                    dlist.push({id: ''});
                    var tableIns = table.render({
                        elem: '#tablist'
                        , width: 810
                        , data: dlist
                        , cellMinWidth: 80
                        , page: false //开启分页
                        , cols: [[//表头
                                {field: 'id', title: 'ID', sort: true, fixed: 'left', width: 60}
                                , {field: 'title', title: '联系人', width: 100}
                                , {field: 'mobile', title: '手机号码', width: 120}
//                                , {field: 'tel', title: '固定电话'}
//                                , {field: 'hottel', title: '热线电话'}
                                , {field: 'address', title: '联系地址'}
                                , {field: 'is_default', title: '默认', templet: '#switchTpl', unresize: true, width: 100}
                                , {field: 'thumb', title: '头像', templet: '#imgTpl', unresize: true, width: 140}
                                , {field: '', title: '操作', toolbar: '#barDemo', width: 80}
                            ]]
                        , done: function (obj) {
                            $('.layui-upload-img2').unbind('click');
                            $('.layui-upload-img2').click(function () {
                                imgcoper(this, 1);
                            });

                            $(".thumbchange").on('change', function () {
//                                        console.log($(this).val());
                            }); //.change();
                        }

                    });
                }
            }});

    }

    /**
     * 私有相册管理
     */
    $(".layui-upload-photopanle").on('click', function () {
        common.imgFlow($(this), uploadimg, delfile, sid_id, userid, imgcoper);
    });

    /**
     * 显示折扣信息
     */
    $(".showdiscount").on("click", function () {
        table.init('parse-table-demo', {//转化静态表格
            width: 'full-500'
        });
        layer.open({
            type: 1,
//            shade: false,
            area: [600, 500],
            title: false, //不显示标题
            content: $('.discountcontent'), //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
            success: function () {

            }
        });
    });

    layer.photos({
        photos: '.layerphoto'
        , anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
    });

    /*日期选择*/
    laydate.render({
        elem: '#start', //绑定的位置
        type: 'date', //时间输出方式 日期/日期时间
        min: 0, //时间节点
        calendar: true //公历节日
    });


    $('.rentingmod').on('click', function () {
        $('.unitname').html($(this).attr("data-title"));
        var ptype = $(this).val();
        var count = $("#count").val();
        get_roomprice(ptype, count);
    });

    $("#count").on('blur', function () {
        var count = $(this).val();
        var ptype = $("input[name='rentingmod']:checked").val();
        get_roomprice(ptype, count);
    });

    /**
     * 取折扣信息
     * @param {type} ptype
     * @param {type} count
     * @returns {undefined}
     */
    function get_roomprice(ptype, number) {
        var roomid = $("#roomid").val();
        var postdata = {action: 'get_discount', usermodel: 'pcusers', roomid: roomid, pricetype: ptype, number: number, userid: userid};
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
                    $(".discount").html(data.data.discount);
                    $("#deposit").html(data.data.deposit);
                    $("#zhujing").html(data.data.total);
                    $(".housePriceNu").html(data.data.totalall);
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
        });
    }
});