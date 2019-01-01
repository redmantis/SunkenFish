/* 
 * UTF-8  语音合成
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2018-1-3 14:16:52
 * @Modify  2018-1-3 14:16:52
 * @CopyRight:  2018 by RDM
 */

$(function () {
    var myPlaylist;
    var textlist;
    var myPalyindex = 0;
    var oldtext = "";
    var curenturl = "http://demo3.fakeruhe.com/";
    var handel;
    var datatype;
    var dataid;
    var hightshow = 1;
    var loopmark = 0;
    var music_controll = "music-player";
    var playpanle = "";

    function GetQueryString() {
        var name = $(handel).text();
        var textlist2 = name.split(/[！。？，\s]/);
        textlist = [];
        for (var i = 0; i < textlist2.length; i++) {
            if (textlist2[i] !== '') {
                textlist.push(textlist2[i]);
            }
        }
    }

    $(".lisen").click(function () {
        if (handel !== $(this).attr("data-handel"))
        {
            myPalyindex = 0;
            handel = $(this).attr("data-handel");
            datatype = $(this).attr("data-type");
            dataid = $(this).attr("data-id");
            hightshow = $(this).attr("data-hight");
            playpanle = $(this).attr("data-playpanle");
            oldtext = $(handel).text();            
        }
        GetQueryString();       
        if (myPalyindex === 0) {
            hightlight(0);
            write_panel_full($(handel));
            console.log(music_controll);
            $("." + music_controll).show();
            get_aip();
        }
    });

    function hightlight(index) {
        if (hightshow === '0') {
            return;
        }
        var text = oldtext;
//        console.log(textlist[index]);
        //定义正则表达式对象  array[i]是关键字   "g"是指全局范围
        var a = new RegExp(textlist[index], "g")
        //对标签文本进行全局替换，包含关键字的位置替换为加红字span对象
        text = text.replace(a, ("<span style='color:#F00'>" + textlist[index] + "</span>"));
        //将替换完的文本对象赋给此对象中A标签对象的html值中
//        console.log(index);
        if (index === textlist.length - 1) {
            $(handel).html(oldtext);
            myPalyindex = 0;
        } else {
            $(handel).html(text);
        }

    }

    function get_aip() {
        $.ajax({
            url: '/index/aip.html',
            type: 'post',
            dataType: 'json',
            data: {text: textlist[myPalyindex], path: "voice" + myPalyindex, 'type': datatype, 'id': dataid},
            success: function (data) {
                if (myPalyindex === 0) {
                    var cssSelector = {
                        jPlayer: "#jquery_jplayer",
                        cssSelectorAncestor: "." + music_controll
                    };

                    var options = {
                        swfPath: "/Public/images/Jplayer.swf",
                        volume: 0.5,
                        ended: function () {
                            hightlight(myPlaylist.current);
                            if (myPlaylist.current === textlist.length - 1) {
                                if (loopmark === 0) {
                                    loopmark = 1;
                                } else {
//                                    myPlaylist.play(0);
//                                    myPlaylist.pause();                                     
                                    myPlaylist.current = 0;
                                    $(this).jPlayer("stop");
                                    loopmark = 0;
                                }
                            }
                        },
                        supplied: "mp3",
                        playlistOptions: {
                            autoPlay: true,
                            enableRemoveControls: true
                        },
                    };
                    var playlist = [{
                            title: data.path,
                            artist: data.path,
                            mp3: curenturl + data.path,
                            poster: "/Public/images/nopic.jpg"
                        }];
                    myPlaylist = new jPlayerPlaylist(cssSelector, playlist, options);

//                     $(".jp-play").on('click',function(){
//                          if (myPlaylist.current === textlist.length - 1) {
//                                myPlaylist.current = 0;
//                            }
//                     });
                } else {
                    myPlaylist.playlist.push({
                        title: data.path,
                        artist: data.path,
                        mp3: curenturl + data.path,
                        poster: "/Public/images/nopic.jpg"
                    });
                }
                myPalyindex = myPalyindex + 1;
                if (myPalyindex < textlist.length) {
                    get_aip()
                }
            }
        });
    }
    function write_panel(obj) {
        $("." + music_controll).remove();
        var content = "<div class=\"" + music_controll + "\" style=\"display: none;\">"
                + "<div class=\"jp-playlist\" style=\" display: none;\"><ul><li></li></ul></div>"
                + "<a href=\"javascript:;\" class=\"icon-play jp-play\" title=\"play\"></a>"
                + "<a href=\"javascript:;\" class=\"icon-pause jp-pause\" title=\"pause\"></a>"
                + "<div id=\"jquery_jplayer\" class=\"jp-jplayer\"></div>"
                + "</div>";
        if (typeof (playpanle) !== "undefined") {
            $(playpanle).html(content);
        } else {
            $(obj).after(content);
        }
    }


    function write_panel_full(obj) {
        $(".music-player").remove();
        var content = "<div class=\"music-player\" style=\"display: none;\">"
                + "<div class=\"info\" style=\" display: none;\">"
                + "<div class=\"left\">"
                + "<a href=\"javascript:;\" class=\"icon-shuffle\"></a>"
                + "<a href=\"javascript:;\" class=\"icon-heart\"></a>"
                + "</div>"
                + "<div class=\"center\">"
                + "<div class=\"jp-playlist\"><ul><li></li></ul></div>"
                + "</div>"
                + "<div class=\"right\">"
                + "<a href=\"javascript:;\" class=\"icon-repeat\"></a>"
                + "<a href=\"javascript:;\" class=\"icon-share\"></a>"
                + "</div>"
                + "<div class=\"progress jp-seek-bar\"><span class=\"jp-play-bar\" style=\"width: 0%\"></span></div>"
                + "</div>"
                + "<div class=\"controls\">"
                + "<div class=\"current jp-current-time\" style=\" display: none;\">00:00</div>"
                + "<div class=\"play-controls\">"
                + "<a href=\"javascript:;\" class=\"icon-previous jp-previous\" title=\"previous\"></a>"
                + "<a href=\"javascript:;\" class=\"icon-play jp-play\" title=\"play\"></a>"
                + "<a href=\"javascript:;\" class=\"icon-pause jp-pause\" title=\"pause\"></a>"
                + "<a href=\"javascript:;\" class=\"icon-next jp-next\" title=\"next\"></a>"
                + "</div>"
                + "<div class=\"volume-level jp-volume-bar\">"
                + "<span class=\"jp-volume-bar-value\" style=\"width: 0%\"></span>"
                + "<a href=\"javascript:;\" class=\"icon-volume-up jp-volume-max\" title=\"max volume\"></a>"
                + "<a href=\"javascript:;\" class=\"icon-volume-down jp-mute\" title=\"mute\"></a>"
                + "</div>"
                + "</div>"
                + "<div id=\"jquery_jplayer\" class=\"jp-jplayer\"></div>"
                + "</div>";
//        console.log(obj);
        $(obj).after(content);
    }
});
