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


list($id,$title,$text)=$SQL->fetch_row($SQL->query("SELECT 
		".pkSQLTAB_CONTENT.".content_id,
		".pkSQLTAB_CONTENT.".content_title,
		".pkSQLTAB_CONTENT.".content_text 
	FROM ".pkSQLTAB_CONTENT." 
		LEFT JOIN ".pkSQLTAB_CONTENT_CATEGORY." ON ".pkSQLTAB_CONTENT_CATEGORY.".contentcat_id=".pkSQLTAB_CONTENT.".content_cat 
	WHERE ".pkSQLTAB_CONTENT.".content_option='1' AND 
		".pkSQLTAB_CONTENT.".content_status='1' AND 
		(".pkSQLTAB_CONTENT.".content_expire>'".pkTIME."' OR 
			 ".pkSQLTAB_CONTENT.".content_expire=0) AND 
		".pkSQLTAB_CONTENT.".content_time<'".pkTIME."' AND 
		".sqlrights("".pkSQLTAB_CONTENT_CATEGORY.".contentcat_rights")."
	ORDER by ".pkSQLTAB_CONTENT.".content_time DESC 
	LIMIT 1"));


pkLoadClass($BBCODE,'bbcode');

$title = pkEntities($title);
$text = $BBCODE->parse($text,1,1,1);
$text = strip_tags($text);
$text = mb_substr($text,0,$config['nb_newarticle_cur'],pkGetLang('__CHARSET__'));

$link=pkLink('article','','contentid='.$id);

$lang_read_more=pkGetLang('read_more');
$lang_read_more_link=pkGetLang('read_more_link');

$class='pkcontent_block_newarticle_'.$navalign;

eval("\$newarticle=\"".pkTpl("navigation/content")."\";");

return array($newarticle);
?>