<?php
# PHPKIT Web Content Management System
# --------------------------------------------
# Copyright (c) 2002-2007 Gersöne & Schott GbR
#
# This file / the PHPKIT-software is no freeware!
# For further informations please vistit our website
# or contact us via email:
#
# Diese Datei / die PHPKIT-Software ist keine Freeware!
# Für weitere Information besuchen Sie bitte unsere 
# Webseite oder kontaktieren uns per E-Mail:
#
# Website : http://www.phpkit.de
# Mail    : info@phpkit.de
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATIONS
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


$form_action=pkLink('contact','suggest');
$form_action_add=pkFormActionGet($form_action);

$suggest_path=$ENV->_isset_get('suggest_path') ? urldecode($ENV->_get('suggest_path')) : $ENV->getvar('QUERY_STRING');
$suggest_path=urlencode($suggest_path);


$lang_receiver=pkGetLang('receiver');
$lang_email_address=pkGetLang('email_address');
$lang_bl_go=pkGetLang('bl_go');

eval("\$suggest=\"".pkTpl("navigation/suggest")."\";"); 
return array($suggest);
?>
