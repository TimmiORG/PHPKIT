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


if($ACTION==$_POST['save'])
	{
	$content_epp1 = $ENV->_post_id('content_epp1'); #returns integer 0 or higher
	$content_epp1 = $content_epp1 ? $content_epp1 : 10;
	
	$content_epp2 = $ENV->_post_id('content_epp2');
	$content_epp2 = $content_epp2 ? $content_epp2 : 10;
	
	$content_epp3 = $ENV->_post_id('content_epp3');
	$content_epp3 = $content_epp3 ? $content_epp3 : 10;
			
	$content_epp4 = $ENV->_post_id('content_epp4');
	$content_epp4 = $content_epp4 ? $content_epp4 : 10;
			
	
	#set the save values
	$save_values['content_epp1'] = $content_epp1; #validated above
	$save_values['content_epp2'] = $content_epp2;
	$save_values['content_epp3'] = $content_epp3;
	$save_values['content_epp4'] = $content_epp4;
	
	$save_values['content_length1'] = $ENV->_post_id('content_length1');
	$save_values['content_length2'] = $ENV->_post_id('content_length2');
	$save_values['content_length3'] = $ENV->_post_id('content_length3');
	$save_values['content_length4'] = $ENV->_post_id('content_length4');
	
	$save_values['content_submit1'] = $ENV->_post('content_submit1');
	$save_values['content_submit2'] = $ENV->_post('content_submit2');
	$save_values['content_submit3'] = $ENV->_post('content_submit3');
	$save_values['content_submit4'] = $ENV->_post('content_submit4');
	
	$save_values['content_downloadpath']	= $ENV->_post('content_downloadpath');
	$save_values['content_downloadstatus']	= $ENV->_post('content_dlstatus');
	
	#titles
	$save_values['content_archive_title_articles']	= $ENV->_post('content_archive_title_articles');
	$save_values['content_archive_title_news']		= $ENV->_post('content_archive_title_news');
	$save_values['content_overview_title_news']		= $ENV->_post('content_overview_title_news');
	$save_values['content_archive_title_links']		= $ENV->_post('content_archive_title_links');
	$save_values['content_archive_title_downloads']	= $ENV->_post('content_archive_title_downloads');
	
	return; #dont forget this
	}

$content_epp1 = pkGetConfigF('content_epp1');
$content_epp2 = pkGetConfigF('content_epp2');
$content_epp3 = pkGetConfigF('content_epp3');
$content_epp4 = pkGetConfigF('content_epp4');

$content_length1 = pkGetConfig('content_length1');
$content_length2 = pkGetConfig('content_length2');
$content_length3 = pkGetConfig('content_length3');
$content_length4 = pkGetConfig('content_length4');

$submit1_0 = pkGetConfig('content_submit1')=='none'		? $_selected : '';
$submit1_1 = pkGetConfig('content_submit1')=='user'		? $_selected : '';
$submit1_2 = pkGetConfig('content_submit1')=='member'	? $_selected : '';
$submit1_3 = pkGetConfig('content_submit1')=='mod'		? $_selected : '';
$submit1_4 = pkGetConfig('content_submit1')=='guest'	? $_selected : '';

$submit2_0 = pkGetConfig('content_submit2')=='none'		? $_selected : '';
$submit2_1 = pkGetConfig('content_submit2')=='user'		? $_selected : '';
$submit2_2 = pkGetConfig('content_submit2')=='member'	? $_selected : '';
$submit2_3 = pkGetConfig('content_submit2')=='mod'		? $_selected : '';
$submit2_4 = pkGetConfig('content_submit2')=='guest'	? $_selected : '';

$submit3_0 = pkGetConfig('content_submit3')=='none'		? $_selected : '';
$submit3_1 = pkGetConfig('content_submit3')=='user'		? $_selected : '';
$submit3_2 = pkGetConfig('content_submit3')=='member'	? $_selected : '';
$submit3_3 = pkGetConfig('content_submit3')=='mod'		? $_selected : '';
$submit3_4 = pkGetConfig('content_submit3')=='guest'	? $_selected : '';

$submit4_0 = pkGetConfig('content_submit4')=='none'		? $_selected : '';
$submit4_1 = pkGetConfig('content_submit4')=='user'		? $_selected : '';
$submit4_2 = pkGetConfig('content_submit4')=='member'	? $_selected : '';
$submit4_3 = pkGetConfig('content_submit4')=='mod'		? $_selected : '';
$submit4_4 = pkGetConfig('content_submit4')=='guest'	? $_selected : '';

$dlstatus4_0 = pkGetConfig('content_downloadstatus')=='guest'	? $_selected : '';
$dlstatus4_1 = pkGetConfig('content_downloadstatus')=='user'	? $_selected : '';
$dlstatus4_2 = pkGetConfig('content_downloadstatus')=='member'	? $_selected : '';
$dlstatus4_3 = pkGetConfig('content_downloadstatus')=='mod'		? $_selected : '';
$dlstatus4_4 = pkGetConfig('content_downloadstatus')=='admin'	? $_selected : '';
$dlstatus4_5 = pkGetConfig('content_downloadstatus')=='none'	? $_selected : '';

$content_downloadpath = pkGetConfigF('content_downloadpath');

#title
$content_archive_title_articles		= pkGetConfigF('content_archive_title_articles');
$content_archive_title_news			= pkGetConfigF('content_archive_title_news');
$content_overview_title_news		= pkGetConfigF('content_overview_title_news');
$content_archive_title_links		= pkGetConfigF('content_archive_title_links');
$content_archive_title_downloads	= pkGetConfigF('content_archive_title_downloads');

?>