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


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


include(pkDIRPUBLICINC.'forumsheader'.pkEXT);


$epp=20;
$entries=(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;


$usercount=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_USER." WHERE user_posts>'0' AND user_activate=1 AND user_status!='ban'"));
$sidelink=sidelinkfull($usercount[0],$epp,$entries,"include.php?path=forumstopuser","sitebodysmall");

$getuserinfo=$SQL->query("SELECT user_id, user_nick, user_posts, user_postdelay, user_email, user_emailshow, user_status, user_ghost FROM ".pkSQLTAB_USER." WHERE user_posts>'0' AND user_activate=1 AND user_status!='ban' ORDER by user_posts+user_postdelay DESC LIMIT ".$entries.",".$epp);
while($userinfo=$SQL->fetch_array($getuserinfo))
	{
	$row=rowcolor($row);
	$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
	
	if($userinfo['user_status']=="admin")
		$userinfo_status=$lang['admin'];
	elseif($userinfo['user_status']=="mod")
		$userinfo_status=$lang['mod'];
	elseif($userinfo['user_status']=="member")
		$userinfo_status=$lang['member'];
	else
		$userinfo_status=$lang['user'];
	
	if(isonline($userinfo['user_id']))
		eval("\$info_os= \"".pkTpl("member_os_online")."\";");
	else
		eval ("\$info_os= \"".pkTpl("member_os_offline")."\";");
	
	$userinfo_rank=postcount($userinfo['user_posts'], $userinfo['user_postdelay'],1);
	$userinfo_posts=$userinfo['user_posts']+$userinfo['user_postdelay'];
	
	if($userinfo_posts<0)
		$userinfo_posts=0;
	
	eval("\$topuser_row.= \"".pkTpl("forum/topuser_row")."\";");
	
	unset($userinfo_email);
	}

eval("\$site_body.= \"".pkTpl("forum/topuser")."\";");

include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
?>