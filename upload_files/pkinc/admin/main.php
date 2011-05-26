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


if(!defined('pkFRONTEND') || pkFRONTEND!='admin')
	die('Direct access to this location is not permitted.');


if(!adminaccess('adminarea'))
	return pkEvent('access_forbidden');


$newest_user = '';
$newest_content = '';
$main_infooffline = '';

#global stats
$phpkit_status = phpkitstatus();

$visitors_today 	= pkNumberFormat($phpkit_status['counter_today']);
$picount_today	 	= pkNumberFormat($phpkit_status['picount_today']);
$visitors_yesterday = pkNumberFormat($phpkit_status['counter_yesterday']);
$picount_yesterday	= pkNumberFormat($phpkit_status['picount_yesterday']);
$visitors_total		= pkNumberFormat($phpkit_status['counter_total']);
$picount_total		= pkNumberFormat($phpkit_status['picount_total']);

list($stats_since) = $SQL->fetch_row($SQL->query("SELECT MIN(calender_date) FROM ".pkSQLTAB_CALENDAR));
$stats_since = pkTimeFormat($stats_since,'date');


#user related
list($notused) 		= $SQL->fetch_row($SQL->query("SELECT COUNT(logtime) FROM ".pkSQLTAB_USER." WHERE logtime=0"));
list($todelete) 	= $SQL->fetch_row($SQL->query("SELECT COUNT(user_activate) FROM ".pkSQLTAB_USER." WHERE user_activate=2"));
list($toactivate)	= $SQL->fetch_row($SQL->query("SELECT COUNT(user_activate) FROM ".pkSQLTAB_USER." WHERE user_activate=0"));

	
#new users
$row = '';

$query = $SQL->query("SELECT user_id, user_name, signin, logtime FROM ".pkSQLTAB_USER." ORDER BY user_id DESC LIMIT 10");
while($userinfo = $SQL->fetch_assoc($query))
	{
	$sigintime	= formattime($userinfo['signin']);
	$user_name	= pkEntities($userinfo['user_name']);
	$user_name	= $userinfo['logtime'] ? $user_name : '<span class="highlight">'.$user_name.'</span>';

	$row = rowcolor($row);
	
	eval("\$newest_user.= \"".pkTpl("main_newestuser")."\";");
	}



#content related
$user_lastlog = pkGetUservalue('lastlog');

list($submited_content_count) = $SQL->fetch_row($SQL->query("SELECT COUNT(content_submited_id) FROM ".pkSQLTAB_CONTENT_SUBMIT));
list($comment_count) = $SQL->fetch_row($SQL->query("SELECT COUNT(comment_id) FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='cont' AND comment_time>".intval($user_lastlog).""));

#new contents		
$row = '';
$hash = array(0=>'content',1=>'article',2=>'news',3=>'link',4=>'download');#for language output

$query = $SQL->query("SELECT content_id, content_title, content_option, content_time FROM ".pkSQLTAB_CONTENT." ORDER BY content_id DESC LIMIT 10");
while($contentinfo = $SQL->fetch_assoc($query)) 
	{
	$type = isset($hash[$contentinfo['content_option']]) ? $hash[$contentinfo['content_option']] : $hash[0];
	$type = pkGetLang($type);

	$contenttime	= pkTimeFormat($contentinfo['content_time']);			
	$contenttitle	= pkEntities(pkStringCut($contentinfo['content_title'],25));
	$contenttitle	= empty($contenttitle) ? pkGetLang('no_title_formated') : $contenttitle;

	$row = rowcolor($row);
	
	eval("\$newest_content.= \"".pkTpl("main_newestcontent")."\";");
	}
	

#maintenance message		
if(!pkGetConfig('site_eod'))
	{ 
	eval("\$main_infooffline= \"".pkTpl("main_offline_warning")."\";");
	}

#version
$phpkitversion	= pkGetLang('version').' '.pkPHPKIT_VERSION.' (Build: '.pkPHPKIT_BUILD.')';
$link_copyright	= 'http://www.phpkit.com';
$lang_copyright	= pkGetLang('PHPKIT_copyright');


eval("\$site_body.= \"".pkTpl("main")."\";");
?>