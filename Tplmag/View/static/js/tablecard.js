UE.Editor.prototype._bkGetActionUrl = UE.Editor.prototype.getActionUrl;
UE.Editor.prototype.getActionUrl = function (action) {
    switch (action) {
        case 'uploadimage':
            return uploadimage;
            break;
        case 'uploadscrawl':
            return uploadscrawl;
            break;
        case 'uploadvideo':
            return uploadvideo;
            break;
        case 'uploadfile':
            return edituploadfile;
            break;
        case 'catchimage':
            return catchimage;
            break;
        case 'listimage':
            return listimage;
            break;
        default:
            return this._bkGetActionUrl.call(this, action);
    }
}


$(function () {
    var elist = editorlist.split(',');
    for (i = 0; i < elist.length; i++) {
        if (elist[i].length > 0) {
            createditor(elist[i]);
        }
    }
});
function createditor(contentname) {
    var ue = UE.getEditor('post-' + contentname, {
        toolbars: [['fullscreen', 'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'formatmatch', 'autotypeset', 'blockquote', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
               // 'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                'directionalityltr', 'directionalityrtl', '|',
                //'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
                'touppercase', 'tolowercase', '|', 'link', 'unlink', 'anchor', '|',
                'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                'insertimage', 'scrawl', 'attachment', '|',
                'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
             //   'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
                'searchreplace', 'drafts',
            'removeformat', 'pasteplain', 'indent', ]
        ],
        initialFrameWidth: '100%',
        initialFrameHeight: 300,
        zIndex: 100
    });
}

layui.config({
    base: baselayui
}).use(['form', 'layedit',  'element', 'lay_common', 'jquery'], function () {
    var $ = layui.jquery
            , form = layui.form
            , element = layui.element
            , layer = layui.layer           
            , layedit = layui.layedit
            ,common = layui.lay_common
       

    
    form.on("submit(submit1)", function (data) {
        common.ajaxSubmit(this,data,self_url,tokenname);
    });

    /*上传 编辑图片*/
    $('.select-img').click(function () {
        common.ImgCoper(this, 1,picsize,uploadimg,uploadfilemod,cutfile,'manage')
    });
    
    /*删除图片*/
    $('.del-image').click(function () {
        common.DeleteFile(this, delfile,'manage');
    });
    
     $(".layui-upload-photopanle").on('click', function () {
        $(this).attr("usermodel", "manage");
        common.imgFlow($(this), uploadimg, delfile, sid_id, userid,cutfile);
    });

    $('.trimblank').blur(function () {
        var xid = $(this).val();
        $(this).val($.trim(xid));
    });

     var tipindex;
    $(".tips").hover(function () {
        var content = $(this).attr("tips");
        if(content=='') return;
        var that = this;
        tipindex= layer.tips(content, that, {
             tips: [1, '#3595CC'],
        });
    }, function () {
        layer.close(tipindex);
    });
    
    form.on('switch(jumpandflash)', function (data) {
         var jumpandflash = data.elem.checked ? true : false;
        $(this).attr('atr',jumpandflash);
        $.ajax({
            url: jumpandflashurl,
            type: 'post',
            dataType: 'json',
            data: {flash:data.elem.checked},
            success: function (data) {                
            }});
    });
    
    $(".form-render").on('click', function () {        
         form.render(); //刷新select选择框渲染
        layer.msg('重新渲染完毕');
    });

    //触发事件
    var active = {
        tabAdd: function () {
            //新增一个Tab项
            element.tabAdd('demo', {
                title: '新选项' + (Math.random() * 1000 | 0) //用于演示
                , content: '内容' + (Math.random() * 1000 | 0)
            })
        }
        , tabDelete: function () {
            //删除指定Tab项
            element.tabDelete('demo', 2); //删除第3项（注意序号是从0开始计算）
        }
        , tabChange: function () {
            //切换到指定Tab项
            element.tabChange('demo', 1); //切换到第2项（注意序号是从0开始计算）
        }
    };

    $('.site-demo-active').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

});

/**
 * 自动设置作者
 * @returns {undefined}
 */
function setposter(obj) {
    var poster = "";
    $(".autocomplete").each(function () {
        var p = $(this).val();
        if (p !== '') {
            poster += p + ",";
        }
    });
    poster = poster.substring(0, poster.length - 1);
    $("input[name^='poster']").val(poster);

    $("input[name^='name']").val(obj);
}