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


return array(
	pkHtmlLink(pkLink('start'),'Startseite'			,'','pknidmain0','pkcontent_a_'.$navalign),
	pkHtmlLink(pkLink('news'),'News'				,'','pknidmain1','pkcontent_a_'.$navalign),
	pkHtmlLink(pkLink('article'),'Artikel'			,'','pknidmain2','pkcontent_a_'.$navalign),
	pkHtmlLink(pkLink('link'),'Links'				,'','pknidmain3','pkcontent_a_'.$navalign),
	pkHtmlLink(pkLink('download'),'Downloads'		,'','pknidmain4','pkcontent_a_'.$navalign),
	pkHtmlLink(pkLink('faq'),'FAQ'					,'','pknidmain5','pkcontent_a_'.$navalign),
	pkHtmlLink(pkLink('forumsdisplay'),'Forum'		,'','pknidmain6','pkcontent_a_'.$navalign),
	pkHtmlLink(pkLink('guestbook'),'G&auml;stebuch'	,'','pknidmain7','pkcontent_a_'.$navalign),
	pkHtmlLink(pkLink('contact'),'Kontakt'			,'','pknidmain8','pkcontent_a_'.$navalign)
	);
?>