/* 
 * 通用收藏、点击、点赞、评论数据统计功能
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-11-17 11:21:30
 * @Modify  2017-11-17 11:21:30
 * @CopyRight:  2017 by RDM
 */


function cleartoken(obj){//表
    $(obj).find("input[name='" + tokenname + "']").remove();    
    return true;
}

layui.config({
    base: baselayui,
}).use(['form', 'flow', 'jquery'], function () {
    var $ = layui.jquery
            , form = layui.form
            , layer = layui.layer;


    layer.ready(function () {
        $('.collect_handle').each(function () {
            var collectid = $(this).attr('collectid');
            var collecttable = $(this).attr('collecttable');
            var collecttype = $(this).attr('collecttype');
            var showmod = $(this).attr('showmod');
            var aboutctr = $(this).attr("about-ctr");
            var aboutclass = $(this).attr("about-class");
            var obj = $(this);
            var postdata = {usermodel: 'pcusers', action: 'collect', collectid: collectid, collecttable: collecttable, collecttype: collecttype, showmod: showmod, sid_id: sid_id, userid: userid};
            postdata[tokenname] = $("meta[name='" + tokenname + "']").attr('content');
            $.ajax({
                url: api_url,
                type: 'post',
                dataType: 'json',
                data: postdata,
               success: function(data, textStatus, request)  {
                    var token = request.getResponseHeader(tokenname);
                    $("meta[name='" + tokenname + "']").attr('content', token);
                    $("input[name='" + tokenname + "']").attr('value', token);
                    switch (collecttype) {
                        case '20':
                            $(obj).html(data.favstatus);
                            break;
                        default:
                            if (data.favstatus > 0) {
                                $(obj).html($(obj).attr('tip1'));
                                $(aboutctr).addClass(aboutclass);
                            } else {
                                $(obj).html($(obj).attr('tip0'));
                                $(aboutctr).remove(aboutclass);
                            }
                            break;
                    }
                    $($(obj).attr("showcounthandle")).html(data.favcount);
                }
            });
        });
    });


    /*
     * Exp
     * <span class="collect_handle" collectid="{$model.id}" collecttable="rooms" collecttype="{:CollectType_Favorite}" tip1="取消收藏" tip0="收藏">收藏</span>
     */
    $('.collect_handle').on('click', function () {
        var collectid = $(this).attr('collectid');
        var collecttable = $(this).attr('collecttable');
        var collecttype = $(this).attr('collecttype');
        var aboutctr = $(this).attr("about-ctr");
        var aboutclass = $(this).attr("about-class");
        if (collecttype === '20') {
            return false;
        }
        var obj = $(this);

        var postdata = {usermodel: 'pcusers', action: 'collect', collectid: collectid, collecttable: collecttable, collecttype: collecttype, sid_id: sid_id, userid: userid};
        postdata[tokenname] = $("meta[name='" + tokenname + "']").attr('content');
        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: postdata,
            success: function(data, textStatus, request)  {
                var token = request.getResponseHeader(tokenname);
                $("meta[name='" + tokenname + "']").attr('content', token);
                $("input[name='" + tokenname + "']").attr('value', token);
                if (data.status) {
                    if (data.favstatus > 0) {
                        $(obj).html($(obj).attr('tip1'));
                        $(aboutctr).addClass(aboutclass);
                    } else {
                        $(obj).html($(obj).attr('tip0'));
                        $(aboutctr).removeClass(aboutclass);
                    }
                    $($(obj).attr("showcounthandle")).html(data.favcount);
                } else {
                    layer.msg(data.msg);
                }
            },
            error: function (data) {
                //top.layer.close(index);
            }
        });
    });

    /**
     * 验证码更新
     */
    $(".vcodeImg").click(function () {
        var time = new Date().getTime();
        $(this).attr({"src": verifyURL + "/?" + time});
    });

    /**监听提交*/
    form.on("submit(login)", function (data) {
        var field = data.field;
        var reload = $(this).attr('data-reload');
        var showmsg = $('.auto-save').attr('data-noshowmsg');
        var jumpurl = $(this).attr('lay-jump');

        if (typeof (field.usermodel) === 'undefined') {
            field.usermodel = 'pcusers';
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
    
    get_judgelist(1);    

    /**加载数据**/
    function get_judgelist(page) {           
       var roomid=$("#judge").attr("data-roomid");
        var filter=$("#judge").attr("data-filter");
        $.ajax({
            url: "/index/judglist.html",
            type: 'post',
            data: {roomid: roomid, size: 5, p: page, filter: filter},
            success: function (data) {
                if (data != "") {
                    $("#judge").html(data);
                    $(".ajaxpage li").on("click", function () {
                        var url = $(this).attr('data-url');
                        if (url == "") {
                            console.log(url + '122');
                        } else if (url == '#') {
                            console.log('2222');
                        } else {
                            get_judgelist(url);
                        }
                    });
                }
            }
        });
    }
    
    $(".statistics li").on('click', function () {
        $(".statistics li").removeClass('statistics-active');
        $(this).addClass('statistics-active');
        $("#judge").attr("data-filter", $(this).attr("data-filter"));
        get_judgelist(1);
    });
   
    layer.photos({
        photos: '.newcontent'
        , anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
    });
 
});
