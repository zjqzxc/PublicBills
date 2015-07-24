<?php
$func=$_REQUEST['func'];

switch ($func)
{
case "gethtmluserlist":
	$txt='
        <div class="ui-block-a">
          <label for="red">赵宏</label>
          <input type="checkbox" name="favcolor" id="red" value="red"  checked="checked" >
        </div>
        <div class="ui-block-b">
          <label for="green">郭鑫鑫</label>
          <input type="checkbox" name="favcolor" id="green" value="green">
        </div>
        <div class="ui-block-c">
          <label for="blue">张嘉琦</label>
          <input type="checkbox" name="favcolor" id="blue" value="blue">
        </div>
        <div class="ui-block-a">
          <label for="red1">赵宏2</label>
          <input type="checkbox" name="favcolor1" id="red1" value="red1">
        </div>
	';
	break;
case  'addoneperson':
	$txt='
	  <div class="ui-block-a">
          <select name="person[]" id="person">
            <option value="mon">鑫鑫</option>
            <option value="tu1e" selected>赵宏</option>
            <option value="wed">张嘉琦</option>
          </select>
        </div>
        <div class="ui-block-b">
          <input type="text" name="paid[]" value="RestOfAll" onfocus="if (value ==\'RestOfAll\'){value =\'\'}" onblur="if (value ==\'\' || isNaN(value)){value=\'RestOfAll\'}">
        </div>
        ';
	break;
case 'appendrecord':
  $txt='
<form id="callAjaxForm"> 
      <select name="title" id="title" data-native-menu="false">
        <option value="mon">事件1</option>
        <option value="tu1e">事件2</option>
        <option value="wed">事件3</option>
      </select>
      
      <label for="money" class="ui-hidden-accessible">Money</label>
      <input type="text" name="money" id="money" placeholder="一共多少钱呀">      
      <label for="date">
        <input id="btn_dayminus" type="button" data-inline="true" value="昨天的事" data-mini="true" onclick="dayminus()">
        <input id="btn_hoursminus" type="button" data-inline="true" value="一小时前" data-mini="true" onclick="hoursminus()">
      </label>
      <div class="ui-grid-a">
        <div class="ui-block-a">
            <input type="date" name="date" id="date">
        </div>
        <div class="ui-block-b">
            <input type="time" name="time" id="time">
        </div>
      </div>
      <input type="button" onclick="submit0()" id="submit" data-inline="false" value="提交">

    </form>
  '  ;
  break;
default:
	$txt='Unknown Function！';
}
echo $txt;

?>