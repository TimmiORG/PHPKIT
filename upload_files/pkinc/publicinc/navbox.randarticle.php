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


pkLoadClass($BBCODE,'bbcode');


$query = $SQL->query("SELECT 
		c.content_id,
		c.content_title,
		c.content_text 
	FROM ".pkSQLTAB_CONTENT." AS c
		LEFT JOIN ".pkSQLTAB_CONTENT_CATEGORY." AS cc ON cc.contentcat_id=c.content_cat 
	WHERE c.content_option='1' AND 
		c.content_status='1' AND 
		(c.content_expire>'".pkTIME."' OR c.content_expire=0) AND 
		c.content_time<'".pkTIME."' AND 
		".sqlrights('cc.contentcat_rights')."
	ORDER BY RAND()
	LIMIT 1");

list($id,$title,$text) = $SQL->fetch_row($query);

if(!$id)
	{
	return array();
	}

$title = pkEntities($title);
$text = $BBCODE->parse($text,1,1,1,1,1);
$text = strip_tags($text);
$text = mb_substr($text,0,$config['nb_randarticle_cur'],pkGetLang('__CHARSET__'));


$link=pkLink('article','','contentid='.$id);

$lang_read_more=pkGetLang('read_more');
$lang_read_more_link=pkGetLang('read_more_link');

$class='pkcontent_block_randarticle_'.$navalign;	

eval("\$randarticle=\"".pkTpl("navigation/content")."\";");

return array($randarticle);
?>