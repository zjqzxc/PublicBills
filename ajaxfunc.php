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
default:
	$txt='Unknown Function！';
}
echo $txt;

?>