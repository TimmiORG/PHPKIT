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
# licence   : http://www.phpkit.com/licence/phpkit
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


unset($jumpto);
if(intval($_REQUEST['next'])>0 || intval($_REQUEST['prev'])>0)
	{
	if(intval($_REQUEST['prev'])>0)
		{
		$order='DESC';
		$where="forumthread_id<'".intval($_REQUEST['prev'])."'";
		}
	else
		{
		$order='ASC';
		$where="forumthread_id>'".intval($_REQUEST['next'])."'";
		}
	
	$linkid=$SQL->fetch_array($SQL->query("SELECT ".pkSQLTAB_FORUM_THREAD.".forumthread_id FROM ".pkSQLTAB_FORUM_THREAD." LEFT JOIN ".pkSQLTAB_FORUM_CATEGORY." ON ".pkSQLTAB_FORUM_CATEGORY.".forumcat_id=".pkSQLTAB_FORUM_THREAD.".forumthread_catid WHERE ".pkSQLTAB_FORUM_THREAD.". ".$where." AND (".sqlrights(pkSQLTAB_FORUM_CATEGORY.".forumcat_rrights")." OR ".pkSQLTAB_FORUM_CATEGORY.".forumcat_mods LIKE '%-".$SQL->i(pkGetUservalue('id'))."-%' OR ".pkSQLTAB_FORUM_CATEGORY.".forumcat_user LIKE '%-".$SQL->i(pkGetUservalue('id'))."-%') ORDER by ".pkSQLTAB_FORUM_THREAD.".forumthread_id ".$order." LIMIT 1"));

	if($linkid['forumthread_id']>0)
		$jumpto="include.php?path=forumsthread&threadid=".$linkid['forumthread_id'];
	elseif(intval($_REQUEST['next'])>0)
		$id=intval($_REQUEST['next']);
	elseif(intval($_REQUEST['prev'])>0)
		$id=intval($_REQUEST['prev']);
	}
elseif($_REQUEST['jumpid']=='search')
	$jumpto="include.php?path=forumsearch";
elseif($_REQUEST['jumpid']=='newthread')
	$jumpto="include.php?path=forumsnewpost";
elseif($_REQUEST['jumpid']=='main')
	$jumpto="include.php?path=forumsdisplay";
elseif(intval($_REQUEST['jumpid'])>0)
	$jumpto="include.php?path=forumscategory&catid=".intval($_REQUEST['jumpid']);
elseif(isset($_GET['remove_path']))
	$jumpto="include.php?".urldecode($_GET['remove_path']);
elseif(isset($_REQUEST['remove_path']))
	$jumpto="include.php?".$_REQUEST['remove_path'];
else
	$jumpto="include.php?path=forumsdisplay";

if($jumpto) 
	{
	header("location: ".$jumpto);
	exit();
	}


include(pkDIRPUBLICINC.'forumsheader'.pkEXT);

if(intval($_REQUEST['next'])>0)
	eval("\$site_body.= \"".pkTpl("forum/jump_next")."\";");
elseif(intval($_REQUEST['prev'])>0)
	eval("\$site_body.= \"".pkTpl("forum/jump_prev")."\";");

include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
?>