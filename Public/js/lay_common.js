/**自定义模块*/
layui.define(['layer', 'form', 'table', 'element', 'upload','layedit','laydate'], function (exports) {
    "use strict";
    var $ = layui.jquery,
            form = layui.form,
            layer = layui.layer,
            element = layui.element,
            layedit = layui.layedit,
            upload = layui.upload,
            laydate = layui.laydate,
            table = layui.table;

    var bsid_id = 0, buserid = 0, buploadimg = '', bdelfile = '', tableindex = 0, handle, photoinfo,layeditorindex=[];

    form.verify({
        username: function (value, item) { //value：表单的值、item：表单的DOM对象
            if (!new RegExp("^[a-zA-Z0-9\u4e00-\u9fa5\\s·]+$").test(value)) {

                var attr = $(item).attr("lay-verify-msg-username");
                if (typeof attr !== typeof undefined && attr !== false) {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false) {
                    return attr;
                }
                item.focus();//自动聚焦到验证不通过的输入框或字段
                return "不匹配用户名规则";
            }
        }
        ,moyueditor: function(value) { 
            $.each(layeditorindex,function(){
                layedit.sync(this);
            })
            return "";
        }
        , mustradio: function (value, item) { //value：表单的值、item：表单的DOM对象     
            var va = $(item).find("input[type='radio']:checked").val();
            if (typeof (va) == "undefined") {
                return $(item).attr("lay-verify-msg");
            }
        }
        , mustcheck: function (value, item) { //value：表单的值、item：表单的DOM对象            
            var va = $(item).find("input[type='checkbox']:checked").val();
            if (typeof (va) == "undefined") {
                return $(item).attr("lay-verify-msg");
            }
        }
        , muststar: function (value, item) { //value：表单的值、item：表单的DOM对象    
            if (!new RegExp("^\\d+$").test(value)) {
                return $(item).attr("lay-verify-msg");
            }
        }
        , required: function (value, item) { //value：表单的值、item：表单的DOM对象
            if (!(/[\S]+/.test(value))) {

                var attr = $(item).attr("lay-verify-msg-required");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '必填项不能为空';
            }
        }
        , phone: function (value, item) { //value：表单的值、item：表单的DOM对象
            if (!(/^1\d{10}$/.test(value))) {

                var attr = $(item).attr("lay-verify-msg-phone");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '请输入正确的手机号';
            }
        }
        , email: function (value, item) { //value：表单的值、item：表单的DOM对象
            if (!(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value))) {

                var attr = $(item).attr("lay-verify-msg-email");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '请输入正确的手机号';
            }
        }
        , url: function (value, item) { //value：表单的值、item：表单的DOM对象
            if (!(/(^#)|(^http(s*):\/\/[^\s]+\.[^\s]+)/.test(value))) {

                var attr = $(item).attr("lay-verify-msg-url");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '请输入正确的手机号';
            }
        }
        , number: function (value, item) {
            if (!value || isNaN(value)) {
                var attr = $(item).attr("lay-verify-msg-number");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '只能填写数字';
            }
        }
        , date: function (value, item) { //value：表单的值、item：表单的DOM对象
            if (!(/^(\d{4})[-\/](\d{1}|0\d{1}|1[0-2])([-\/](\d{1}|0\d{1}|[1-2][0-9]|3[0-1]))*$/.test(value))) {

                var attr = $(item).attr("lay-verify-msg-date");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '日期格式不正确';
            }
        }
        , identity: function (value, item) { //value：表单的值、item：表单的DOM对象
            if (!(/(^\d{15}$)|(^\d{17}(x|X|\d)$)/.test(value))) {

                var attr = $(item).attr("lay-verify-msg-identity");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '请输入正确的身份证号';
            }
        }
        , greater: function (value, item) {

            var compare = $(item).attr("lay-verify-compare-greater");
            var compareval = $("#" + compare).val();
            var x = parseFloat(value) < parseFloat(compareval);
            if (x) {
                var attr = $(item).attr("lay-verify-msg-greater");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }

                attr = $(item).attr("lay-verify-msg");
                if (typeof attr !== typeof undefined && attr !== false && attr !== '') {
                    return attr;
                }
                return '输入数据过大';
            }
        }
    });

    //将当前数据保存回页面
    function savevalue() {
        var jsstr = JSON.stringify(photoinfo);
        $("#" + handle).text(jsstr);
    }

    //排序
    function sortbyid(a, b) {
        return a.id - b.id;
    }

    //图片列表渲染
    function randtable(el, picsize, buploadimg, cutfile, usermodel) {
        var dlist = photoinfo['list'][el['tagvalue']];
        table.render({
            elem: '#tablist' + el['tagvalue']
            , data: dlist
            , cellMinWidth: 80
            , page: false //开启分页
            , cols: [[//表头
                    {field: 'id', title: 'ID', edit: 'text', width: 80}
//                  , {field: 'is_default', title: '默认', templet: '#switchTpl', unresize: true}
                    , {field: 'thumb', title: '图像', templet: '#imgTpl', unresize: true, width: 200}
                    , {field: 'memo', title: '图片说明', edit: 'text', width: 400}
                    , {field: '', title: '操作', toolbar: '#barDemo', width: 110}
                ]]
            , initSort: {field: 'id', type: 'asc'}
            , done: function (obj) {
                $('.layui-upload-img2').unbind('click');
                $('.layui-upload-img2').click(function () {
                    CmsCommon.ImgCoper(this, 0, picsize, buploadimg, '', cutfile, usermodel);
                });
            }
        });

        /**
         * 工具条单元格
         */
        table.on('tool(mytablist' + el['tagvalue'] + ')', function (obj) {
            var data = obj.data;
            switch (obj.event) {
                case 'del':
                    layer.confirm('删除后无法恢复，确定要删除吗？', {
                        title: '删除照片',
                        resize: false,
                        btn: ['确定', '取消'],
                        btnAlign: 'c',
                        anim: 1,
                        icon: 3
                    }, function (index) {
                        $.ajax({
                            url: bdelfile,
                            type: 'post',
                            async: false,
                            data: {"filepath": data.thumb, usermodel: 'pcusers', sid_id: bsid_id, userid: buserid},
                            success: function (d) {
                                layer.close(index);
                                if (data.status === 0) {
                                    layer.msg(d.msg);
                                } else {
                                    var dlist = photoinfo['list'][el['tagvalue']];
                                    $(dlist).each(function (index, el) {
                                        if (el.thumb === data.thumb) {
                                            dlist.splice(index, 1);
                                        }
                                    });
                                    photoinfo['list'][el['tagvalue']] = dlist;
                                    obj.del();
                                    savevalue();
                                }
                            }
                        });
                    }, function () {

                    });
                    break;
            }
        });

        table.on('sort(mytablist' + el['tagvalue'] + ')', function (obj) {
            var dlist = photoinfo['list'][el['tagvalue']];
            dlist.sort(function (a, b) {
                if (obj.type === 'asc') {
                    return a.id - b.id
                } else {
                    return b.id - a.id
                }
            });
            photoinfo['list'][el['tagvalue']] = dlist;
            savevalue();
        });

        table.on('edit(mytablist' + el['tagvalue'] + ')', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = obj.data;
            var dlist = photoinfo['list'][el['tagvalue']];
            $(dlist).each(function (index, el) {
                if (el.thumb === data.thumb) {
                    dlist[index] = data;
                }
            });
            photoinfo['list'][el['tagvalue']] = dlist;
            savevalue();
        });
    }

    var CmsCommon = {
        /**错误msg提示 */
        cmsLayErrorMsg: function (text) {
            top.layer.msg(text, {icon: 5});
        },
        /**成功 msg提示 */
        cmsLaySucMsg: function (text) {
            top.layer.msg(text, {icon: 6});
        },
        Login: function (message, url) {
            layer.msg(message, {icon: 6, time: 800, shade: [0.5, '#f5f5f5']}, function () {
                //回调
                location = url;
            });
        },
        LoginQz: function (title, message, url) {
            layer.confirm(message, {
                title: title,
                resize: false,
                btn: ['确定', '取消'],
                btnAlign: 'c',
                anim: 1,
                icon: 3
            }, function () {
                location = url;
            })
        },
        /**
         * 对话框，执行ajax操作成功后执行后续动作Fun(fparam)
         * @param {type} title 对话框标题
         * @param {type} text 对话框内容
         * @param {type} url  ajax 命令
         * @param {type} param ajax 参数
         * @param {type} Fun  后续命令
         * @param {type} fparam 后续命令参数
         * @returns {undefined} 
         */
        ajaxFunConfirm: function (title, text, url, param, Fun, fparam) {        
            layer.confirm(text, {
                title: title,
                resize: false,
                btn: ['确定', '取消'],
                btnAlign: 'c',
                anim: 1,
                icon: 3
            }, function (index) {
                $.ajax({
                    url: url,
                    type: 'post',
                    async: false,
                    data: param,
                    success: function (data) {
                        layer.close(index);                
                        if (data.status === 0) {
                            top.layer.msg(data.msg, {icon: 6});                     
                            if (Fun && (typeof Fun == "function")) {  //判断是否是过程                                 
                                Fun(fparam);
                            } 
                        } else {
                            top.layer.msg(data.msg, {icon: 5});
                        }
                    }, error: function (data) {

                    }
                });

            }, function () {

            })

        },
        /**ajax Confirm 对话框*/
        ajaxCmsConfirm: function (title, text, url, param, tokenname) {
            layer.confirm(text, {
                title: title,
                resize: false,
                btn: ['确定', '取消'],
                btnAlign: 'c',
                anim: 1,
                icon: 3
            }, function (index) {
                $.ajax({
                    url: url,
                    type: 'post',
                    async: false,
                    data: param,
                    success: function (data, textStatus, request) {
                        var token = request.getResponseHeader(tokenname);
                        $("meta[name='" + tokenname + "']").attr('content', token);
                        $("input[name='" + tokenname + "']").attr('value', token);
                        layer.close(index);
                        if (data.status === 0) {
                            top.layer.msg(data.msg, {icon: 6});
                            location.reload();
                        } else {
                            top.layer.msg(data.msg, {icon: 5});
                        }
                    }, error: function (data) {

                    }
                });

            }, function () {

            })

        },
        /**ajax 树形删除对话框*/
        ajaxCmsDelete: function (title, text, url, param, tokenname, obj) {
            layer.confirm(text, {
                title: title,
                resize: false,
                btn: ['确定', '取消'],
                btnAlign: 'c',
                anim: 1,
                icon: 3
            }, function (index) {
                $.ajax({
                    url: url,
                    type: 'post',
                    async: false,
                    data: param,
                    success: function (data, textStatus, request) {
                        var token = request.getResponseHeader(tokenname);
                        $("meta[name='" + tokenname + "']").attr('content', token);
                        $("input[name='" + tokenname + "']").attr('value', token);
                        layer.close(index);
                        if (data.status === 0) {
                            top.layer.msg(data.msg, {icon: 6});
                            var xpath = $(obj).parents('tr').attr('xpath');
                            $("tr[xpath^='" + xpath + "']").remove();
                        } else {
                            top.layer.msg(data.msg, {icon: 5});
                        }
                    }, error: function (data) {

                    }
                });

            }, function () {

            })

        },
        /** ajax 表单提交 */
        ajaxSubmit: function (obj, data, url, tokenname, Fun, fparam) {
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: data.field,
                success: function (data, textStatus, request) {
                    var token = request.getResponseHeader(tokenname);
                    $("meta[name='" + tokenname + "']").attr('content', token);
                    $("input[name='" + tokenname + "']").attr('value', token);
                    if (data.status === 0) {
                        CmsCommon.cmsLaySucMsg(data.msg);
                        if (Fun && (typeof Fun == "function")) {  //判断是否是过程                                 
                            Fun(fparam);
                        }
                        else{
                            var jump=$(obj).data("jump");
                            if (typeof jump !== 'undefined') {
                                location = jump;
                            }
                            console.log($(obj).data());
                            if ($(obj).data("reload")== 1) {
                                location.reload(); //刷新父页面
                            }
                            if ($("#jumpandflash").attr('atr') == 'true') {
                            parent.location.reload(); //刷新父页面
                        }
                        }
                    } else {
                        CmsCommon.cmsLayErrorMsg(data.msg);
                    }
                }, 
            });
            return false;
        },
        /**弹出层*/
        cmsLayOpen: function (title, url, width, height, isTop) {
            if (typeof (isTop) == "undefined" || isTop == null) {
                var index = layui.layer.open({
                    title: '<i class="larry-icon larry-bianji3"></i>' + title,
                    type: 2,
                    skin: 'layui-layer-molv',
                    content: url,
                    area: [width, height],
                    resize: true,
                    maxmin: true,
                    anim: 1,
                    success: function (layero, index) {

                    }
                })
            } else {
                var index = top.layui.layer.open({
                    title: '<i class="larry-icon larry-bianji3"></i>' + title,
                    type: 2,
                    skin: 'layui-layer-molv',
                    content: url,
                    area: [width, height],
                    resize: true,
                    maxmin: true,
                    anim: 1,
                    success: function (layero, index) {

                    }
                })
            }
        },
        /**退出*/
        logOut: function (title, text, url, type, dataType, data, callback) {
            parent.layer.confirm(text, {
                title: title,
                resize: false,
                btn: ['确定退出系统', '不，我点错了！'],
                btnAlign: 'c',
                icon: 3
            }, function () {
                location.href = url
            }, function () {

            })
        },
        /* 多图上传*/
        imgFlow: function (obj, bbuploadimg, bbdelfile, bbsid_id, bbuserid,cutfile) {
            handle = $(obj).attr("data-handle");
            var usermodel = $(obj).attr("usermodel");
            if (typeof (usermodel) === 'undefined') {
                usermodel = "pcusers";
            }
            buploadimg = bbuploadimg;
            bdelfile = bbdelfile;
            var jsonstr = $("#" + handle).text();
            photoinfo = $.parseJSON(jsonstr);

            var card = photoinfo['card'];
            bsid_id = bbsid_id;
            buserid = bbuserid;
            var picsize=2048;
            var loadindex;
//       
            var layerindex = parent.layer.open({
                title: '图片管理',
                type: 1,
                //skin: 'layui-layer-rim', //加上边框
                area: ['820px', '540px'], //宽高
                content: "<script type=\"text/html\" id=\"barDemo\">"
                        + "<a class=\"layui-btn layui-btn-danger layui-btn-xs\" lay-event=\"del\">删除</a>"
                        + "<\/script>"
                        + " <script type=\"text/html\" id=\"switchTpl\">"
                        + "{{#  if(d.id > 0){ }} <input type=\"checkbox\" name=\"is_default\" value=\"{{d.id}}\" lay-skin=\"switch\" lay-text=\"是|否\" lay-filter=\"is_default\" {{ d.is_default == 1 ? 'checked' : '' }}>{{#  } }}"
                        + "<\/script>"
                        + " <script type=\"text/html\" id=\"imgTpl\">"
                        + " <img class=\"layui-upload-img2\" style=\"width:100%;\" data-handle=\"{{d.LAY_TABLE_INDEX}}\" name=\"thumb\" id='demofup{{d.LAY_TABLE_INDEX}}'  src=\"{{d.thumb}}\"  lay-filter=\"thumb\" >"
                        + "<\/script>"

                        + "<div class=\"layui-tab layui-tab-card\" lay-filter=\"photopanel\">"
                        + "<ul class=\"layui-tab-title\"></ul>"
                        + "<div class=\"layui-tab-content\"></div></div>"
                        + " <div class=\"site-demo-button\" style=\"margin:auto 0px;text-align: center;\"><button class=\"layui-btn site-demo-active muploadimg\">添加图片</button>"
                        + " <button class=\"layui-btn site-demo-active savephoto\">保存</button></div>",
                success: function () {
                    $(card).each(function (index, el) {
                        var content = "<table id=\"tablist" + el['tagvalue'] + "\" lay-filter=\"mytablist" + el['tagvalue'] + "\"></table>";
                        element.tabAdd('photopanel', {
                            title: el['title']
                            , content: content
                            , id: 'p' + el['tagvalue']
                        });
                        randtable(card[tableindex], picsize, buploadimg, cutfile, usermodel);
                    });
                    element.tabChange('photopanel', 'p' + card[0]['tagvalue']);
                    //选项卡切换
                    element.on('tab(photopanel)', function (data) {
                        tableindex = data.index;
                        randtable(card[tableindex], picsize, buploadimg, cutfile, usermodel);
                    });                   
                    upload.render({
                        elem: '.muploadimg'
                        , exts: 'jpg|gif|png|jpeg|ico'
                        , size: picsize
                        , url: buploadimg
                        , data: {type: '', usermodel: usermodel}
                        , accept: 'file'
                        , multiple: true
                        , before: function () {
                            loadindex = layer.load();
                        }
                        , done: function (res, index, upload) {
                            layer.close(loadindex);
                            if (res.status != 0) {
                                return layer.msg(res.msg);
                            } else {
                                //var maxcardindex=photoinfo['list'][card[tableindex]['tagvalue']].length;
                                var maxcardindex = photoinfo['list'][card[tableindex]['tagvalue']].length;
                                photoinfo['list'][card[tableindex]['tagvalue']].push({id: maxcardindex, thumb: res.abspic, memo: res.name});
                                randtable(card[tableindex],picsize, buploadimg,cutfile,usermodel);
                                savevalue();
                            }
                        }
                    });

                    $(".savephoto").on('click', function () {
                        savevalue();
                        layer.close(layerindex);
                    });
                }
                , end: function () {
                    handle = $(obj).attr("data-handle");
                    var img = "";
                    var pho = $.parseJSON($("#" + handle).text());
                    $(pho['card']).each(function () {
                        var imglist = pho.list[this.tagvalue];
                        imglist.sort(sortbyid);
                        $(imglist).each(function () {
                            img = this.thumb;
                            if (img !== '') {
                                return false;
                            }
                        });
                        if (img !== '') {
                            return false;
                        }
                    });
                    if (img !== '') {
                        $(obj).attr("src", img);
                    } else {
                        $(obj).attr("src", '/Public/images/select.png');
                    }
                }
            })
        },
        
        attrPrompt: function (obj, tokenname, api_url, bbsid_id, bbuserid, busertype) {
            var idname = $(obj).data('idd');
            var filedname = $(obj).data('filedname');
            var idval = $(obj).data('idval');
            var filedval = $(obj).data('filedval');
            var format = $(obj).data("format");
            var nameroot = $(obj).data("nameroot");
            var datatitle = "修改<b>【" + $(obj).data("title") + "】</b>状态";

            var postdata = {action: 'prompt', 'idname': idname, 'id': idval, 'filedname': filedname, 'filedval': filedval, nameroot: nameroot, usertype: busertype};
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
//                console.log(data);
                    var score = "";
                    if (data['showscore'] === 1) {
                        score = "<div class=\"layui-form-item\" id=\"score_div\">"
                                + "<label class=\"layui-form-label\" id=\"score_name\">积分奖励</label>"
                                + "<div class=\"layui-input-block\" ><input type=\"hidden\" id=\"effectscore\" name=\"effectscore\" value=\"\"  />"
                                + data.score
                                + "</div>"
                                + "</div>";
                    }

                    var inputdate="";
                    if (data['inputdate'] !== '') {
                    inputdate = "<div class=\"layui-form-item\" id=\"inputdate_div\">"
                                + "<label class=\"layui-form-label\" id=\"inputdate_name\">输入日期</label>"
                                + "<div class=\"layui-input-block\" >"
                                + data.inputdate
                                + "</div>"
                                + "</div>";
                    }

                    var content = "<div class=\"layui-tab\">"
                            + "<ul class=\"layui-tab-title\">"
                            + " <li class=\"layui-this\">当前操作</li>"
                            + " <li>历史记录</li>"
                            + "</ul>"
                            + "<div class=\"layui-tab-content\">"
                            + "<div class=\"layui-tab-item layui-show\">"
                            + "<form class=\"layui-form layui-form-pane\" action=\"\">"
                            + score
                            + inputdate
                            + "<div class=\"layui-form-item layui-form-text\">"
                            + "<label class=\"layui-form-label\">操作说明</label>"
                            + "<div class=\"layui-input-block\" ><textarea name=\"memo\" id=\"memo\" class=\"trimblank layui-textarea\"></textarea>"
                            + "</div>"
                            + "</div>"

                            + "<div class=\"layui-form-item\">"
                            + "<div class=\"layui-input-block\"> "
                            + data.btnlist
                            + "</div>"
                            + "</div>"
                            + "</form>"

                            + "</div>"
                            + "<div class=\"layui-tab-item\">"

                            + " <script type=\"text/html\" id=\"userTpl\">"
                            + "<span class=\"layer_notice\">"
                            + "{{d.memo}}"
                            + "</span>"
                            + "<\/script>"

                            + "<table id=\"logtab\" lay-filter=\"logtab\"></table>"
                            + "</div>"

                            + "</div>"
                            + "</div>";

                    var open_index = layer.open({
                        title: datatitle,
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['750px', '400px'], //宽高
                        content: content,
                        success: function () {

                            form.verify({
                                score: function (value, item) {
                                    if (parseInt(value) < parseInt($(item).attr('min'))) {
                                        return $(item).attr('placeholder');
                                    }
                                    if (parseInt(value) > parseInt($(item).attr('max'))) {
                                        return $(item).attr('placeholder');
                                    }
                                }
                            });


                            table.render({
                                elem: '#logtab'
                                , width: 720
                                , data: data.log
                                , cellMinWidth: 80
                                , page: false //开启分页
                                , cols: [[//表头                               
                                        {field: 'filedval', title: '操作前', width: 100}
                                        , {field: 'newval', title: '操作后', width: 100}
                                        , {field: 'memo', title: '操作说明', templet: '#userTpl', unresize: true}
                                        , {field: 'score', title: '积分', width: 80}
                                        , {field: 'username', title: '管理员', width: 100}
                                        , {field: 'addtime', title: '操作时间', width: 160}
                                    ]]
                                , done: function () {
//                                    layer.open({
//                                        type: 1,
//                                        shade: false,
//                                        title: false, //不显示标题
//                                        content: $('.layer_notice'), //捕获的元素                                        
//                                    });
                                }
                            });

                            form.render();
                            $(".datepicker").each(function () {
                                var mp = $(this).data();
                                mp.elem = this;
                                laydate.render(mp);
                            });

                            $(".changeopmod").on('mouseover', function () {
                                var effectscore = $(this).attr("data-effectscore");
                                if ($(this).attr('data-mustinput') === "checked") {
                                    $("#memo").attr("lay-verify", 'required');
                                } else {
                                    $("#memo").attr("lay-verify", '');
                                }
                                $("#effectscore").val(effectscore);
                                switch (effectscore) {
                                    case '+':
                                        $("#score").attr("lay-verify", 'number|score');
                                        $("#score_name").text("奖励积分");
                                        $("#score_div").show();
                                        break;
                                    case '-':
                                        $("#score").attr("lay-verify", 'number|score');
                                        $("#score_name").text("扣除积分");
                                        $("#score_div").show();
                                        break;
                                    default:
                                        $("#score").attr("lay-verify", '');
                                        $("#score_name").text("");
                                        $("#score_div").hide();
                                        break;
                                }
                            });

                            /**监听提交*/
                            form.on("submit(prompt_submit)", function (data) {

                                var field = data.field;
                                var token = $("meta[name='" + tokenname + "']").attr('content');
                                field[tokenname] = token;
                                field['usertype'] = busertype;
                                field['sid_id'] = bbsid_id;
                                field['userid'] = bbuserid;
                                field['nameroot'] = nameroot;
                                field['action'] = 'commitPrompt';
                                field['id'] = idval;
                                field['idname'] = idname;
                                field['filedname'] = filedname;
                                field['filedval'] = filedval;
                                field['newval'] = $(this).data("val");
                                field['callback'] = $(this).data("callback");

                                var layerindex = layer.confirm("本操作不可撤销，确定要继续吗？", {
                                    title: $(this).text(),
                                    resize: false,
                                    btn: ['确定', '取消'],
                                    btnAlign: 'c',
                                    anim: 1,
                                    icon: 3
                                }, function () {
                                    layer.close(layerindex);
                                    $.ajax({
                                        url: api_url,
                                        type: 'post',
                                        dataType: 'json',
                                        data: field,
                                        success: function (data, textStatus, request) {
                                            var token = request.getResponseHeader(tokenname);
                                            $("meta[name='" + tokenname + "']").attr('content', token);
                                            $("input[name='" + tokenname + "']").attr('value', token);
                                            layer.close(open_index);
//                                            console.log(data);
                                            if (data.status === 0) {
                                                $(obj).attr('filedval', data.curentval);
                                                $(obj).find('i').html(data.curenttext);
                                                $(obj).removeClass(data.removeclass);
                                                $(obj).addClass(data.curent);
                                            } else {
                                                top.layer.msg(data.msg, {icon: 5});
                                            }
                                        }
                                    });
                                });
                                return false;
                            });
                        }
                    });
                }
            });
        },
        
        ImgCoper: function (obj, enablenewpic,picsize,uploadimg,uploadfilemod,cutfile,usermodel) {
            var cutdata;
            var namehandle = $(obj).data('handle');
            var id = "demofup" + namehandle;
            var btnid = "#valfup" + namehandle;
            var handid = "valfup" + namehandle;           
            var s = $("#"+id).attr('src');
            var s1 = s.split('?');
            var oldimgurl = s1[0]; //保存原图片
            if (oldimgurl === '/Public/images/select.png') {
                oldimgurl = '';
            }
            var curimgurl = oldimgurl;
        
            if(curimgurl==""){
                curimgurl="/Public/images/select.png";
            }
        
            var exts = $(btnid).attr('exts');
            var vouchratio = $(btnid).attr('vouchratio');
            var onlyvouchratio = $(btnid).attr('onlyvouchratio');
            var coustomratio = "";
            var btnrationlist = "";
            if (vouchratio !== '') {
                coustomratio = "<button class=\"layui-btn layui-btn-sm resizecoper\" data-val='" + vouchratio + "'>推荐</button>";
            } else {
                vouchratio = 1;
            }
            if (onlyvouchratio !== '1') {
                btnrationlist = "<div class=\"layui-btn-group\">"
                        + "<button class=\"layui-btn layui-btn-sm resizecoper\" data-val='1.7777777777777777'>16:9</button>"
                        + "<button class=\"layui-btn layui-btn-sm resizecoper\" data-val='1.3333333333333333'>4:3</button>"
                        + "<button class=\"layui-btn layui-btn-sm resizecoper\" data-val='1'>1:1</button>"
                        + "<button class=\"layui-btn layui-btn-sm resizecoper\" data-val='0.6666666666666666'>2:3</button>"
                        + coustomratio
                        + "<button class=\"layui-btn layui-btn-sm resizecoper\" data-val='NaN'>自定义</button>"
                        + "</div>";
            }

            var newpicstr = "";
            if (enablenewpic === 1) {
                newpicstr = " <button type=\"button\" exts=\"" + exts + "\"  class=\"layui-btn layui-btn-sm layui-btn-danger uploadimgcut\" id=\"fup\">替换图片</button>已上传图片，裁剪时会自动保存";
            }

            var layerindex = layer.open({
                title: '图片编辑',
                type: 1,
                //skin: 'layui-layer-rim', //加上边框
                area: ['560px', '360px'], //宽高
                content: "<div class=\"loadUpImg-con\">"
                        + "<div class=\"loadUpImgCon-img\">"
                        + " <div class=\"avatar-wrapper\"><img src=\"" + curimgurl + "?t=" + Math.random() + "\"></div>"
                        + "<div class=\"loadUpImgConImg-right\">"
                        + "<div class=\"avatar-preview loadUpImgConImg-right1\"></div>"
                        + "<div class=\"avatar-preview loadUpImgConImg-right2\"></div>"
                        + " <div class=\"avatar-preview loadUpImgConImg-right3\"></div>"
                        + " </div>"
                        + "</div>"
                        + "<div class=\"loadUp_Toolsbar\">"
                        + btnrationlist
                        + "<div class=\"layui-btn-group rightbtn\">"
                        + "<a href=\"javascript:;\" class=\"layui-btn layui-btn-sm cutcurimg\">裁剪</a><a href=\"javascript:;\" class=\"layui-btn layui-btn-sm savecurimg\">保存</a>"
                        + newpicstr
                        + "</div>"
                        + "</div>"
                        + "</div>",
                success: function () {
                    startcropper();
                }
            });
            var loadindex;
            upload.render({
                elem: ".uploadimgcut"
                , exts: exts
                , size: picsize
                , url: uploadimg
                , data: {type: uploadfilemod, usermodel: usermodel}
                , before: function () {
                    loadindex = layer.load();
                }
                , done: function (res) {
                    layer.close(loadindex);
                    //如果上传失败
                    if (res.status != 0) {
                        return layer.msg(res.msg);
                    } else {
                        var item = this.item;
                        $('.avatar-wrapper').empty().html("<img id=\"demofupthumbcut\" src=\"" + res.abspic + "\">");
                        curimgurl = res.abspic;
                        startcropper();
                    }
                    //上传成功
                }
                , error: function () {
                    return layer.msg("上传失败")
                }
            });
            $(".cutcurimg").on("click", function () {
                $.ajax({
                    url: cutfile,
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: {"filepath": curimgurl, usermodel: usermodel, x: cutdata.x, y: cutdata.y, width: cutdata.width, height: cutdata.height, rotate: cutdata.rotate},
                    success: function (res) {
                        if (res.status != 0) {
                            return layer.msg(res.msg);
                        } else {
                            var newimgurl = curimgurl + "?t=" + Math.random();
                            $('.avatar-wrapper').empty().html("<img src=\"" + newimgurl + "\">");
                            if (curimgurl === oldimgurl) {
                                $("#" + id).attr('src', newimgurl); //更新图示
                            }
                            startcropper();
                        }
                    }
                });
            });
            $(".savecurimg").on("click", function () {
                var newimgurl = curimgurl + "?t=" + Math.random();
                $("#" + id).attr('src', newimgurl); //更新图示            
                $("[handid=" + handid + "]").attr('imgval', curimgurl);
                var xval = $("#" + handid).val();
                if (typeof xval !== 'undefined') {
                    var newval = xval.replace(oldimgurl, curimgurl);
                    if (newval !== "") {
                        $("#tools-" + namehandle).fadeIn(500);
                    } else {
                        $("#" + id).attr('src', "/Public/images/select.png");
                    }
                    $("#" + handid).val(newval);
                }
                //$("#" + handid).change(function () {});
                if (curimgurl !== oldimgurl) {
                    $(".auto-save").click();
                }
                layer.close(layerindex);
            });
            $('.resizecoper').on("click", function () {
                var ratio = $(this).attr('data-val');
                $('.avatar-wrapper > img').cropper('setAspectRatio', ratio);
            });
            function startcropper() {
                $('.avatar-wrapper > img').cropper({
                    aspectRatio: vouchratio,
                    preview: '.avatar-preview',
                    crop: function (data) {
                        // 出来裁切后的图片数据.
                        cutdata = data;
                    },
                });
            }  
        }
        //删除文件
        , DeleteFile: function (obj, delfile, usermodel) {
            var handle = $(obj).data('handle');
            var imageurl = $("#valfup" + handle).val();
            if (imageurl === "") {
                return;
            }
            var layindex = layer.confirm("删除后将不能恢复，确定要删除吗？"
                    , {
                        title: "删除图片",
                        resize: false,
                        btn: ['确定', '取消'],
                        btnAlign: 'c',
                        anim: 1,
                        icon: 3
                    }
            , function () {
                $.ajax({
                    url: delfile,
                    type: 'post',
                    async: false,
                    data: {filepath: imageurl, usermodel: usermodel},
                    success: function (data) {
                        layer.close(layindex);//              
                        if (data.status !== 0) {
                            top.layer.msg(data.msg, {icon: 5});
                        } else {
                            $("#valfup" + handle).val('');
                            $("#demofup" + handle).attr('src', '/Public/images/select.png');
                            $("#tools-" + handle).fadeOut(500);
                            $(".auto-save").click();
                        }
                    }
                });
            });
        }
        , sortSeting: function (obj, userid, usermodel) {
            var tb = $(obj).data('table');
            var idname = $(obj).data('idname');
            var filedname = $(obj).data('sortfiled');
            var idval = $(obj).attr('name');
            var filedval = $(obj).val();

            if (filedval == $(obj).data('oldvue')) {
                return false;
            }
          
            layer.confirm("确定要修改排序吗?", {
                title: "修改排序",
                resize: false,
                btn: ['确定', '取消'],
                btnAlign: 'c',
                anim: 1,
                icon: 3
            }, function (index) {
                layer.close(index);
            var postdata = {usermodel: usermodel, 'table': tb, 'idname': idname, 'idval': idval, 'filedname': filedname, 'filedval': filedval, 'userid': userid, action: 'sortset'};
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
                    if (data.status !== 0) {
                        CmsCommon.cmsLayErrorMsg(data.msg);
                    }
                }
            });

            }, function (index) {
                obj.val(obj.attr('oldval'));
                layer.close(index);
                return false;
            });
        }
  
        //Ajax
        ,json: function (url, data, success, options) {
            var that = this, type = typeof data === 'function';

            if (type) {
                options = success
                success = data;
                data = {};
            }

            options = options || {};

            return $.ajax({
                type: options.type || 'post',
                dataType: options.dataType || 'json',
                data: data,
                url: url,
                success: function (res) {
                    if (res.status === 0) {
                        success && success(res);
                    } else {
                        layer.msg(res.msg || res.code, {shift: 6});
                        options.error && options.error();
                    }
                }, error: function (e) {
                    layer.msg('请求异常，请重试', {shift: 6});
                    options.error && options.error(e);
                }
            });
        }

        //计算字符长度
        , charLen: function (val) {
            var arr = val.split(''), len = 0;
            for (var i = 0; i < val.length; i++) {
                arr[i].charCodeAt(0) < 299 ? len++ : len += 2;
            }
            return len;
        }  
        , escape: function (html) {
            return String(html || '').replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;')
                    .replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
        }

        //内容转义
        , content: function (content) {
            //支持的html标签
            var html = function (end) {
                return new RegExp('\\n*\\[' + (end || '') + '(pre|hr|div|span|p|table|thead|th|tbody|tr|td|ul|li|ol|li|dl|dt|dd|h2|h3|h4|h5)([\\s\\S]*?)\\]\\n*', 'g');
            };
            content = fly.escape(content || '') //XSS
                    .replace(/img\[([^\s]+?)\]/g, function (img) {  //转义图片
                        return '<img src="' + img.replace(/(^img\[)|(\]$)/g, '') + '">';
                    }).replace(/@(\S+)(\s+?|$)/g, '@<a href="javascript:;" class="fly-aite">$1</a>$2') //转义@
                    .replace(/face\[([^\s\[\]]+?)\]/g, function (face) {  //转义表情
                        var alt = face.replace(/^face/g, '');
                        return '<img alt="' + alt + '" title="' + alt + '" src="' + fly.faces[alt] + '">';
                    }).replace(/a\([\s\S]+?\)\[[\s\S]*?\]/g, function (str) { //转义链接
                var href = (str.match(/a\(([\s\S]+?)\)\[/) || [])[1];
                var text = (str.match(/\)\[([\s\S]*?)\]/) || [])[1];
                if (!href)
                    return str;
                var rel = /^(http(s)*:\/\/)\b(?!(\w+\.)*(sentsin.com|layui.com))\b/.test(href.replace(/\s/g, ''));
                return '<a href="' + href + '" target="_blank"' + (rel ? ' rel="nofollow"' : '') + '>' + (text || href) + '</a>';
            }).replace(html(), '\<$1 $2\>').replace(html('/'), '\</$1\>') //转移HTML代码
                    .replace(/\n/g, '<br>') //转义换行   
            return content;
        }

        //新消息通知
        , newmsg: function () {
            var elemUser = $('.fly-nav-user');
            if (layui.cache.user.uid !== -1 && elemUser[0]) {
                fly.json('/message/nums/', {
                    _: new Date().getTime()
                }, function (res) {
                    if (res.status === 0 && res.count > 0) {
                        var msg = $('<a class="fly-nav-msg" href="javascript:;">' + res.count + '</a>');
                        elemUser.append(msg);
                        msg.on('click', function () {
                            fly.json('/message/read', {}, function (res) {
                                if (res.status === 0) {
                                    location.href = '/user/message/';
                                }
                            });
                        });
                        layer.tips('你有 ' + res.count + ' 条未读消息', msg, {
                            tips: 3
                            , tipsMore: true
                            , fixed: true
                        });
                        msg.on('mouseenter', function () {
                            layer.closeAll('tips');
                        })
                    }
                });
            }
            return arguments.callee;
        }

    };

    var bhlayerindex = 0;
    $(".layui-table-cell-over").on("click", function () {
        layer.close(bhlayerindex);
        var content = $(this).html();
        content = '<div class="notice-content layer_notice layui-layer-wrap"  style="display: block;">' + content + '</div>';
        bhlayerindex = layer.open({
            type: 1,
            shade: false,
            title: false, //不显示标题
            content: content,
        });
    });

    /*修改属性*/
    $("body").on("click", ".changeattr", function () {
        var data = $(this).data();
        var obj = $(this);
        $.ajax({
            url: changearrurl,
            type: 'post',
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.status == 0) {
                    $(obj).data('filedval', result.curentval);
//                    console.log(data);
                    if (result.curentval == 1) {
                        obj.removeClass(data.class0);
                        obj.addClass(data.class1);
                        obj.find('i').html(data.key1);
                    } else {
                        obj.removeClass(data.class1);
                        obj.addClass(data.class0);
                        obj.find('i').html(data.key0);
                    }
                } else {
                    CmsCommon.cmsLayErrorMsg(result.msg);
                }
            }
        });
    });

    /*属性轮换*/
    $("body").on("click", ".rotationattr", function () {
        var data = $(this).data();
        var obj = $(this);
        $.ajax({
            url: rotationattr,
            type: 'post',
            dataType: 'json',
            data: data,
            success: function (result) {
                if (result.status == 0) {
                    obj.data('filedval', result.curentval);
                    obj.find('i').html(result.curenttext);
                    obj.removeClass(result.removeclass);
                    obj.addClass(result.curent.tagclass);
                } else {
                    CmsCommon.cmsLayErrorMsg(result.msg);
                }
            }
        });
    });

    /**用户失效*/
    $("body").on("click", ".tree_del", function () {
        var dataurl = $(this).data("url");
        var datatitle = $(this).data("title");
        var userStatus = $(this).data("status");
        var message = $(this).data("message");
        var param = {userId: userStatus};
        CmsCommon.ajaxCmsDelete(datatitle, message, dataurl, param, tokenname, $(this));
    });

    /*属性管理*/
    $("body").on("click", ".prompt", function () {
        var handl = $(this).attr("handle");
        var obj = $("#" + handl);
        CmsCommon.attrPrompt(obj, tokenname, api_url, sid_id, manageid, 1);
    });

    /**
     * 自动刷新开关
     */
    form.on('switch', function () {
        var jumpandflash = this.checked ? true : false;
        $(this).attr('atr', jumpandflash);
    });
   
    $(".layui-upload-img").attr('usermodel', usermodel);

   

     /**
     * 联动下拉菜单
     */
    form.on('select(linkage)', function (data) {

        var obj = $(data.elem);
        var cdata = $(obj).data();
        var next = $(obj).attr("datax-next");
        var linkagename = ".linkage-" + $(obj).attr("datax-linkagename");// cdata.linkagename;
        var valuemodel = $(obj).attr("datax-valuemodel");// cdata.valuemodel;
        var delimiter =  $(obj).attr("datax-delimiter");//cdata.delimiter;
        var valuectrl = "#" +  $(obj).attr("datax-valuectrl");//cdata.valuectrl;
        var action = $(obj).attr("datax-action");//cdata.action
        var pramname = $(obj).attr("datax-pramname");//cdata.pramname
        var ctrindex =$(obj).attr("datax-ctrindex");
        var isend = $(obj).attr("datax-isend");
        var fullnameval = "#" + $(obj).attr("datax-linkagename");

        var mp = new Array();
        mp.push({name: 'action', value:action });
        mp.push({name: pramname, value: data.value});
        mp.push({name: tokenname, value: $("meta[name='" + tokenname + "']").attr('content')});
        $.each(cdata,function(name,value){
             mp.push({name: name, value: value});
        });
        
        if (isend !== '1') {
            $.ajax({
                url: api_url,
                type: 'post',
                dataType: 'json',
                data: mp,
                success: function (result, textStatus, request) {
                    var token = request.getResponseHeader(tokenname);
                    $("meta[name='" + tokenname + "']").attr('content', token);
                    $("input[name='" + tokenname + "']").attr('value', token);
                    if (result.status == 0) {
                        $("#" + next).find("option").remove();
                        $(result.list).each(function (index, element) {
                            $("#" + next).append("<option value='" + this.idvalue + "'>" + this.idname + "</option>");
                        });
                        form.render('select');
                    } else {
                        CmsCommon.cmsLayErrorMsg(result.msg);
                    }
                }
            });
        }

        var val = "";
        var fullval = "";
        var select = $(linkagename).find('select');
        $(select).each(function (index, element) {
            if (ctrindex >= $(this).attr("datax-ctrindex")) {
                fullval = fullval + $(this).val();
                if ($(this).attr("datax-isend") !== '1') {
                    fullval = fullval + delimiter;
                }
            }
        });

        switch (valuemodel) {
            case '1':
                val = fullval;
                break;
            case '2':
                val = data.value;
                break;
        }
        $(fullnameval).val(fullval);
        $(valuectrl).val(val);
    });
    
    $(".datepicker").each(function () {  
        var mp = $(this).data();
        mp.elem = this;
        laydate.render(mp);
    });
   
    $('.moyu-editor').each(function () {
        var height =$(this).data("height");
        var tools = {tool: ['strong', 'italic', 'underline', 'del', '|', 'left', 'center', 'right', 'link', 'unlink', 'face', 'image', 'code']
            , uploadImage: {url: uploadimg, type: 'post'}
            ,height: height };
        var index = layedit.build($(this).attr("id"), tools); //建立编辑器
        layeditorindex.push(index);
    });
    
    /**
     * 输入框自动清除空格
     */
    $('.trimblank').blur(function () {
        var xid = $(this).val();
        $(this).val($.trim(xid));
    });
    
    $(".js_submit").on("click",function(){
        var handle=$(this).data("handle");
        $("#"+handle).submit();
    });

    exports('lay_common', CmsCommon)
})