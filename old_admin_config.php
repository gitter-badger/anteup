<?php
require_once("../../class2.php");
if(!getperms("P")){ header("location:".e_BASE."index.php"); exit; }
require_once(e_ADMIN."auth.php");
require_once(e_HANDLER.'userclass_class.php');
include_lan(e_PLUGIN."anteup/languages/".e_LANGUAGE.".php");
require_once(e_PLUGIN."anteup/_class.php");
require_once(e_HANDLER."calendar/calendar_class.php");
$cal = new DHTML_Calendar(true);
$gen = new convert();
	
$pageid = "admin_menu_01";

if(isset($_POST['updatesettings']))
{
	if(!empty($_POST['anteup_due']) && !empty($_POST['anteup_goal']))
	{
		$pref['anteup_currency'] 		= $tp->toDB($_POST['anteup_currency']);
		$pref['anteup_goal'] 			= $tp->toDB($_POST['anteup_goal']);
		$pref['anteup_due'] 			= $tp->toDB($_POST['anteup_due']);
		$pref['anteup_lastdue'] 		= $tp->toDB($_POST['anteup_lastdue']);
		$pref['anteup_showcurrent'] 	= $tp->toDB($_POST['anteup_showcurrent']);
		$pref['anteup_showibalance']	= $tp->toDB($_POST['anteup_showibalance']);
		$pref['anteup_showleft'] 		= $tp->toDB($_POST['anteup_showleft']);
		$pref['anteup_showgoal'] 		= $tp->toDB($_POST['anteup_showgoal']);
		$pref['anteup_showdue'] 		= $tp->toDB($_POST['anteup_showdue']);
		$pref['anteup_showtotal']  		= $tp->toDB($_POST['anteup_showtotal']);
		$pref['anteup_showconfiglink']  = $tp->toDB($_POST['anteup_showconfiglink']);
		$pref['anteup_dformat'] 		= $tp->toDB($_POST['anteup_dformat']);
		$pref['anteup_description'] 	= $tp->toDB($_POST['anteup_description']);
		$pref['anteup_mtitle']   		= $tp->toDB($_POST['anteup_mtitle']);
		$pref['anteup_full']     		= $tp->toDB(str_replace("#","",$_POST['anteup_full']));
		$pref['anteup_empty']    		= $tp->toDB(str_replace("#","",$_POST['anteup_empty']));
		$pref['anteup_border']   		= $tp->toDB(str_replace("#","",$_POST['anteup_border']));
		$pref['anteup_height']   		= $tp->toDB($_POST['anteup_height']);
		$pref['anteup_width']   		= $tp->toDB($_POST['anteup_width']);
		$pref['anteup_showbar']  		= $tp->toDB($_POST['anteup_showbar']);
		$pref['anteup_textbar']  		= $tp->toDB($_POST['anteup_textbar']);
		$pref['pal_button_image']   	= $tp->toDB($_POST['pal_button_image']);
		$pref['pal_business']       	= $tp->toDB($_POST['pal_business']);
		save_prefs();
		$message = ANTELAN_CONFIG_02;
	}
	else
	{
		$message = ANTELAN_CONFIG_03;
	}
}

if(!isset($pref['anteup_description']))
{
   $pref['anteup_description'] = ANTELAN_DONATIONS_09;
}

$_POST['data'] = $tp->toForm($pref['anteup_description']);

if(isset($message)){ $ns->tablerender("", "<div style='text-align:center'><b>".$message."</b></div>"); }

$text = $cal->load_files()."
<script src='".e_PLUGIN."anteup/js/jscolor.js' type='text/javascript'></script>
<div style='text-align:center'>
<form method='post' action='".e_SELF."' id='tracker_form'>";

$currency_dropbox = "<select class='tbox' name='anteup_currency'>";
$sql->db_Select("anteup_currency", "*");
while($row = $sql->db_Fetch())
{
	$currency_dropbox .= "<option value='".$row['id']."'".($row['id'] == $pref['anteup_currency'] ? " selected" : "").">".$row['description']." (".$row['symbol'].")</option>";
}
$currency_dropbox .= "</select>";

$format_dropbox = "<select name='anteup_dformat' class='tbox'>";
foreach(array('long', 'short', 'forum') as $format)
{
	$format_dropbox .= "<option value='".$format."'".($format == $pref['anteup_dformat'] ? " selected" : "").">".$gen->convert_date(time(), $format)." (".$format.")</option>";
}
$format_dropbox .= "</select>";

$donate_icon_div = "<select class='tbox' name='pal_button_image'>";
foreach(glob(e_PLUGIN."anteup/images/icons/*.gif") as $icon)
{
	$icon = str_replace(e_PLUGIN."anteup/images/icons/", "", $icon);
	$donate_icon_div .= "<option value='".$icon."'".($icon == $pref['pal_button_image'] ? " selected" : "").">".$icon."</option>";
}
$donate_icon_div .= "</select>";


$text .= "<input class='button' type='submit' name='updatesettings' value='".ANTELAN_CONFIG_01."' />
<br />
<div onclick='expandit(\"config\");' class='fcaption' style='cursor: pointer;'>".ANTELAN_CONFIG_CAPTION01."</div>
<table style='width:85%; display:none;' class='fborder' id='config'>
	".config_block($currency_dropbox, ANTELAN_CONFIG_G_01, ANTELAN_CONFIG_G_02)."
	".config_block(format_currency("<input class='tbox' type='text' name='anteup_goal' value='".$pref['anteup_goal']."' />", $pref['anteup_currency'], false), ANTELAN_CONFIG_G_03, ANTELAN_CONFIG_G_04)."
	".config_block("<a href='#' id='f-calendar-trigger-1'>".CALENDAR_IMG."</a> <input class='tbox' type='text' id='anteup_due' name='anteup_due' value='".$pref['anteup_due']."' />\n<script type='text/javascript'>Calendar.setup({'ifFormat':'%m/%d/%Y','daFormat':'%m/%d/%Y','inputField':'anteup_due','button':'f-calendar-trigger-1'});</script>", ANTELAN_CONFIG_G_05, ANTELAN_CONFIG_G_06)."
	".config_block($format_dropbox, ANTELAN_CONFIG_G_07, ANTELAN_CONFIG_G_08)."
	".config_block("<textarea class='tbox' style='width:200px; height:140px' name='anteup_description'>".(strstr($tp->post_toForm($pref['anteup_description']), "[img]http") ? $tp->post_toForm($pref['anteup_description']) : str_replace("[img]../", "[img]", $tp->post_toForm($pref['anteup_description'])))."</textarea>", ANTELAN_CONFIG_G_09, ANTELAN_CONFIG_G_10)."
</table>
<br />
<div onclick='expandit(\"showhide\");' class='fcaption' style='cursor: pointer;'>".ANTELAN_CONFIG_CAPTION02."</div>
<table style='width:85%; display:none;' class='fborder' id='showhide'>
	".config_block("<input class='tbox' type='checkbox' name='anteup_showibalance'".($pref['anteup_showibalance'] ? " checked" : "").">", ANTELAN_CONFIG_I_01, ANTELAN_CONFIG_I_02)."
	".config_block("<input class='tbox' type='checkbox' name='anteup_showcurrent'".($pref['anteup_showcurrent'] ? " checked" : "").">", ANTELAN_CONFIG_I_03, ANTELAN_CONFIG_I_04)."
	".config_block("<input class='tbox' type='checkbox' name='anteup_showtotal'".($pref['anteup_showtotal'] ? " checked" : "").">", ANTELAN_CONFIG_I_05, ANTELAN_CONFIG_I_06)."
	".config_block("<input class='tbox' type='checkbox' name='anteup_showgoal'".($pref['anteup_showgoal'] ? " checked" : "").">", ANTELAN_CONFIG_I_07, ANTELAN_CONFIG_I_08)."
	".config_block("<input class='tbox' type='checkbox' name='anteup_showdue'".($pref['anteup_showdue'] ? " checked" : "").">", ANTELAN_CONFIG_I_09, ANTELAN_CONFIG_I_10)."
	".config_block("<input class='tbox' type='checkbox' name='anteup_showleft'".($pref['anteup_showleft'] ? " checked" : "").">", ANTELAN_CONFIG_I_11, ANTELAN_CONFIG_I_12)."
	".config_block("<input class='tbox' type='checkbox' name='anteup_showconfiglink'".($pref['anteup_showconfiglink'] ? " checked" : "").">", ANTELAN_CONFIG_I_13, ANTELAN_CONFIG_I_14)."
</table>
<br />
<div onclick='expandit(\"menu\");' class='fcaption' style='cursor: pointer;'>".ANTELAN_CONFIG_CAPTION03."</div>
<table style='width:85%; display:none;' class='fborder' id='menu'>
	".config_block("<input class='tbox' type='text' name='anteup_mtitle' value='".$pref['anteup_mtitle']."'>", ANTELAN_CONFIG_M_01, ANTELAN_CONFIG_M_02)."
	".config_block("<input class='tbox' type='checkbox' name='anteup_showbar'".($pref['anteup_showbar'] ? " checked" : "").">", ANTELAN_CONFIG_M_03, ANTELAN_CONFIG_M_04)."
	".config_block("<textarea class='tbox' style='width:200px; height:140px' name='anteup_textbar'>".$pref['anteup_textbar']."</textarea>", ANTELAN_CONFIG_M_05, ANTELAN_CONFIG_M_06)."
	".config_block("#<input class='tbox jscolor' type='text' name='anteup_full' value='".$pref['anteup_full']."' />", ANTELAN_CONFIG_M_07, ANTELAN_CONFIG_M_08)."
	".config_block("#<input class='tbox jscolor' type='text' name='anteup_empty' value='".$pref['anteup_empty']."' />", ANTELAN_CONFIG_M_09, ANTELAN_CONFIG_M_10)."
	".config_block("#<input class='tbox jscolor' type='text' name='anteup_border' value='".$pref['anteup_border']."' />", ANTELAN_CONFIG_M_11, ANTELAN_CONFIG_M_12)."
	".config_block("<input class='tbox' type='text' name='anteup_height' value='".$pref['anteup_height']."' />", ANTELAN_CONFIG_M_13, ANTELAN_CONFIG_M_14)."
	".config_block("<input class='tbox' type='text' name='anteup_width' value='".$pref['anteup_width']."' />", ANTELAN_CONFIG_M_15, ANTELAN_CONFIG_M_16)."
</table>
<br />
<div onclick='expandit(\"paypal\");' class='fcaption' style='cursor: pointer;'>".ANTELAN_CONFIG_CAPTION04."</div>
<table style='width:85%; display:none;' class='fborder' id='paypal'>
<tr>
<td class='forumheader' colspan='2'>".ANTELAN_CONFIG_P_C_01."</td>
</tr>
	".config_block($donate_icon_div , ANTELAN_CONFIG_P_01, ANTELAN_CONFIG_P_02)."
	".config_block("<input class='tbox' type='text' name='pal_business' value='".$pref['pal_business']."' />", ANTELAN_CONFIG_P_03, ANTELAN_CONFIG_P_04)."
</table>
<input class='button' type='submit' name='updatesettings' value='".ANTELAN_CONFIG_01."' />
<input type='hidden' value='".$pref['anteup_due']." name='anteup_lastdue' />
</form>
</div>
";

$ns->tablerender(ANTELAN_CONFIG_CAPTION00, $text);
require_once(e_ADMIN."footer.php");
?>