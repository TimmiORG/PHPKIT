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


$boxlinks = array();
$list = '';

$toselect = pkGetConfig('user_design') && pkGetUservalue('design') ? pkGetUservalue('design') : pkGetConfig('site_style');

$query = $SQL->query("SELECT 
		style_id,
		style_name,
		style_user 
	FROM ".pkSQLTAB_STYLE."
	WHERE ".(pkGetConfig('user_design') ? "style_user=1" : "style_id=".$SQL->i(pkGetConfig('site_style')))."
	ORDER BY style_name");
while(list($id,$name,$user) = $SQL->fetch_row($query))
	{
	$list.='<option value="'.$id.'"'.($id==$toselect ? ' selected="selected"' : '').'>'.pkEntities($name).'</option>';
	}


$lang_bl_go	= pkGetLang('bl_go');
$form_action = pkLink('','','','',pkEntities($ENV->getvar('QUERY_STRING')));
$form_additional_fields = pkFormActionGet($form_action);

eval("\$boxlinks[]=\"".pkTpl("navigation/style")."\";");

return $boxlinks;
?>