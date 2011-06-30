<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTM�SSIGE KOPIEN DIESER
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


if(defined('pkDIRROOT') || (defined('pkREQUESTEDFILE') && pkREQUESTEDFILE!=basename(__FILE__) && pkFRONTEND!='setup'))
	die('Recursive includes are not permitted!');


if(!defined('pkFRONTEND'))
	{
	$fx=isset($_GET['fx']) ? $_GET['fx'] : NULL;
	
	switch($fx)
		{
		case 'captcha' :
		case 'rsimg' :
		case 'style' :
			define('pkFRONTEND',$fx);
			break;
		default: 
			define('pkFRONTEND','public');
		}
				
	define('pkREQUESTEDFILE','include.php');	
	}


define('pkDIRROOT',dirname(__FILE__).'/');			#root-directory for internal use (f.e. include)
define('pkSITE','include');

require_once(pkDIRROOT.'pkinc/main.php');
?>