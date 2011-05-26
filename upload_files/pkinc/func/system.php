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


if(defined('pkSYSTEMREQUIREDPHPVERSION'))
	return;

define('pkSYSTEMREQUIREDPHPVERSION','5.2.0');
define('pkSYSTEMREQUIREDSAFEMODE',false);
define('pkSYSTEMREQUIREDFILEUPLOADS',true);


function pkSystemcheckPhpversion()
	{
	return str_replace('.','',phpversion()) > str_replace('.','',pkSYSTEMREQUIREDPHPVERSION);
	}
	
function pkSystemcheckSafemode()
	{
	return ini_get('safe_mode') == pkSYSTEMREQUIREDSAFEMODE;
	}
	

function pkSystemcheckFileuploads()
	{
	return ini_get('file_uploads') == pkSYSTEMREQUIREDFILEUPLOADS;
	}
	
function pkSystemWriteableDirectories()
	{
	return pkCfgData('writedirs');
	}

function pkSystemSqltables()
	{
	return pkCfgData('sqltables');
	}

function pkSystemcheckExtension($extension)
	{
	if($extension=='gd')
		return extension_loaded('gd');
	}
?>