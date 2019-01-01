
/* 多图相册排序 */
function gridlyimg() {
    $('.gridly').gridly({
        gutter: 10, // px
        columns: 6
    });

    $('.brick').on('mouseup', function () {
        var obj = $(this).parent();
        var imglist = getimglist(obj);
        $(obj).parent().find("[id^='valfup']").val(imglist);
        var autopost = $(obj).parent().find("[id^='valfup']").attr('data-autopost');

        if (autopost === '1') {
            $('.auto-save').click();
        }
    });
}

function getimglist(obj) {
    var imglist = "";
    var listobj = new Array();
    $(obj).find('.delimg').each(function () {
        var y = $(this).parent().css('top');
        var x = $(this).parent().css('left');

        x = x.replace(/px$/g, "");
        x = parseInt(x);
        x = x * 90;
        x = (Array(7).join(0) + x).slice(-7);

        y = y.replace(/px$/g, "");
        y = parseInt(y);
        y = y * 90;
        y = (Array(7).join(0) + y).slice(-7);

        var xlier = parseInt(y + "" + x);
        var sobj = {xlier: xlier, imgsrc: $(this).attr('imgval')};
        listobj.push(sobj);
    });
    listobj.sort(sortNumber);
    $(listobj).each(function () {
        imglist += this.imgsrc + ",";
    });
    imglist = imglist.replace(/,$/g, "");
    return imglist;
}

function sortNumber(a, b) {
    return a.xlier - b.xlier;
}

var api_url = "/api/api/index.html";//api接口地址
var tokenname = '__rdmshkjhash__';

layui.config({
    base: baselayui,
}).use(['form', 'jquery'], function () {
    var $ = layui.jquery
            , layer = layui.layer;

    var enablesign = 1;
    layer.ready(function () {
        $('.everydaysign').each(function () {
            var sid_id = $(this).attr('data-sidid');
            var userid = $(this).attr('data-userid');
            var datalx = $(this).attr('data-lx');
            var datacount = $(this).attr('data-count');
            var enablecls = $(this).attr("data-enablecls");
            var obj = $(this).find('i');
            var postdata = {usermodel: 'pcusers', action: 'sign_info', sid_id: sid_id, userid: userid};
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
                    if (data.status) {                        
                        $("." + datacount).html(data.signcount);
                        enablesign = data.enablesign;
                        if (enablesign === 1) {
                            $(obj).attr('class', enablecls);
                            $("." + datalx).html("连续签到:<i class=\"fa fa-heart-o\"></i> <span>" + data.sign + "</span>次");
                        } else {
                            $("." + datalx).html("立刻签到");
                        }
                    }
                }
            });
        });
    });

    $('.everydaysign').on('click', function () {
        if (enablesign === 1) {
            return;
        }
        var sid_id = $(this).attr('data-sidid');
        var userid = $(this).attr('data-userid');
        var datalx = $(this).attr('data-lx');
        var datacount = $(this).attr('data-count');
        var enablecls = $(this).attr("data-enablecls");
        var obj = $(this).find('i');
        var postdata = {usermodel: 'pcusers', action: 'everyday_sign', sid_id: sid_id, userid: userid};
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
                if (data.status) {                
                    $("." + datalx).html("连续签到:<i class=\"fa fa-heart-o\"></i> <span>" + data.sign + "</span>次");
                    $("." + datacount).html(data.signcount);
                    enablesign = 1;
                    $(obj).attr('class', enablecls);
                } else {
                    //layer.msg(data.msg);
                }
            },
            error: function (data) {
                //top.layer.close(index);
            }
        });
    });
});