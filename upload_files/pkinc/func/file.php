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


if(!defined('pkFRONTEND'))
	die('Direct access to this location is not permitted.');


#prevents public use for security reasons
if(pkFRONTEND=='setup' || pkFRONTEND=='admin') 
	{
#function pkMkDir ( string dir [, string mode ] )
#return bool result
	function pkMkDir($dir,$mode='0755')
		{
		if(substr($dir,0,strlen(pkDIRROOT))!=pkDIRROOT)
			{
			return false;
			}
		
		pkMkDir(substr($dir,0,strrpos($dir,'/')),$mode);
		
		if(pkDEVMODE)
			{
			return is_dir($dir) ? NULL : mkdir($dir,$mode);
			}
		else
			{
			return @is_dir($dir) ? NULL : @mkdir($dir,$mode);
			}
		}
#END function pkMkDir
		
	
#function pkRmDir ( string dir )
#return bool result
	function pkRmdir($dir)
		{
		if(!$dir || !is_dir($dir))
			return false;
		
		$handle=@opendir($dir);
	
		while($obj=@readdir($handle))
			{
			if($obj == '.' || $obj=='..')
				continue;
			
			if(!@is_dir($dir.'/'.$obj))
				@unlink($dir.'/'.$obj);
			else
				pkRmdir($dir.'/'.$obj);			
			}
	
		@closedir($handle);
		
		$bResult=@rmdir($dir);
		return $bResult;
		}
#END function pkRmDir
	} #END if


#function pkReadDir( string dir )	
#return array content
function pkReadDir($dir)
	{
	if(!$dir || !is_dir($dir))
		return false;

	$content=array();
	$handle=@opendir($dir);

	while($obj=@readdir($handle))
		{
		if($obj=='.' || $obj=='..')
			continue;
		
		$content[$obj]=@is_dir($dir.$obj);
		}

	@closedir($handle);

	return $content;
	}
#END function pkReadDir


#function pkFileContent ( string path [, bool as_array] ) 
#return mixed
function pkFileContent($path,$as_array=false)
	{
	$content=file($path);
	
	if($as_array)
		return $content;
	
	return implode('',$content);
	}
#END function pkFile Content


#function pkFileStream( string filepath [, bool noheader ] )
#return bool success
function pkFileStream($filepath,$noheader=false)
	{
	if(!$noheader)
		pkHeaderDownload(basename($filepath));
	
	if(!$fp=@fopen($filepath,'r'))
		return false;

	while(!@feof($fp))
		echo @fgets($fp,4096);
	@fclose($fp);

	return true;
	}
#END function pkFileStream
?>