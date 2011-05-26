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

if(!adminaccess('config')) 
	return pkEvent('access_forbidden');


$ACTION = isset($_POST['action']) ? $_POST['action'] : 'view'; #unsave variable


if($ACTION==$_POST['save'])
	{
	$SQL->query("REPLACE INTO ".pkSQLTAB_CONFIG." (id,value) VALUES ('licencekey','".$SQL->f(serialize($ENV->_post('licencekey')))."')");
	
	pkHeaderLocation('licencekey');
	}


#language file
pkLoadLang('adminconfig');

$licencekey = pkGetConfigF('licencekey');

$lang_licencekey_explain	= pkGetLang(pkl(1)||pkl(2) ? 'licencekey_explain_thank'.pkL : 'licencekey_explain_nokey');
$lang_licencekey_desc		= pkGetLang(pkl(1)||pkl(2) ? 'licencekey_valid' : 'licencekey_invalid');

$form_action = pkLink('licencekey');
$mode_title = pkGetLang('licencekey');

eval("\$site_body.= \"".pkTpl("config_licencekey")."\";");
?>