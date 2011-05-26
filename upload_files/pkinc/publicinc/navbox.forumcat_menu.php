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


include_once(pkDIRCLASS.'forum'.pkEXT);

$FORUM = new pkForum;

$cat_hash = $FORUM->gettree();
if(!is_array($cat_hash))
	return;

$boxlinks = array();
$list='';

foreach($cat_hash as $catinfo)
	{
	$list.='<option value="'.$catinfo['forumcat_id'].'">'.str_repeat('-',$catinfo['level']).pkEntities($catinfo['forumcat_name']).'</option>';
	}

$current_path=pkEntities($ENV->getvar('QUERY_STRING'));
$form_action=pklink('forumsjump');

$lang_forumsdisplay=pkGetLang('forumsdisplay');
$lang_search=pkGetLang('search');
$lang_new_thread=pkGetLang('new_thread');
$lang_select_target_forum=pkGetLang('select_target_forum');
$lang_bl_go=pkGetLang('bl_go');

eval("\$boxlinks[]= \"".pkTpl("navigation/forumcat_menu")."\";");

return $boxlinks;
?>