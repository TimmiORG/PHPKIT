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


if(!adminaccess('stats'))
	return pkEvent('access_forbidden');


$epp=20;
$maxwidth=65;
$width1=50;
$width2=-15;
$phpkit_status=phpkitstatus();

$t_date=formattime(time(),'','date');
$y_date=formattime(time()-3600*24,'','date');


$counter_t=$SQL->fetch_assoc($SQL->query("SELECT
	calender_counter, 
	calender_id, 
	calender_picount 
	FROM ".pkSQLTAB_CALENDAR." 
	ORDER by calender_id DESC LIMIT 1"));

$counter_y=$SQL->fetch_assoc($SQL->query("SELECT 
	calender_counter,
	calender_picount
	FROM ".pkSQLTAB_CALENDAR."
	WHERE calender_id<'".$counter_t['calender_id']."'
	ORDER BY calender_id DESC LIMIT 1"));
	

$counter_t['calender_counter']=$counter_t['calender_counter'] ? $counter_t['calender_counter'] : 0;
$counter_t['calender_picount']=$counter_t['calender_picount'] ? $counter_t['calender_picount'] : 0;
$counter_y['calender_counter']=$counter_y['calender_counter'] ? $counter_y['calender_counter'] : 0;
$counter_y['calender_picount']=$counter_y['calender_picount'] ? $counter_y['calender_picount'] : 0;


if(is_array($phpkit_status['online_user']))
	{
	foreach($phpkit_status['online_user'] as $userinfo)
		{
		$row=rowcolor($row);
		$user_lasturl=pkRemoveSessionId($userinfo['user_lasturl']);

		
		if(strlen($user_lasturl)>$maxwidth) 
			$url_linktext=substr($user_lasturl,0,$width1)."...".substr($user_lasturl,$width2);
		else
			$url_linktext=$user_lasturl;
		
		$userinfo_time=formattime($userinfo['logtime'],0,'time_full');
		$user_lasturl=pkEntities($user_lasturl);		
		$url_linktext=pkEntities($url_linktext);		
#		$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
		
		eval("\$record_useronline_row.= \"".pkTpl("record_useronline_row")."\";");
		}
	
	unset($row);
	}


if(is_array($phpkit_status['guests_hash']))
	{
	foreach($phpkit_status['guests_hash'] as $userinfo)
		{
		$row=rowcolor($row);
		$user_lasturl=pkRemoveSessionId($userinfo['session_url']);

		
		if(strlen($user_lasturl)>$maxwidth)
			$url_linktext=substr($user_lasturl,0,$width1)."...".substr($user_lasturl,$width2);
		else
			$url_linktext=$user_lasturl;
		
		$userinfo_time=formattime($userinfo['logtime'],0,'time_full');
		$user_lasturl=pkEntities($user_lasturl);		
		$url_linktext=pkEntities($url_linktext);
		
		eval("\$record_guestonline_row.= \"".pkTpl("record_guestonline_row")."\";");
		}
	}

eval ("\$site_body.= \"".pkTpl("record")."\";");
?>