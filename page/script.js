$(document).ready(function() {
    $(".reg-window").css("display", "flex")
        .hide();
});

var slide_count=0;
    $.ajax({
        url:"/actions/news",
        type:"post",
        data:"name=count",
        cache:false,
        success:function (result) {
           slide_count=result;
        }
    })


var reg = $(".reg-window.reg");
var reg_log = $(".reg-window.log");
$(".registr").click(function () {
    if(reg.attr("active")!="true")
    reg.css("display", "flex")
        .hide().fadeIn(500).attr("active","true");
});

$(".login").click(function () {
    if(reg_log.attr("active")!="true")
        reg_log.css("display", "flex")
            .hide().fadeIn(500).attr("active","true");
});
// reg.click(function () {
//     if(reg.attr("active")=="true")
//         reg.css("display", "none")
//             .hide().attr("active","false");
// });
$("button[value='Cancel']").click(function () {
  if(reg.attr("active")=="true")
        reg.css("display", "none")
           .hide().attr("active","false");
    if(reg_log.attr("active")=="true")
        reg_log.css("display", "none")
            .hide().attr("active","false");
});

var i=0;
var timer;

$(".p").click(function () {
    i=$(this).attr("id")[1];
   var back = "URL('/resource/darkblue" +i+ ".jpg') center"
    $('.slide').css(
        "background", back
    )
   var slide = "#slide" + i;
    $(".active").removeClass("active");
    $(this).addClass("active");
    $(".active-slide").removeClass("active-slide");
    $(slide).addClass("active-slide");
    clearInterval(timer);
    timer =  setTimeout(autoslider,5000);
});

$("#mob-menu").click(function () {
    $("#mobpages").toggle(300);

});

function autoslider() {
    //alert();
   // console.log(slide_count);
    if(i==slide_count) i=1;
    else i++;
    id= '#p' + i;
    clearInterval(timer);
    $(id).trigger('click');
}
autoslider();

$('#message').hide(0);

function Showmessage(result) {
    var  color=JSON.parse(result).color;
    var  text=JSON.parse(result).text;
   var mess = $('#message');
   if(mess.css('display')=='flex')
   {
       mess.hide(300);
   }
    mess.html(text).css('background',color).show(500).click(
    function () {
        mess.hide(500);
    }
);
}

$("#send-to-mail").click(function () {
    $.ajax({
        url:"/actions/history",
        type:'POST',
        data:"with=" + $("#with").val(),
        success:function (result) {

            Showmessage(result);
        }
    })
})

$("input[name='login']").click(function () {

   $.ajax({
       url:"/actions/login",
       type:"POST",
       data:"name=login&login="+$("input[name='login_l']").val()+"&password="+$("input[name='password_l']").val(),
       success:function (result) {
        if(!result) window.location.replace("/");
        else {
            Showmessage(result);
        }
       }
   })
})

$("input[name='reg']").click(function () {
    $.ajax({
        url:"/actions/reg",
        type:"POST",
        data:"name=reg&login="+$("input[name='login_but']").val()+"&password="+$("input[name='password']").val()
        +"&mail="+$("input[name='mail']").val()+"&TOU="+$("select[name='TOU']").val()+"&captcha="+$("input[name='captcha']").val(),
        success:function (result) {
            if(!result) window.location.replace("/");
            else {
                Showmessage(result);
            }
        }
    })
})

var arrow = $("#mob-task img");
arrow.click(function () {
    $("#mob-tasks").toggle(500);
    if(arrow.attr("src")=="/resource/arrow.png")
    arrow.attr("src","/resource/arrow2.png");
    else arrow.attr("src","/resource/arrow.png");

});

$("input[name='check_login']").click(function () {
    $.ajax({
        url:"/actions/check",
        type:"post",
        data:"name=check&login="+$("input[name='login_manege']").val(),
        cache:false,
        success:function (result) {
            Showmessage(result);
    }
    });
})

$("input[name='connect_to_team']").click(function () {
    $.ajax({
        url:"/actions/connect",
        type:"post",
        data:"name=connect&login="+$("input[name='login_manege']").val(),
        cache:false,
        success:function (result)
        {
            Showmessage(result);
        }
    })
})

$("#accept").click(function () {
    var but = $(this);
    var user = $(this).attr("name");
    $.ajax({
        url:"/actions/join",
        type:"post",
        data:"name=join&type=accept&user="+user,
        cache:false,
        success:function (result) {
           // alert(result)
            $(but).parent().fadeOut(350);
            Showmessage(result);
        }
    })
})

$("#cancel").click(function () {
    var but = $(this);
    var user = $(this).attr("name");
    $.ajax({
        url:"/actions/join",
        type:"post",
        data:"name=join&type=cancel&user="+user,
        cache:false,
        success:function (result) {
            // alert(result)
            $(but).parent().fadeOut(350);
            Showmessage(result);
        }
    })
})

$(".kick").click(function () {
    var but = $(this);
    var user = $(this).attr("name");
    $.ajax({
        url:"/actions/kick",
        type:"post",
        data:"name=kick&user="+user,
        cache:false,
        success:function (result) {
            // alert(result)
            $(but).parent().fadeOut(350);
            Showmessage(result);
        }
    })
})

$("button[name='change_text']").click(function () {
    $.ajax({
        url:"/actions/change_text",
        type:"post",
        data:"name=change_text&text="+$("textarea[name='main_text']").val(),
        cache:false,
        success:function (result) {
            // alert(result)
            Showmessage(result);
        }
    })
})

$(".new_news").click(function () {
    var but = $(this);
    $.ajax({
        url:"/actions/news",
        type:"post",
        data:"type=new&name="+but.attr('name')+"&title="+$("input[name='title']").val()+
        "&short="+$("textarea[name='short']").val()+
        "&long="+$("textarea[name='long']").val()+
        "&date="+$("input[name='date']").val(),
        cache:false,
        success:function (result) {
           Showmessage(result);
        }
    })
})

$(".del_news").click(function () {
   var but = $(this);
  // alert(but.attr('name'));
    $.ajax({
        url:"/actions/news",
        type:"post",
        data:"name=delete&id="+but.attr('name'),
        cache:false,
        success:function (result) {
            // alert(result)
           Showmessage(result);
           if(but.hasClass('page-news'))
           {
               but.parent().parent().parent().parent().fadeOut(350);
           }
           else but.parent().fadeOut(350);
        }
    })
})

$(".soc_link").click(function () {
    var but = $(this);
   var in_name = but.attr('name');
   var val="";
   // alert(but.attr('name'));

    if(!$("input[name="+in_name+"]").val())
    {
        val = 'null';
    }
    else val = $("input[name="+in_name+"]").val();


    $.ajax({
        url:"/actions/soc",
        type:"post",
        data:"name=change&link="+val+"&id="+in_name,
        cache:false,
        success:function (result) {
            // alert(result)
            Showmessage(result);
        }
    })
})

$("input[name='title'].su").keyup(function () {
    var  str =$("input[name='title'].su").val();
    if(str.length>20){
        $("input[name='title'].su").val(str.substring(0,20));
    }
})

$("textarea[name='short'].su").keyup(function () {
  var  str =$("textarea[name='short'].su").val();
    if(str.length>165){
        $("textarea[name='short'].su").val(str.substring(0,164));
    }
})


$("button[name='more']").click(function () {
    var but = $(this);
    $.ajax({
        url:"/actions/more",
        type:"post",
        data:"name="+but.attr("id"),
        cache:false,
        success:function (result) {
           // console.log(result);
           // alert();
           but.parent().before(JSON.parse(result).text);
            if(JSON.parse(result).hide == 'true')
            {
                but.parent().fadeOut(500);
            }

        }
    })
})

$(document).on("click", ".show-all", function(){
   var but = $(this);
   $.ajax({
       url:"/actions/full_mess",
       type:"post",
       data:"name=full_mess&id="+but.attr('id'),
       cache:false,
       success:function (result) {
           var div = but.parent();
               div.text(result);
       }
   })
})

$(".addpoints").click(function () {
    var but = $(this);
    var value;
    var id = but.attr('name');
    if(but.hasClass('mb'))
    {
        value=$("input[name='"+id+"'].mb").val()
    }
    else value = $("input[name='"+id+"']").val();

    $.ajax({
        url:"/actions/addpoints",
        type:"post",
        data:"name=add&idreport="+id+"&mark="+value,
        cache:false,
        success:function (result) {
            Showmessage(result);
            if(JSON.parse(result).reload)
            {
                setTimeout(reload,1000);
            }
        }
    })
})

function reload() {
    location.reload();
}

$("button[name='random_pass']").click(function () {
    var all = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    var text="";
    for(i=0;i<8;i++)
    {
        text = text + all[parseInt(all.length*Math.random())]
    }
    $("input[name='newpass']").val(text);
});

$("button[name='changepass']").click(function () {
    $.ajax({
        url:"/actions/changepass",
        type:"post",
        data:"name=change&oldpass="+$("input[name='oldpass']").val()+"&newpass="+$("input[name='newpass']").val(),
        cache:false,
        success:function (result) {
           // console.log(result);
            Showmessage(result);
        }
    })
})