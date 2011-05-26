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


$align = '';
$content = '';
pkLoadClass($BBCODE,'bbcode');


$query = $SQL->query("SELECT 
		c.content_id,
		c.content_title, 
		c.content_header,
		c.content_text,
		c.content_time 
	FROM ".pkSQLTAB_CONTENT." AS c
		LEFT JOIN ".pkSQLTAB_CONTENT_CATEGORY." AS cc ON (cc.contentcat_id=c.content_cat)
	WHERE c.content_option='2' AND 
		c.content_status='1' AND 
		(c.content_expire>'".pkTIME."' OR c.content_expire=0) AND 
		c.content_time<'".pkTIME."' AND 
		".sqlrights("cc.contentcat_rights")."
	ORDER by c.content_time DESC 
	LIMIT 2");
while($contentinfo = $SQL->fetch_assoc($query))
	{
	$content_title	= pkEntities($contentinfo['content_title']);
	$content_time	= pkTimeFormat($contentinfo['content_time']);
	$link			= pkLink('news','','contentid='.$contentinfo['content_id']);
	
	$headline = $BBCODE->parse($contentinfo['content_header']." ".$contentinfo['content_text'],1,1,1);
	$headline = strip_tags($headline);
	$headline = mb_substr($headline,0,$config['nb_newarticle_cur'],pkGetLang('__CHARSET__'));


	$align = $align=='left' ? 'right' : 'left';
	
	eval("\$content.=\"".pkTpl("newsblock_".$align)."\";");
	
	unset($content_title,$headline,$content_time,$link);
	}

eval("\$site_body.=\"".pkTpl("newsblock")."\";");
?>