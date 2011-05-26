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


if(defined('pkFRONTEND'))
	die('Recursive includes are not permitted!');

define('pkFRONTEND','setup');
define('pkREQUESTEDFILE',basename(__FILE__));

require_once(dirname(__FILE__).'/include.php');

define('pkDIRSETUP',pkDIRINC.'setup/');
define('pkDIRSETUPTPL',pkDIRINC.'setup/tpl/');

require_once(pkDIRSETUP.'setup'.pkEXT);

$SETUP = new pkSetup;
?>