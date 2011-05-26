<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! Für weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com

if (isset($_POST['save']) && $ACTION == $_POST['save'])
{
	$site_url = $ENV->_post('site_url');

	while (mb_substr($site_url, -1, 1, 'UTF-8') == '/') #remove ending slashes
	{
		$site_url = mb_substr($site_url, 0, -1, 'UTF-8');
	}

	$site_email = $ENV->_post('site_email');
	$site_email = emailcheck($site_email, 0) ? $site_email : '';

	$time_offset = $ENV->_post('time_offset');
	$time_offset = is_numeric($time_offset) ? $time_offset : 0;

	#set the save values
	$save_values['site_name'] = $ENV->_post('site_name');
	$save_values['site_slogan'] = $ENV->_post('site_slogan');
	$save_values['site_title'] = $ENV->_post('site_title');
	$save_values['site_title_prefix'] = $ENV->_post('site_title_prefix');
	$save_values['site_title_suffix'] = $ENV->_post('site_title_suffix');

	$save_values['site_url'] = $site_url;
	$save_values['site_urls'] = $ENV->_post('site_urls');
	$save_values['site_email'] = $site_email;
	$save_values['site_copy'] = $ENV->_post('site_copy');
	$save_values['site_closure'] = $ENV->_post('site_closure');

	$save_values['cookie_domain'] = $ENV->_post('cookie_domain');
	$save_values['cookie_path'] = $ENV->_post('cookie_path');

	$save_values['time_gmtzone'] = $ENV->_post('time_gmtzone');
	$save_values['time_offset'] = $time_offset;
	$save_values['time_summertime'] = $ENV->_post_ibool('time_summertime');

	$save_values['version_check'] = $ENV->_post_ibool('version_check');
	$save_values['site_adview'] = $ENV->_post_ibool('site_adview');
	$save_values['site_style'] = $ENV->_post_id('site_style');

	$save_values['templatename'] = $ENV->_post('templatename');
	$save_values['captcha'] = $ENV->_post_ibool('captcha');

	$save_values['text_imgresize'] = $ENV->_post_ibool('text_imgresize');
	$save_values['user_imgresize'] = $ENV->_post_ibool('user_imgresize');
	$save_values['image_resize_width'] = $ENV->_post_id('image_resize_width');
	$save_values['image_resize_height'] = $ENV->_post_id('image_resize_height');

	$save_values['user_textwrap'] = $ENV->_post_id('user_textwrap');
	$save_values['session_adminautolog'] = $ENV->_post_ibool('session_adminautolog');

	return;
}

$site_style_list = '';

$getstyles = $SQL->query("SELECT style_id, style_name FROM " . pkSQLTAB_STYLE . " ORDER BY style_name ASC");
while ($styles = $SQL->fetch_assoc($getstyles))
{
	$site_style_list .= '<option value="' . $styles['style_id'] . '"';

	if ($styles['style_id'] == $config['site_style'])
	{
		$site_style_list .= $_selected;
	}

	$site_style_list .= '>' . pkEntities($styles['style_name']) . '</option>';
}

// timezones
$info_m12 = $info_m11 = $info_m10 = $info_m9 = $info_m8 = $info_m7 = $info_m6 = $info_m5 = $info_m4 = $info_m35 = $info_m3 = $info_m2 = $info_m1 = $info_0 =

$info_p12 = $info_p11 = $info_p10 = $info_p95 = $info_p9 = $info_p8 = $info_p7 = $info_p65 = $info_p6 = $info_p575 = $info_p55 = $info_p5 = $info_p45 = $info_p4 = $info_p35 = $info_p3 = $info_p2 = $info_p1 = '';

if ($config['time_gmtzone'] < 0)
{
	$v = str_replace(array(
	                      '-', '.'
	                 ), '', $config['time_gmtzone']);
	$tz = "m" . $v;
}
elseif ($config['time_gmtzone'] > 0)
{
	$v = str_replace(array(
	                      '+', '.'
	                 ), '', $config['time_gmtzone']);
	$tz = "p" . $v;
}
else
{
	$tz = 0;
}

$var = 'info_' . $tz;
$$var = $_selected;

$site_name = pkGetConfigF('site_name');
$site_slogan = pkGetConfigF('site_slogan');
$site_title = pkGetConfigF('site_title');
$site_title_prefix = pkGetConfigF('site_title_prefix');
$site_title_suffix = pkGetConfigF('site_title_suffix');

$site_url = pkGetConfigF('site_url');
$site_urls = pkGetConfigF('site_urls');
$site_email = pkGetConfigF('site_email');
$site_copy = pkGetConfigF('site_copy');
$site_closure = pkGetConfigF('site_closure');

$cookie_domain = pkGetConfigF('cookie_domain');
$cookie_path = pkGetConfigF('cookie_path');

$site_adview1 = pkGetConfig('site_adview') == 1 ? $_checked : '';
$site_adview0 = $site_adview1 ? '' : $_checked;

$templatename1 = pkGetConfig('templatename') == 1 ? $_checked : '';
$templatename0 = $templatename1 ? '' : $_checked;

$time_summertime1 = pkGetConfig('time_summertime') == 1 ? $_checked : '';
$time_summertime0 = $time_summertime1 ? '' : $_checked;

$captcha1 = pkGetConfig('captcha') == 1 ? $_checked : '';
$captcha0 = $captcha1 ? '' : 'checked';

$user_imgresize1 = pkGetConfig('user_imgresize') == 1 ? $_checked : '';
$user_imgresize0 = $user_imgresize1 ? '' : $_checked;

$text_imgresize1 = pkGetConfig('text_imgresize') == 1 ? $_checked : '';
$text_imgresize0 = $text_imgresize1 ? '' : $_checked;

$image_resize_width = pkGetConfig('image_resize_width');
$image_resize_width = intval($image_resize_width) > 0 ? intval($image_resize_width) : '';

$image_resize_height = pkGetConfig('image_resize_height');
$image_resize_height = intval($image_resize_height) > 0 ? intval($image_resize_height) : '';

$session_adminautolog1 = pkGetConfig('session_adminautolog') == 1 ? $_checked : '';
$session_adminautolog0 = $session_adminautolog1 ? '' : $_checked;

$vcheck1 = pkGetConfig('version_check') == 1 ? $_checked : '';
$vcheck0 = $vcheck1 ? '' : $_checked;

$lang_gdstatus = pkGetLang(@extension_loaded('gd') ? 'available' : 'not_available');
?>