function showLoader() {
  //显示加载器.for jQuery Mobile 1.2.0
  $.mobile.loading('show', {
    text: '加载中...', //加载器中显示的文字
    textVisible: true, //是否显示文字
    theme: 'b',        //加载器主题样式a-e
    textonly: false,   //是否只显示文字
    html: ""           //要显示的html内容，如图片等
  });
}
function hideLoader(){
  //隐藏加载器
  $.mobile.loading('hide');
} 

function addzero (value, length) {//时间日期前补‘0’
  if (!length) length = 2;          
  value = String(value);        
  for (var i = 0, zeros = ''; i < (length - value.length); i++) {
    zeros += '0';
    }
  return zeros + value;         
};
//日期减一天
function dayminus(){
  var date=$("#date")[0].value;
  var arr=date.split("-");
  date=arr[0]+"-"+arr[1]+"-"+addzero(Number(arr[2])-1);
  $("#date").val(date);
  $("#btn_dayminus").val("还在昨天").button("refresh"); 
}
//时间减去一小时
function hoursminus(){
  var time=$("#time")[0].value;
  var arr=time.split(":");
  time=addzero(Number(arr[0])-1)+":"+arr[1];
  $("#time").val(time);
  $("#btn_hoursminus").val("再早点儿").button("refresh");
}
//addone
function addone(){
  $.get("ajaxfunc.php",{ func: "addoneperson" },function(result){
    $("#person").append(result).trigger('create');
  });
}

function loadnewpage(name){
  $.get("ajaxfunc.php",{ func: name },function(result){
    $("#mainpage").html(result).trigger('create');
  }); 
}

function settlement(){
$.get("postdata.php",{ funcname: "settlement" },function(result){
   if (result==1) loadnewpage('settlement');
   else newalert('发起失败，请稍后再试')
  });
}

function dosettlement(){
$.get("postdata.php",{ funcname: "dosettlement" },function(result){
   newalert(result);
   if (result==1 ) loadnewpage('settlement');
   else if(result==2 ){
     alert('结算全部完成！');
     loadnewpage('settlement');
   }else{
     newalert('好像是出错。。。');
   }
  });
}

function setCookie(c_name,value,expiredays)
{
var exdate=new Date()
exdate.setDate(exdate.getDate()+expiredays)
document.cookie=c_name+ "=" +escape(value)+
((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}
function getCookie(c_name)
{
if (document.cookie.length>0)
  {
  c_start=document.cookie.indexOf(c_name + "=")
  if (c_start!=-1)
    { 
    c_start=c_start + c_name.length+1 
    c_end=document.cookie.indexOf(";",c_start)
    if (c_end==-1) c_end=document.cookie.length
    return unescape(document.cookie.substring(c_start,c_end))
    } 
  }
return ""
}

function newalert(title){
  var popupDialogId =  Date.parse(new Date());
    $('<div data-role="popup" id="' + popupDialogId + '" data-confirmed="no" data-transition="pop" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="min-width:216px;max-width:500px;"> \
                    \
                    <div role="main" class="ui-content">\
                        <h3 class="ui-title" style="color:#fff; text-align:center;margin-bottom:15px">'+title+'</h3>\
                        <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b optionConfirm" data-rel="back" style="background: #1784fd;width: 75%;border-radius: 5px;height: 30px;line-height: 30px;padding: 0;font-size: .9em;margin: 0 0 0 12%;font-weight: 100;">确定</a>\
                    </div>\
                </div>')
    .appendTo($.mobile.pageContainer);
    var popupDialogObj = $('#' + popupDialogId);
    popupDialogObj.trigger('create');
    popupDialogObj.popup({
        afterclose: function (event, ui) {
            popupDialogObj.find(".optionConfirm").first().off('click');
        }
    });
    popupDialogObj.popup('open');
    popupDialogObj.find(".optionConfirm").first().on('click', function () {
        popupDialogObj.attr('data-confirmed', 'yes');
    });
}

function newconfirm(title,func) {
    var popupDialogId = 'popupDialog';
    $('<div data-role="popup" id="' + popupDialogId + '" data-confirmed="no" data-transition="pop" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="min-width:216px;max-width:500px;"> \
                    \
                    <div role="main" class="ui-content">\
                        <h3 class="ui-title" style="color:#fff; text-align:center;margin-bottom:15px">'+title+'</h3>\
                        <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b optionConfirm" data-rel="back" style="background: #1784fd;width: 33%;border-radius: 5px;height: 30px;line-height: 30px;padding: 0;font-size: .9em;margin: 0 0 0 12%;font-weight: 100;">确定</a>\
                        <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b optionCancel" data-rel="back" data-transition="flow" style="background: #DBDBDB;width: 33%;border-radius: 5px;height: 30px;line-height: 30px;padding: 0;font-size: .9em;margin: 0 0 0 5%;font-weight: 100;color: #333;text-shadow: none;">取消</a>\
                    </div>\
                </div>')
    .appendTo($.mobile.pageContainer);
    var popupDialogObj = $('#' + popupDialogId);
    popupDialogObj.trigger('create');
    popupDialogObj.popup({
        afterclose: function (event, ui) {
            popupDialogObj.find(".optionConfirm").first().off('click');
            var isConfirmed = popupDialogObj.attr('data-confirmed') === 'yes' ? true : false;
            $(event.target).remove();
            if (isConfirmed) {
               //这里执行确认需要执行的函数
              func();
            }
        }
    });
    popupDialogObj.popup('open');
    popupDialogObj.find(".optionConfirm").first().on('click', function () {
        popupDialogObj.attr('data-confirmed', 'yes');
    });
}