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
