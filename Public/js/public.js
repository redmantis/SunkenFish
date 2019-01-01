$().ready(function () {
    
    /*新闻点击*/
    if ($("#hitcounter").length > 0) {
        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: {action: 'newshit', cid: cid, r: Math.random()},
            success: function (data) {
                if (data.status > 0) {
                    $("#hitcounter").text(data.hits);
                } else {

                }
            },
            error: function (data) {
            }
        });
    }
    
    /*产品点击*/
    if ($("#goodshitcounter").length > 0) {
        $.ajax({
            url: api_url,
            type: 'post',
            dataType: 'json',
            data: {action: 'goodshit', cid: cid, r: Math.random()},
            success: function (data) {
                if (data.status > 0) {
                    $("#goodshitcounter").text(data.click_count);
                } else {

                }
            },
            error: function (data) {
            }
        });
    }
    
    $('#keypress').on('keypress', function (event) {
        if (event.keyCode == 13)
        {
            return false;
        }
    });
    
//    $('.nav-search').on('click',function(){
//        $("#searchform").submit()
//    });
});