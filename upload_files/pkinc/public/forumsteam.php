<?php
# PHPKIT Web Content Management System
# --------------------------------------------
# Copyright (c) 2002-2007 Gers�ne & Schott GbR
#
# This file / the PHPKIT-software is no freeware!
# For further informations please vistit our website
# or contact us via email:
#
# Diese Datei / die PHPKIT-Software ist keine Freeware!
# F�r weitere Information besuchen Sie bitte unsere 
# Webseite oder kontaktieren uns per E-Mail:
#
# Website : http://www.phpkit.de
# Mail    : info@phpkit.de
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATIONS
#
# SIE SIND NICHT BERECHTIGT, UNRECHTM�SSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


$dir="forum/";

include(pkDIRPUBLICINC.'forumsheader'.pkEXT);
include(pkDIRPUBLIC.'team'.pkEXT);
include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
?>