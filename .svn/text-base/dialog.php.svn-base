<?php
//$myurl = $base_url.'/extensions/'.basename(dirname(__FILE__));
//$mypath = dirname(__FILE__);

?>

<style>
#warning_box{
text-align:center;
color:#FFFFFF;
position:absolute;
border:2px dotted 1f537b;
width:600px;
display:none;
background-color:#1f537b;
-moz-border-radius:10px;
}
#warning_box label{
color:white;
display:block;
}
</style>
<div id="warning_box">
<img src="<?=$ext_info['url']?>/img/bell.png" />
<div id="divusername" style="text-align:center"></div>
<p onclick="$('#warning_box').hide('slow')" style="float:right; cursor:pointer">X</p>

<form id="frmwarning" action="<?php echo $ext_info['url'].'/warning.php';?>" method="get" style="padding:20px;" >
<label><?php echo $lang_warning['Warning reason']?><input type="text" name="w_reason" id="w_reason" size="50" maxlength="255" /></label>
<label><?php echo $lang_warning['Ban after warning']?><input type="checkbox" name="w_ban" id="w_ban" value="1"  /></label>
<input type="hidden" name="w_uid" id="w_uid" value="" />
<input type="hidden" name="w_username" id="w_username" value="" />
<input type="hidden" name="w_tid" id="w_tid" value="" />
<input type="hidden" name="w_pid" id="w_pid" value="" />
<br /><input type="submit" id="w_submit" name="submit" value="<?php echo $lang_common['Submit']?>" />
</form>
</div>
