<?php
/* 
* AnteUp - A Donation Tracking Plugin for e107
*
* Copyright (C) 2012-2015 Patrick Weaver (http://trickmod.com/)
* For additional information refer to the README.mkd file.
*
*/
require_once("../../class2.php");
require_once(HEADERF);
require_once(e_PLUGIN."anteup/_class.php");
e107::lan('anteup');

$pref = e107::pref('anteup');

if(!empty($pref['anteup_paypal']) || $pref['anteup_paypal'] != "youremail@email.com")
{
	$tp	= e107::getParser();
	$sc	= e107::getScBatch('anteup', true);
	$template = e107::getTemplate('anteup');

	$text = "<form action='https://www.paypal.com/cgi-bin/webscr' id='paypal_donate_form' method='post'>
	<input type='hidden' name='cmd' value='_xclick' />
	<input type='hidden' name='business' value='".$pref['anteup_paypal']."' id='paypal_donate_email' />
	<input type='hidden' name='notify_url' value='".ANTEUP_ABS."ipn.php' />
	<input type='hidden' name='return' value='".ANTEUP_ABS."return.php?thanks' />
	<input type='hidden' name='cancel_return' value='".ANTEUP_ABS."return.php?cancel' />
	<input type='hidden' name='cancel_return' value='".ANTEUP_ABS."return.php?cancel' />";

	$text .= $tp->parseTemplate($template['donate'], false, $sc);
	$text .= "</form>";
}
else
{
	$text = "Unable to accept donations because there is no PayPal address setup.";
}

$ns->tablerender(ANTELAN_DONATE_CAPTION00, $tp->toHTML($text, true));
require_once(FOOTERF);
?>
