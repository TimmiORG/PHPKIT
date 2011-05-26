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


pkLoadClass($BBCODE,'bbcode');

if(pkGetConfig('welcome_eod'))
	{
	$welcome_text=$BBCODE->parse($config['welcome_text'],1,1,1,1);
	
	if(!empty($config['welcome_title'])) 
		{
	  	$welcome_title=$BBCODE->parse($config['welcome_title'],1,1,1,1); 
  		eval ("\$welcome_title= \"".pkTpl("welcome_title")."\";");
	  	}
 	
	if(!empty($welcome_text) || !empty($welcome_title))
		eval("\$site_body.= \"".pkTpl("welcome")."\";");
	}


if(empty($config['site_frontpage'])) 
	{
	return;
	}


$f = explode("\n",$config['site_frontpage']);
 	
foreach($f as $fp) 
	{
	$frontpage=trim(basename($fp));
	
	if(empty($frontpage) || $frontpage=='start')
		continue;

	if(strstr($fp,'&') || strstr($fp,'?')) 
		{
		parse_str($fp); 
		$fp=explode('&',$fp);
		$frontpage=$fp[0];
		}
	
	if(pkFileCheck(pkDIRPUBLIC.$frontpage.pkEXT)) 
		{
		include(pkDIRPUBLIC.$frontpage.pkEXT);
		}
	elseif(pkFileCheck($frontpage))
		{			
		$site_body.=implode('',file($frontpage));
		}
	else 
		{
		continue;
		}
	
	
	eval("\$site_body.=\"".pkTpl("frontpage_spacer")."\";");
	}


#page title - set at this point to overwrite the included elements
$CMS->site_title_set(pkGetConfig('site_frontpage_title'));
?>