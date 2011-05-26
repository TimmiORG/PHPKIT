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


$contentid=(isset($_REQUEST['contentid']) && intval($_REQUEST['contentid'])>0) ? intval($_REQUEST['contentid']) : 0;

if(!$contentid)
	pkHeaderLocation('contentarchive','','type=3');

if(!isset($_REQUEST['link']) || $_REQUEST['link']!='go')
	pkHeaderLocation('contentarchive','','type=3&contentid='.$contentid);


$contentinfo=$SQL->fetch_array($SQL->query("SELECT 
	*
	FROM ".pkSQLTAB_CONTENT."
	WHERE content_id='".$contentid."'
	LIMIT 1"));
	

if($contentinfo['content_status']!=1  || $contentinfo['content_time']>pkTIME)
	{
	pkEvent('article_not_available');
	return;
	}	
		
$SQL->query("UPDATE ".pkSQLTAB_CONTENT." 
	SET content_views=content_views+1
	WHERE content_id='".$contentid."'");


$moveto=pkEntities($contentinfo['content_altdat']);
$refreshtime=6;

		
eval("\$site_refresh= \"".pkTpl("site_refresh")."\";");
eval("\$site_body.= \"".pkTpl("content/links")."\";");
?>